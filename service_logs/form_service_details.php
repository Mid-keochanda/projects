<?php
session_start();
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<script>alert('ກະລຸນາລ໋ອກອິນກ່ອນ');location='index.php';</script>";
    exit();
} else {
require_once("../cennect_dbstock.php");
if (!isset($connect)) { die("Error: ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນ."); }
mysqli_set_charset($connect, "utf8");

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($service_id <= 0) {
    echo "<script>alert('ລະບົບບໍ່ພົບ ID ບິນ'); window.location='select_service_logs.php';</script>";
    exit();
}

// 🛠️ API ສຳລັບ AJAX ປິດບິນ
if (isset($_GET['action']) && $_GET['action'] == 'update_status_print') {
    $sql_update = "UPDATE service_logs SET status = 'success', completed_at = NOW() WHERE log_id = $service_id";
    if (mysqli_query($connect, $sql_update)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($connect)]);
    }
    exit();
}

// 1. Logic ບັນທຶກຄ່າແຮງງານ
if (isset($_POST['btn_save_labor'])) {
    $labor = floatval($_POST['labor_cost']);
    $sql_labor = "UPDATE service_logs SET labor_cost = $labor WHERE log_id = $service_id";
    if (mysqli_query($connect, $sql_labor)) {
        header("Location: ?id=$service_id");
        exit();
    } else {
        echo "<script>alert('ຜິດພາດ: " . mysqli_real_escape_string($connect, mysqli_error($connect)) . "');</script>";
    }
}

// 2. Logic ບັນທຶກລາຍການອະໄຫຼ່
if (isset($_POST['btn_save'])) {
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
            echo "<script>alert('ສະຕັອກບໍ່ພໍ! ໃນສະຕັອກເຫຼືອ: " . ($row['qty_stock'] ?? 0) . "');</script>";
            $can_save = false;
        }
    } else {
        echo "<script>alert('ກະລຸນາຢືນຢັນການຍິງບາໂຄດສິນຄ້າໃຫ້ຖືກຕ້ອງ!');</script>";
        $can_save = false;
    }

    if ($can_save) {
        $sql = "INSERT INTO service_details (service_id, part_id, description, qty, price, total) 
                VALUES ($service_id, $part_val, '$description', $qty, $price, $total)";
        if (mysqli_query($connect, $sql)) {
            mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock - $qty WHERE part_id = $part_val");
            header("Location: ?id=$service_id");
            exit();
        } else {
            echo "<script>alert('Error: " . mysqli_real_escape_string($connect, mysqli_error($connect)) . "');</script>";
        }
    }
}

// 3. Logic ຍົກເລີກລາຍການອະໄຫຼ່ (ຄືນສະຕັອກ)
if (isset($_GET['action']) && $_GET['action'] == 'delete_item') {
    $del_id = intval($_GET['del_id']);
    
    $item_check = mysqli_query($connect, "SELECT part_id, qty FROM service_details WHERE detail_id = $del_id");
    if (mysqli_num_rows($item_check) > 0) {
        $item = mysqli_fetch_array($item_check);
        $p_id = $item['part_id'];
        $p_qty = $item['qty'];
        
        if ($p_id > 0) {
            mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock + $p_qty WHERE part_id = $p_id");
        }
        mysqli_query($connect, "DELETE FROM service_details WHERE detail_id = $del_id");
    }
    header("Location: ?id=$service_id");
    exit();
}

// 4. ດຶງຂໍ້ມູນສະແດງຜົນ
$res_total = mysqli_query($connect, "SELECT SUM(total) as sum_parts FROM service_details WHERE service_id = $service_id");
$total_data = mysqli_fetch_array($res_total);
$sum_parts = $total_data['sum_parts'] ?? 0;

$res_log = mysqli_query($connect, "SELECT labor_cost, status FROM service_logs WHERE log_id = $service_id");
$log_data = mysqli_fetch_array($res_log);
$labor_cost = $log_data['labor_cost'] ?? 0;
$current_status = $log_data['status'] ?? 'pending';
$grand_total = $sum_parts + $labor_cost;

