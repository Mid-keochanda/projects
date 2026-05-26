<?php
require_once("../cennect_dbstock.php");
if (!isset($connect)) { die("Error: ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນ."); }
mysqli_set_charset($connect, "utf8");

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($service_id <= 0) {
    echo "<script>alert('ລະບົບບໍ່ພົບ ID ບິນ'); window.location='select_service_logs.php';</script>";
    exit();
}

// 🛠️ ເພີ່ມ API ຂະໜາດນ້ອຍຢູ່ດ້ານເທິງ: ຮັບຄ່າຈາກ AJAX ເພື່ອອັບເດດສະຖານະຕອນກົດປິ່ນບິນ
if (isset($_GET['action']) && $_GET['action'] == 'update_status_print') {
    // ອັບເດດສະຖານະເປັນ success ແລະ ບັນທຶກເວລາປັດຈຸບັນ NOW() ເຂົ້າ completed_at
    $sql_update = "UPDATE service_logs SET status = 'success', completed_at = NOW() WHERE log_id = $service_id";
    if (mysqli_query($connect, $sql_update)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($connect)]);
    }
    exit(); // ຢຸດການເຮັດວຽກຂອງ PHP ບໍ່ໃຫ້ໂຫຼດ HTML ດ້ານລຸ່ມອອກມາ
}

// 1. Logic ບັນທຶກຄ່າແຮງງານ
if (isset($_POST['btn_save_labor'])) {
    $labor = floatval($_POST['labor_cost']);
    $sql_labor = "UPDATE service_logs SET labor_cost = $labor WHERE log_id = $service_id";
    
    if (mysqli_query($connect, $sql_labor)) {
        header("Location: ?id=$service_id");
        exit();
    } else {
        echo "<script>alert('ຜິດພາດ: ບໍ່ສາມາດອັບເດດຄ່າແຮງງານໄດ້ຊ່ວຍກວດສອບ Database! " . mysqli_error($connect) . "');</script>";
    }
}

