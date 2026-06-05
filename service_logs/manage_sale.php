<?php
session_start();

// 1. ກວດສອບການ Login
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<script>alert('ກະລຸນາລ໋ອກອິນກ່ອນ');location='index.php';</script>";
    exit();
}

require_once("../cennect_dbstock.php");
if (!isset($connect)) { die("Error: ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນ."); }
mysqli_set_charset($connect, "utf8");


// 🔄 Action: ເປີດບິນໃໝ່ (ລ້າງ Session ເກົ່າອອກ ເພື່ອໃຫ້ລະບົບສ້າງບິນໃໝ່)
if (isset($_GET['action']) && $_GET['action'] == 'new_bill') {
    unset($_SESSION['current_sale_service_id']);
    header("Location: manage_sale.php");
    exit();
}


// 🌟 ກວດສອບ Session ບິນຂາຍ, ຖ້າບໍ່ມີໃຫ້ສ້າງອັດຕະໂນມັດໃນ service_logs (ຄ່າແຮງເປັນ 0)
if (!isset($_SESSION['current_sale_service_id']) || intval($_SESSION['current_sale_service_id']) <= 0) {
    // ບັນທຶກເຂົ້າ service_logs ໂດຍກົງ ແລະ ໃຫ້ labor_cost = 0
    $create_bill = mysqli_query($connect, "INSERT INTO service_logs (status, labor_cost) VALUES ('pending', 0)");
    if ($create_bill) {
        $_SESSION['current_sale_service_id'] = mysqli_insert_id($connect); // ເກັບ ID ໄວ້ໃນ Session
    } else {
        die("Error: ບໍ່ສາມາດສ້າງບິນຂາຍໃນ service_logs ໄດ້: " . mysqli_error($connect));
    }
}

// ເອົາ ID ບິນຈາກ Session ມາແທນທີ່ $service_id
$service_id = $_SESSION['current_sale_service_id'];


// 🛠️ API ສຳລັບ AJAX ປິດບິນຂາຍ
if (isset($_GET['action']) && $_GET['action'] == 'update_status_sale') {
    $sql_update = "UPDATE service_logs SET status = 'success', completed_at = NOW() WHERE log_id = $service_id";
    if (mysqli_query($connect, $sql_update)) {
        unset($_SESSION['current_sale_service_id']); // ປິດບິນສຳເລັດແລ້ວ ໃຫ້ລ້າງ Session ບິນນີ້ອອກ
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($connect)]);
    }
    exit();
}