// 📦 ດຶງຂໍ້ມູນອະໄຫຼ່ທັງໝົດ ແລະ ແມັບເຂົ້າກັບຫ້ອງ part_code ຂອງທ່ານ
$parts_array = [];
$res_parts = mysqli_query($connect, "SELECT * FROM parts_profile");
while ($p = mysqli_fetch_array($res_parts)) {
    // ຖ້າຫ້ອງ part_code ວ່າງເປົ່າ ຈະໃຊ້ part_id ແທນອັດຕະໂນມັດ
    $barcode_key = (!empty($p['part_code'])) ? $p['part_code'] : $p['part_id'];
    
    $parts_array[] = [
        'part_id'    => $p['part_id'],
        'barcode'    => strval($barcode_key), // ສົ່ງເລກ part_code ໄປໃຫ້ JavaScript ຄົ້ນຫາ
        'part_name'  => $p['part_name'],
        'sale_price' => $p['sale_price']
    ];
}
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
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; color: #333; }
        .card-custom { background: #fff; border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); }
        .table thead th { font-weight: 600; background-color: #f1f4f8; color: #495057; border-bottom: 2px solid #e9ecef; font-size: 13px; }
        .table tbody td { vertical-align: middle; color: #555; }
        .btn { font-weight: 500; border-radius: 8px; }
        .summary-box { background: linear-gradient(135deg, #20c997 0%, #198754 100%); border-radius: 12px; padding: 12px; color: white; }
        .form-control, .form-select { border-radius: 8px; }
        @media print { body * { display: none !important; visibility: hidden !important; } #printFrame, #printFrame * { display: block !important; visibility: visible !important; width: 100% !important; height: 100% !important; } }
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
                <i class="fas fa-barcode"></i> ຍິງບາໂຄດເພີ່ມອະໄຫຼ່
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
                        <th class="text-end py-3" width="180">ລວມ (ກີບ)</th>
                        <th class="text-center py-3" width="120">ຈັດການ</th> </tr>
                </thead>
                <tbody>
                    <?php 
                    $res_det = mysqli_query($connect, "SELECT d.*, p.part_name FROM service_details d LEFT JOIN parts_profile p ON d.part_id = p.part_id WHERE d.service_id = $service_id");
                    if (mysqli_num_rows($res_det) == 0) {
                        echo "<tr><td colspan='6' class='text-center text-muted py-5'><i class='fas fa-box-open fs-3 mb-2 d-block text-black-50'></i>ຍັງບໍ່ມີລາຍການອະໄຫຼ່ໃນບິນນີ້</td></tr>";
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
                                        <a href='?id=".$service_id."&action=delete_item&del_id=".$d['detail_id']."' 
                                           class='btn btn-outline-danger btn-sm px-3' 
                                           onclick='return confirm(\"ຕ້ອງການຍົກເລີກລາຍການນີ້ ແລະ ຄືນສະຕັອກແທ້ບໍ່?\")'>
                                             <i class='fas fa-trash-alt me-1'></i> ຍົກເລີກ
                                        </a>
                                    </td>
                                  </tr>";
                            $i++;
                        }
                    }
                    ?>
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="4" class="text-end py-3 text-muted">ລວມຄ່າອາໄຫຼ່:</td>
                        <td class="text-end py-3 fw-bold text-secondary"><?php echo number_format($sum_parts); ?></td>
                        <td></td> </tr>
                    <tr>
                        <td colspan="4" class="text-end py-2 text-muted border-0">ຄ່າແຮງງານຊ່າງ:</td>
                        <td class="text-end py-2 fw-bold text-warning border-0">+ <?php echo number_format($labor_cost); ?></td>
                        <td class="border-0"></td> </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<iframe id="printFrame" style="display:none;"></iframe>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function printInvoice(serviceId) {
    $.ajax({
        url: window.location.pathname + '?id=' + serviceId + '&action=update_status_print',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                var iframe = document.getElementById('printFrame');
                iframe.src = 'print_service_logs.php?id=' + serviceId;
                iframe.onload = function() {
                    setTimeout(function() {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        window.location.reload();
                    }, 300); 
                };
            } else {
                alert('ເກີດຂໍ້ຜິດພາດໃນການປ່ຽນສະຖານະບິນ!');
            }
        },
        error: function() {
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
            <div class="modal-header bg-light border-bottom-0 pb-2">
                <h5 class="modal-title fw-bold text-primary"><i class="fas fa-barcode me-2"></i> ຍິງບາໂຄດສິນຄ້າ</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="?id=<?php echo $service_id; ?>">
                <div class="modal-body pt-1">
                    
                    <div class="mb-3 p-3 bg-primary bg-opacity-10 rounded border border-primary border-opacity-20 text-center">
                        <label class="form-label small fw-bold text-dark mb-2 d-block"><i class="fas fa-compress-alt text-primary"></i> ເອົາເຄື່ອງຍິງມາຍິງບາໂຄດໃສ່ບ່ອນນີ້</label>
                        <input type="text" id="barcode_search" class="form-control form-control-lg fw-bold text-center text-primary border-primary" placeholder="[ ຍິງບາໂຄດຢູ່ບ່ອນນີ້ ]" autocomplete="off">
                    </div>
                    
                    <input type="hidden" name="part_id" id="part_id_hidden" required>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary mb-1">ຊື່ອະໄຫຼ່ສິນຄ້າ</label>
                        <input type="text" name="description" id="description" class="form-control fw-bold text-dark" placeholder="ຊື່ສິນຄ້າຈະສະແດງຂຶ້ນບ່ອນນີ້..." readonly required style="background-color: #e9ecef;">
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-sm-7">
                            <label class="form-label small fw-bold text-secondary mb-1">ລາຄາ/ໜ່ວຍ (ກີບ)</label>
                            <input type="number" name="price" id="price" class="form-control fw-bold text-secondary" placeholder="0" readonly required style="background-color: #e9ecef;">
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label small fw-bold text-danger mb-1">ປ່ຽນຈຳນວນ</label>
                            <input type="number" name="qty" id="part_qty" class="form-control fw-bold text-center text-success fs-5" value="1" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" name="btn_save" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-save me-1"></i> ບັນທຶກເຂົ້າບິນ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// 📦 ຂໍ້ມູນສະຕັອກສິນຄ້າທັງໝົດທີ່ແປງມາຈາກ PHP (ມີຂໍ້ມູນ barcode ມາຈາກຫ້ອງ part_code ແລ້ວ)
const partsStockList = <?php echo json_encode($parts_array); ?>;

$(document).ready(function() {
    // ເມື່ອເປີດ Modal ໃຫ້ເອົາເຄີເຊີມາໂຟກັດລໍຖ້າການຍິງທັນທີ
    $('#addModal').on('shown.bs.modal', function () {
        clearFields();
        $('#barcode_search').focus();
    });

    // ດັກຈັບທັງການຍິງອັດຕະໂນມັດ (input) ແລະ ການກົດ Enter (keypress)
    $('#barcode_search').on('keypress input', function(e) {
        if (e.type === 'keypress' && e.which !== 13) {
            return; 
        }
        
        var barcodeInput = $(this).val().trim();
        if (barcodeInput === '') return;

        // ຄົ້ນຫາສິນຄ້າຈາກເລກ part_code ຫຼື part_id ທີ່ກົງກັນ
        var matched = partsStockList.find(function(item) {
            return item.barcode.toLowerCase() === barcodeInput.toLowerCase() || String(item.part_id) === barcodeInput;
        });

        if (matched) {
            // ເອົາຂໍ້ມູນໄປສະແດງໃນຟອມ
            $('#part_id_hidden').val(matched.part_id);
            $('#description').val(matched.part_name);
            $('#price').val(matched.sale_price);
            
            // ໂດດໄປຊ່ອງຈຳນວນທັນທີ ເພື່ອໃຫ້ພ້ອມແກ້ໄຂ ຫຼື ກົດ Enter ບັນທຶກ
            $('#part_qty').focus().select();
            $(this).val(''); // ລ້າງຊ່ອງຍິງເພື່ອຖ້າຍິງອັນຕໍ່ໄປ
        } else {
            // ຖ້າກົດ Enter ແລ້ວບໍ່ພົບ ໃຫ້ເຕືອນບອກເລກທີ່ອ່ານໄດ້
            if (e.type === 'keypress' && e.which === 13) {
                alert('❌ ບໍ່ພົບລະຫັດບາໂຄດ: "' + barcodeInput + '" ໃນລະບົບ!\nກະລຸນາກວດສອບວ່າເລກນີ້ໄດ້ປ້ອນໄວ້ໃນຫ້ອງ part_code ແລ້ວຫຼືບໍ່.');
                clearFields();
                $(this).focus().select();
            }
        }
    });
});

function clearFields() {
    $('#barcode_search').val('');
    $('#part_id_hidden').val('');
    $('#description').val('');
    $('#price').val('');
    $('#part_qty').val('1');
}
</script>
</body>
</html>
<?php
}
?>