// 2. Logic ບັນທຶກລາຍການອะໄຫຼ່
if (isset($_POST['btn_save'])) {
    $part_val = intval($_POST['part_id']);
    $part_id_sql = ($part_val > 0) ? $part_val : "NULL";
    $qty = intval($_POST['qty']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $total = $qty * $price;

    $can_save = true;
    if ($part_val > 0) {
        $check = mysqli_query($connect, "SELECT qty_stock FROM parts_profile WHERE part_id = $part_val");
        $row = mysqli_fetch_array($check);
        if (!$row || $row['qty_stock'] < $qty) {
            echo "<script>alert('ສະຕັອກບໍ່ພໍ!');</script>";
            $can_save = false;
        }
    }

    if ($can_save) {
        $sql = "INSERT INTO service_details (service_id, part_id, description, qty, price, total) VALUES ($service_id, $part_id_sql, '$description', $qty, $price, $total)";
        if (mysqli_query($connect, $sql)) {
            if ($part_val > 0) {
                mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock - $qty WHERE part_id = $part_val");
            }
            header("Location: ?id=$service_id");
            exit();
        }
    }
}

// 3. ດຶງຂໍ້ມູນລວມ
$res_total = mysqli_query($connect, "SELECT SUM(total) as sum_parts FROM service_details WHERE service_id = $service_id");
$total_data = mysqli_fetch_array($res_total);
$sum_parts = $total_data['sum_parts'] ?? 0;

// ດຶງຂໍ້ມູນ status ແລະ labor_cost ມາສະແດງຜົນ
$res_log = mysqli_query($connect, "SELECT labor_cost, status FROM service_logs WHERE log_id = $service_id");
$log_data = mysqli_fetch_array($res_log);
$labor_cost = $log_data['labor_cost'] ?? 0;
$current_status = $log_data['status'] ?? 'pending';
$grand_total = $sum_parts + $labor_cost;
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການບິນ #<?php echo str_pad($service_id, 5, "0", STR_PAD_LEFT); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        
        body { 
            font-family: 'Noto Sans Lao', sans-serif; 
            background-color: #f8f9fa; 
            color: #333;
        }
        
        .card-custom { 
            background: #fff;
            border: none;
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); 
        }
        
        .table thead th { 
            font-weight: 600; 
            background-color: #f1f4f8; 
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            font-size: 13px;
        }
        
        .table tbody td {
            vertical-align: middle;
            color: #555;
        }
        
        .btn {
            font-weight: 500;
            border-radius: 8px;
        }
        .summary-box {
            background: linear-gradient(135deg, #20c997 0%, #198754 100%);
            border-radius: 12px;
            padding: 12px;
            color: white;
        }
        .form-control, .form-select { border-radius: 8px; }

        @media print {
            body * { display: none !important; visibility: hidden !important; }
            #printFrame, #printFrame * { display: block !important; visibility: visible !important; width: 100% !important; height: 100% !important; }
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center mb-1">
                <a href="form_service_logs.php" class="btn btn-light btn-sm me-3 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="ກັບຄືນ">
                    <i class="fas fa-arrow-left text-secondary"></i>
                </a>
                <h3 class="fw-bold text-dark mb-0">ຈັດການລາຍການສ້ອມແປງ</h3>
            </div>
            <div class="d-flex align-items-center gap-3 ms-5">
                <p class="text-muted mb-0" style="font-size: 14px;">ເລກທີບິນ: <span class="fw-bold text-primary">#<?php echo str_pad($service_id, 5, "0", STR_PAD_LEFT); ?></span></p>
                <?php if($current_status == 'pending'): ?>
                    <span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-spinner fa-spin me-1"></i> ກຳລັງສ້ອມແປງ</span>
                <?php else: ?>
                    <span class="badge bg-success rounded-pill px-3"><i class="fas fa-check-circle me-1"></i> ສຳເລັດແລ້ວ</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="button" onclick="printInvoice(<?php echo $service_id; ?>)" class="btn btn-success d-flex align-items-center gap-2 px-4 shadow-sm">
                <i class="fas fa-print"></i> ພິມໃບບິນ & ປິດບິນ
            </button>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> ເພີ່ມລາຍການອະໄຫຼ່
            </button>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-8">
            <div class="card-custom p-3 h-100 d-flex align-items-center">
                <form method="POST" action="?id=<?php echo $service_id; ?>" class="w-100">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="form-label mb-0 fw-bold text-secondary"><i class="fas fa-tools text-warning me-1"></i> ຄ່າແຮງງານຊ່າງ</label>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">₭</span>
                                <input type="number" name="labor_cost" class="form-control border-start-0 ps-0" value="<?php echo $labor_cost; ?>" step="any" min="0" required placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-3 mt-3 mt-md-0">
                            <button type="submit" name="btn_save_labor" class="btn btn-warning w-100">
                                <i class="fas fa-save me-1"></i> ບັນທຶກຄ່າແຮງ
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="summary-box h-100 d-flex flex-column justify-content-center align-items-center text-center">
                <span class="text-white-50 small text-uppercase mb-1">ຍອດລວມສຸດທິທັງໝົດ</span>
                <h3 class="fw-bold mb-0 text-white"><?php echo number_format($grand_total); ?> ກີບ</h3>
            </div>
        </div>
    </div>

    <div class="card-custom p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center py-3" width="60">#</th>
                        <th class="py-3">ລາຍການ / ລາຍລະອຽດວຽກ</th>
                        <th class="text-center py-3" width="120">ຈຳນວນ</th>
                        <th class="text-end py-3" width="160">ລາຄາ/ໜ່ວຍ</th>
                        <th class="text-end py-3 pe-4" width="180">ລວມ (ກີບ)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res_det = mysqli_query($connect, "SELECT d.*, p.part_name FROM service_details d LEFT JOIN parts_profile p ON d.part_id = p.part_id WHERE d.service_id = $service_id");
                    if (mysqli_num_rows($res_det) == 0) {
                        echo "<tr><td colspan='5' class='text-center text-muted py-5'><i class='fas fa-box-open fs-3 mb-2 d-block text-black-50'></i>ຍັງບໍ່ມີລາຍການອະໄຫຼ່ໃນບິນນີ້</td></tr>";
                    } else {
                        $i = 1;
                        while($d = mysqli_fetch_array($res_det)) {
                            echo "<tr>
                                    <td class='text-center py-3 text-muted'>".$i."</td>
                                    <td class='py-3 fw-medium'>".($d['part_name'] ?? $d['description'])."</td>
                                    <td class='text-center py-3'><span class='badge bg-light text-dark border px-3 py-2'>".$d['qty']."</span></td>
                                    <td class='text-end py-3 text-muted'>".number_format($d['price'])."</td>
                                    <td class='text-end py-3 pe-4 fw-bold text-dark'>".number_format($d['total'])."</td>
                                  </tr>";
                            $i++;
                        }
                    }
                    ?>
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="4" class="text-end py-3 text-muted">ລວມຄ່າອາໄຫຼ່:</td>
                        <td class="text-end py-3 pe-4 fw-bold text-secondary"><?php echo number_format($sum_parts); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end py-2 text-muted border-0">ຄ່າແຮງງານຊ່າງ:</td>
                        <td class="text-end py-2 pe-4 fw-bold text-warning border-0">+ <?php echo number_format($labor_cost); ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="p-0 border-0"><div style="height: 4px; background-color: #20c997;"></div></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<iframe id="printFrame" style="display:none;"></iframe>

<script>
function printInvoice(serviceId) {
    // 1. ໃຊ້ AJAX ສົ່ງຄ່າໄປຫາ PHP ດ້ານເທິງ (Background) ເພື່ອອັບເດດສະຖານະເປັນ success ແລະ ບັນທຶກເວລາ
    $.ajax({
        url: window.location.pathname + '?id=' + serviceId + '&action=update_status_print',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                
                // 2. ຖ້າອັບເດດຖານຂໍ້ມູນສຳເລັດ, ໃຫ້ iframe ໂຫຼດໜ້າພິມບິນຂຶ້ນມາ
                var iframe = document.getElementById('printFrame');
                iframe.src = 'print_service_logs.php?id=' + serviceId;
                
                iframe.onload = function() {
                    setTimeout(function() {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        
                        // 3. ຫຼັງຈາກກົດປິ່ນແລ້ວ ໃຫ້ໂຫຼດໜ້ານີ້ຄືນໃໝ່ ເພື່ອສະແດງສະຖານະເປັນ "ສຳເລັດແລ້ວ"
                        window.location.reload();
                    }, 300); 
                };
                
            } else {
                alert('ເກີດຂໍ້ຜິດພາດ: ບໍ່ສາມາດປ່ຽນສະຖານະບິນໄດ້!');
            }
        },
        error: function() {
            alert('ບໍ່ສາມາດເຊື່ອມຕໍ່ລະບົບອັບເດດສະຖານະໄດ້, ແຕ່ກຳລັງດຶງໃບບິນໃຫ້...');
            // ຖ້າເອີ້ນອັບເດດຜິດພາດ ກໍໃຫ້ປິ່ນໄດ້ຄືເກົ່າ
            var iframe = document.getElementById('printFrame');
            iframe.src = 'print_service_logs.php?id=' + serviceId;
            iframe.onload = function() {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            };
        }
    });
}
</script>

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-primary"><i class="fas fa-plus-circle me-2"></i> ເພີ່ມລາຍການອະໄຫຼ່/ວຽກ</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="?id=<?php echo $service_id; ?>">
                <div class="modal-body pt-4">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary mb-1">Leurak ahai chak stock (ຖ້າມີ)</label>
                        <select name="part_id" id="part_select" class="form-select bg-light" onchange="updatePrice()">
                            <option value="0">-- ປ້ອນລາຍການໃໝ່ດ້ວຍຕົນເອງ --</option>
                            <?php 
                            $res = mysqli_query($connect, "SELECT * FROM parts_profile WHERE qty_stock > 0"); 
                            while($p = mysqli_fetch_array($res)) { 
                                echo "<option value='".$p['part_id']."' data-price='".$p['sale_price']."' data-name='".$p['part_name']."'>".$p['part_name']." (ພ້ອມขາຍ: ".$p['qty_stock'].")</option>"; 
                            } 
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-1">ຊື່ລາຍການ / ລາຍລະອຽດ</label>
                        <input type="text" name="description" id="description" class="form-control" placeholder="ເຊັ່ນ: ປ່ຽນນ້ຳມັນເຄື່ອງ..." required>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-7">
                            <label class="form-label small fw-bold text-secondary mb-1">ລາຄາຕໍ່ໜ່ວຍ (ກີບ)</label>
                            <input type="number" name="price" id="price" class="form-control" placeholder="0" min="0" required>
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label small fw-bold text-secondary mb-1">ຈຳນວນ</label>
                            <input type="number" name="qty" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" name="btn_save" class="btn btn-primary px-4 shadow-sm">ບັນທຶກລາຍການ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updatePrice() { 
    var sel = document.getElementById("part_select"); 
    var opt = sel.options[sel.selectedIndex]; 
    if(sel.value != "0") { 
        document.getElementById("price").value = opt.getAttribute("data-price"); 
        document.getElementById("description").value = opt.getAttribute("data-name"); 
        document.getElementById("price").style.backgroundColor = "#e9ecef";
        document.getElementById("description").style.backgroundColor = "#e9ecef";
    } else {
        document.getElementById("price").value = "";
        document.getElementById("description").value = "";
        document.getElementById("price").style.backgroundColor = "#fff";
        document.getElementById("description").style.backgroundColor = "#fff";
    }
}
</script>
</body>
</html>