// 1. Logic ບັນທຶກລາຍການອະໄຫຼ່ເຂົ້າ service_details
if (isset($_POST['btn_save_item'])) {
    $part_val = intval($_POST['part_id']);
    $qty = intval($_POST['qty']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $total = $qty * $price;

    $can_save = true;
    if ($part_val > 0) {
        $check = mysqli_query($connect, "SELECT qty_stock FROM parts_profile WHERE part_id = $part_val");
        $row = mysqli_fetch_array($check);
        if (!$row || $row['qty_stock'] < $qty) {
            echo "<script>alert('ສະຕັອກສິນຄ້າບໍ່ພໍ!'); window.location='manage_sale.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('ກະລຸນາເລືອກສິນຄ້າຈາກສະຕັອກ!'); window.location='manage_sale.php';</script>";
        exit();
    }

    if ($can_save) {
        $sql = "INSERT INTO service_details (service_id, part_id, description, qty, price, total) 
                VALUES ($service_id, $part_val, '$description', $qty, $price, $total)";
        if (mysqli_query($connect, $sql)) {
            // ຕັດສະຕັອກອັດຕະໂນມັດ
            mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock - $qty WHERE part_id = $part_val");
            header("Location: manage_sale.php");
            exit();
        } else {
            echo "<script>alert('Error: " . mysqli_real_escape_string($connect, mysqli_error($connect)) . "');</script>";
        }
    }
}


// 2. Logic ຍົກເລີກລາຍການ (ຄືນສະຕັອກ)
if (isset($_GET['action']) && $_GET['action'] == 'delete_sale_item') {
    $del_id = intval($_GET['del_id']);
    
    $item_check = mysqli_query($connect, "SELECT part_id, qty FROM service_details WHERE detail_id = $del_id AND service_id = $service_id");
    if (mysqli_num_rows($item_check) > 0) {
        $item = mysqli_fetch_array($item_check);
        $p_id = $item['part_id'];
        $p_qty = $item['qty'];
        
        if ($p_id > 0) {
            mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock + $p_qty WHERE part_id = $p_id");
        }
        mysqli_query($connect, "DELETE FROM service_details WHERE detail_id = $del_id");
    }
    header("Location: manage_sale.php");
    exit();
}


// 3. ດຶງຂໍ້ມູນມາສະແດງຜົນ
$res_total = mysqli_query($connect, "SELECT SUM(total) as sum_grand FROM service_details WHERE service_id = $service_id");
$total_data = mysqli_fetch_array($res_total);
$grand_total = $total_data['sum_grand'] ?? 0;

$res_log = mysqli_query($connect, "SELECT status FROM service_logs WHERE log_id = $service_id");
$log_data = mysqli_fetch_array($res_log);
$current_status = $log_data['status'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ໜ້າບິນຂາຍອະໄຫຼ່ (ຕາຕະລາງຫຼັກ)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; color: #333; }
        .card-custom { background: #fff; border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); }
        .table thead th { font-weight: 600; background-color: #f1f4f8; color: #495057; border-bottom: 2px solid #e9ecef; }
        .table tbody td { vertical-align: middle; }
        .btn { font-weight: 500; border-radius: 8px; }
        .summary-box { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); border-radius: 12px; padding: 20px; color: white; }
        @media print { body * { display: none !important; } #printFrame, #printFrame * { display: block !important; width: 100% !important; } }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center mb-1">
                <a href="form_service_logs.php" class="btn btn-light btn-sm me-3 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="ກັບຄືນ">
                    <i class="fas fa-arrow-left text-secondary"></i>
                </a>
                <h3 class="fw-bold text-dark mb-0"><i class="fas fa-shopping-cart text-primary me-2"></i> ໜ້າຂາຍອະໄຫຼ່ໜ້າຮ້ານ (POS)</h3>
            </div>
            <div class="d-flex align-items-center gap-3 ms-5">
                <p class="text-muted mb-0">ເລກທີບິນຂາຍ: <span class="fw-bold text-primary">#<?php echo str_pad($service_id, 5, "0", STR_PAD_LEFT); ?></span></p>
                <?php if($current_status == 'pending'): ?>
                    <span class="badge bg-warning text-dark rounded-pill px-3">ກຳລັງຂາຍ</span>
                <?php else: ?>
                    <span class="badge bg-success rounded-pill px-3">ສຳເລັດແລ້ວ</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="manage_sale.php?action=new_bill" onclick="return confirm('ຕ້ອງການເປີດບິນໃໝ່ແທ້ບໍ່?')" class="btn btn-outline-primary d-flex align-items-center gap-2 px-3 shadow-sm">
                <i class="fas fa-file-invoice"></i> ເປີດບິນໃໝ່
            </a>
            <button type="button" onclick="printSaleInvoice()" class="btn btn-success d-flex align-items-center gap-2 px-4 shadow-sm" <?php echo $grand_total == 0 ? 'disabled' : ''; ?>>
                <i class="fas fa-money-bill-wave"></i> ຊຳລະ微ັນ & ພິມບິນ
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card-custom p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-plus-circle text-primary me-2"></i> ເenvironmentລືອກລາຍການອະໄຫຼ່</h5>
                <form method="POST" action="manage_sale.php">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">ເລືອກອະໄຫຼ່ໃນສະຕັອກ</label>
                        <select name="part_id" id="part_select" class="form-select bg-light py-2" onchange="updateSalePrice()" required>
                            <option value="">-- ກະລຸນາເລືອກລາຍການ --</option>
                            <?php 
                            $res = mysqli_query($connect, "SELECT * FROM parts_profile WHERE qty_stock > 0"); 
                            while($p = mysqli_fetch_array($res)) { 
                                echo "<option value='".$p['part_id']."' data-price='".$p['sale_price']."' data-name='".$p['part_name']."'>".$p['part_name']." (ພ້ອມຂາຍ: ".$p['qty_stock'].")</option>"; 
                            } 
                            ?>
                        </select>
                    </div>

                    <input type="hidden" name="description" id="description">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">ລາຄາຕໍ່ໜ່ວຍ</label>
                        <div class="input-group">
                            <input type="number" name="price" id="price" class="form-control bg-light" readonly required placeholder="0">
                            <span class="input-group-text bg-light">₭</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">ຈຳນວນທີ່ຂາຍ</label>
                        <input type="number" name="qty" class="form-control py-2 text-center fw-bold" value="1" min="1" required>
                    </div>

                    <button type="submit" name="btn_save_item" class="btn btn-primary w-100 py-2 shadow-sm">
                        <i class="fas fa-cart-plus me-1"></i> ເພີ່ມເຂົ້າບິນຂາຍ
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="summary-box mb-4 d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <span class="text-white-50 small text-uppercase d-block mb-1">ຍອດລວມທັງໝົດທີ່ຕ້ອງຈ່າຍ</span>
                    <h2 class="fw-bold mb-0 text-white"><?php echo number_format($grand_total); ?> ກີບ</h2>
                </div>
                <i class="fas fa-wallet fa-3x text-white-50"></i>
            </div>

            <div class="card-custom p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center py-3" width="60">ລຳດັບ</th>
                                <th class="py-3">ລາຍການສິນຄ້າ</th>
                                <th class="text-center py-3" width="100">ຈຳນວນ</th>
                                <th class="text-end py-3" width="150">ລາຄາ/ໜ່ວຍ</th>
                                <th class="text-end py-3" width="160">ລວມ (ກີບ)</th>
                                <th class="text-center py-3" width="100">ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res_det = mysqli_query($connect, "SELECT d.*, p.part_name FROM service_details d LEFT JOIN parts_profile p ON d.part_id = p.part_id WHERE d.service_id = $service_id");
                            if (mysqli_num_rows($res_det) == 0) {
                                echo "<tr><td colspan='6' class='text-center text-muted py-5'><i class='fas fa-shopping-basket fs-3 mb-2 d-block text-black-50'></i>ຍັງບໍ່ມີລາຍການສິນຄ້າໃນບິນນີ້</td></tr>";
                            } else {
                                $i = 1;
                                while($d = mysqli_fetch_array($res_det)) {
                                    echo "<tr>
                                            <td class='text-center py-3 text-muted'>".$i."</td>
                                            <td class='py-3 fw-medium'>".($d['part_name'] ?? $d['description'])."</td>
                                            <td class='text-center py-3'><span class='badge bg-light text-dark border px-3 py-2'>".$d['qty']."</span></td>
                                            <td class='text-end py-3 text-muted'>".number_format($d['price'])."</td>
                                            <td class='text-end py-3 fw-bold text-dark'>".number_format($d['total'])."</td>
                                            <td class='text-center py-3'>
                                                <a href='manage_sale.php?action=delete_sale_item&del_id=".$d['detail_id']."' 
                                                   class='btn btn-outline-danger btn-sm px-2' 
                                                   onclick='return confirm(\"ຕ້ອງການລົບລາຍການນີ້ອອກບໍ່?\")'>
                                                     <i class='fas fa-trash-alt'></i> ລົບ
                                                </a>
                                            </td>
                                          </tr>";
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<iframe id="printFrame" style="display:none;"></iframe>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function updateSalePrice() { 
    var sel = document.getElementById("part_select"); 
    var opt = sel.options[sel.selectedIndex]; 
    var priceInput = document.getElementById("price");
    var descInput = document.getElementById("description");

    if(sel.value !== "") { 
        priceInput.value = opt.getAttribute("data-price"); 
        descInput.value = opt.getAttribute("data-name"); 
    } else {
        priceInput.value = "";
        descInput.value = "";
    }
}

function printSaleInvoice() {
    if(confirm('ຢືນຢັນການຊຳລະເງິນ ແລະ ປິດບິນຂາຍນີ້ແທ້ບໍ່?')) {
        $.ajax({
            url: 'manage_sale.php?action=update_status_sale',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    var iframe = document.getElementById('printFrame');
                    iframe.src = 'print_service_logs.php?id=<?php echo $service_id; ?>'; 
                    iframe.onload = function() {
                        setTimeout(function() {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();
                            window.location.href = 'manage_sale.php'; 
                        }, 300); 
                    };
                } else {
                    alert('ເກີດຂໍ້ຜິດພາດໃນການປ່ຽນສະຖານະບິນ!');
                }
            },
            error: function() { alert('ເກີດຂໍ້ຜິດພາດໃນການເຊື່ອມຕໍ່ລະບົບ!'); }
        });
    }
}
</script>
</body>
</html>