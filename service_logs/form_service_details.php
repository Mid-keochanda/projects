<?php
session_start();
// ກວດສອບການເຂົ້າເຖິງດ້ວຍ SweetAlert2 ແທນ alert()
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body><script>
    Swal.fire({icon: 'error', title: 'ກະລຸນາລ໋ອກອິນກ່ອນ', confirmButtonText: 'ຕົກລົງ'}).then(() => { location='index.php'; });
    </script></body></html>";
    exit();
} 

require_once("../cennect_dbstock.php");
if (!isset($connect)) { die("Error: ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນ."); }
mysqli_set_charset($connect, "utf8");

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($service_id <= 0) {
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body><script>
    Swal.fire({icon: 'error', title: 'ລະບົບບໍ່ພົບ ID ບິນ', confirmButtonText: 'ຕົກລົງ'}).then(() => { window.location='select_service_logs.php'; });
    </script></body></html>";
    exit();
}

$swal_message = ''; // ຕົວປ່ຽນສຳລັບເກັບຂໍ້ຄວາມແຈ້ງເຕືອນ SweetAlert

// 🛠️ API ສຳລັບ AJAX ປິດບິນ
if (isset($_GET['action']) && $_GET['action'] == 'update_status_print') {
    $stmt = $connect->prepare("UPDATE service_logs SET status = 'success', completed_at = NOW() WHERE log_id = ?");
    $stmt->bind_param("i", $service_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    $stmt->close();
    exit();
}

// 1. Logic ບັນທຶກຄ່າແຮງງານ
if (isset($_POST['btn_save_labor'])) {
    $labor = floatval($_POST['labor_cost']);
    $stmt = $connect->prepare("UPDATE service_logs SET labor_cost = ? WHERE log_id = ?");
    $stmt->bind_param("di", $labor, $service_id);
    if ($stmt->execute()) {
        header("Location: ?id=$service_id");
        exit();
    }
    $stmt->close();
}

// 🛠️ Logic ຍົກເລີກ/ລ້າງຄ່າແຮງງານ
if (isset($_GET['action']) && $_GET['action'] == 'clear_labor') {
    $stmt = $connect->prepare("UPDATE service_logs SET labor_cost = 0 WHERE log_id = ?");
    $stmt->bind_param("i", $service_id);
    if ($stmt->execute()) {
        header("Location: ?id=$service_id");
        exit();
    }
    $stmt->close();
}

// 2. Logic ບັນທຶກລາຍການອະໄຫຼ່
if (isset($_POST['btn_save'])) {
    $part_val = intval($_POST['part_id']);
    $qty = intval($_POST['qty']);
    $price = floatval($_POST['price']);
    $description = $_POST['description'];

    $can_save = true;
    if ($part_val > 0) {
        $stmt_check = $connect->prepare("SELECT qty_stock FROM parts_profile WHERE part_id = ?");
        $stmt_check->bind_param("i", $part_val);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();
        $row = $res_check->fetch_assoc();
        
        if (!$row || $row['qty_stock'] < $qty) {
            // ແທນທີ່ຈະໃຊ້ alert() ກໍເກັບຂໍ້ຄວາມໄວ້ສະແດງຜົນດ້ວຍ SweetAlert2
            $swal_message = "Swal.fire({icon: 'warning', title: 'ສະຕັອກບໍ່ພໍ!', text: 'ໃນສະຕັອກເຫຼືອພຽງ: " . ($row['qty_stock'] ?? 0) . "', confirmButtonText: 'ຕົກລົງ'});";
            $can_save = false;
        }
        $stmt_check->close();
    } else {
        $swal_message = "Swal.fire({icon: 'warning', title: 'ແຈ້ງເຕືອນ', text: 'ກະລຸນາເລືອກອະໄຫຼ່ລົດກ່ອນບັນທຶກ!', confirmButtonText: 'ຕົກລົງ'});";
        $can_save = false;
    }

    if ($can_save) {
        $stmt_exist = $connect->prepare("SELECT detail_id, qty FROM service_details WHERE service_id = ? AND part_id = ?");
        $stmt_exist->bind_param("ii", $service_id, $part_val);
        $stmt_exist->execute();
        $res_exist = $stmt_exist->get_result();
        
        if ($res_exist->num_rows > 0) {
            $exist_row = $res_exist->fetch_assoc();
            $new_qty = $exist_row['qty'] + $qty;
            $new_total = $new_qty * $price;
            
            $stmt_up = $connect->prepare("UPDATE service_details SET qty = ?, total = ? WHERE detail_id = ?");
            $stmt_up->bind_param("idi", $new_qty, $new_total, $exist_row['detail_id']);
            $stmt_up->execute();
            $stmt_up->close();
        } else {
            $total = $qty * $price;
            $stmt_in = $connect->prepare("INSERT INTO service_details (service_id, part_id, description, qty, price, total) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_in->bind_param("iisidd", $service_id, $part_val, $description, $qty, $price, $total);
            $stmt_in->execute();
            $stmt_in->close();
        }
        $stmt_exist->close();

        $stmt_stock = $connect->prepare("UPDATE parts_profile SET qty_stock = qty_stock - ? WHERE part_id = ?");
        $stmt_stock->bind_param("ii", $qty, $part_val);
        $stmt_stock->execute();
        $stmt_stock->close();

        header("Location: ?id=$service_id");
        exit();
    }
}

// 3. Logic ຍົກເລີກລາຍການອະໄຫຼ່ 
if (isset($_GET['action']) && $_GET['action'] == 'delete_item') {
    $del_id = intval($_GET['del_id']);
    
    $stmt_item = $connect->prepare("SELECT part_id, qty FROM service_details WHERE detail_id = ?");
    $stmt_item->bind_param("i", $del_id);
    $stmt_item->execute();
    $res_item = $stmt_item->get_result();
    
    if ($res_item->num_rows > 0) {
        $item = $res_item->fetch_assoc();
        $p_id = $item['part_id'];
        $p_qty = $item['qty'];
        
        if ($p_id > 0) {
            $stmt_re = $connect->prepare("UPDATE parts_profile SET qty_stock = qty_stock + ? WHERE part_id = ?");
            $stmt_re->bind_param("ii", $p_qty, $p_id);
            $stmt_re->execute();
            $stmt_re->close();
        }
        $stmt_del = $connect->prepare("DELETE FROM service_details WHERE detail_id = ?");
        $stmt_del->bind_param("i", $del_id);
        $stmt_del->execute();
        $stmt_del->close();
    }
    $stmt_item->close();
    
    header("Location: ?id=$service_id#parts_section");
    exit();
}

// 4. ດຶງຂໍ້ມູນສະແດງຜົນ 
$stmt_sum = $connect->prepare("SELECT SUM(total) as sum_parts FROM service_details WHERE service_id = ?");
$stmt_sum->bind_param("i", $service_id);
$stmt_sum->execute();
$total_data = $stmt_sum->get_result()->fetch_assoc();
$sum_parts = $total_data['sum_parts'] ?? 0;
$stmt_sum->close();

$stmt_log = $connect->prepare("SELECT labor_cost, status FROM service_logs WHERE log_id = ?");
$stmt_log->bind_param("i", $service_id);
$stmt_log->execute();
$log_data = $stmt_log->get_result()->fetch_assoc();
$labor_cost = $log_data['labor_cost'] ?? 0;
$current_status = $log_data['status'] ?? 'pending';
$stmt_log->close();

$grand_total = $sum_parts + $labor_cost;

$parts_array = [];
$res_parts = mysqli_query($connect, "SELECT * FROM parts_profile");
while ($p = mysqli_fetch_array($res_parts)) {
    $barcode_key = (!empty($p['part_code'])) ? $p['part_code'] : $p['part_id'];
    
    $img_raw = trim($p['part_image']);
    if (!empty($img_raw)) {
        if (strpos($img_raw, 'uploads/') !== false) {
            $part_image_path = str_replace('uploads/', '../parts_profile/uploads/', $img_raw);
        } else {
            $part_image_path = '../parts_profile/uploads/' . $img_raw;
        }
    } else {
        $part_image_path = 'https://placehold.co/150x150?text=No+Image';
    }
    
    $parts_array[] = [
        'part_id'    => $p['part_id'],
        'barcode'    => strval($barcode_key),
        'part_name'  => $p['part_name'], 
        'sale_price' => $p['sale_price'],
        'part_image' => $part_image_path,
        'qty_stock'  => intval($p['qty_stock'])
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f6f9; color: #333; }
        .card-custom { background: #fff; border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); }
        .table thead th { font-weight: 600; background-color: #f1f4f8; color: #495057; border-bottom: 2px solid #e9ecef; font-size: 12px; padding: 6px 10px; }
        .table tbody td { vertical-align: middle; color: #555; font-size: 13px; padding: 6px 10px; }
        .table tfoot td { font-size: 13px; padding: 6px 10px; }
        .btn { font-weight: 500; border-radius: 8px; transition: all 0.2s ease; }
        .btn:disabled { cursor: not-allowed; opacity: 0.7; }
        .summary-box { background: linear-gradient(135deg, #20c997 0%, #198754 100%); border-radius: 12px; padding: 15px; color: white; }
        .form-control, .form-select { border-radius: 8px; }
        .part-item-card { cursor: pointer; transition: all 0.2s ease-in-out; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #fff; display: flex; flex-direction: column; height: 100%; position: relative;}
        .part-item-card:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); border-color: #3b82f6; }
        .part-item-card img { object-fit: contain !important; width: 100%; height: 100%; background-color: #fafafa; }
        @media print { body * { display: none !important; visibility: hidden !important; } #printFrame, #printFrame * { display: block !important; visibility: visible !important; width: 100% !important; height: 100% !important; } }
    </style>
</head>
<body>

<div class="container-fluid py-4 px-xl-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center mb-1">
                <a href="form_service_logs.php" class="btn btn-white btn-sm me-3 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fas fa-arrow-left text-secondary"></i>
                </a>
                <h3 class="fw-bold text-dark mb-0">ໜ້າຈັດການລາຍການສ້ອມແປງ</h3>
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
        <div>
            <button type="button" id="btn_print" onclick="printInvoice(<?php echo $service_id; ?>)" class="btn btn-success d-flex align-items-center gap-2 px-4 py-2 shadow-sm fs-5 fw-bold">
                <i class="fas fa-print"></i> ພິມໃບບິນ & ປິດບິນ
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8 col-xl-8">
            <div class="card-custom p-4 shadow-sm h-100">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-boxes me-2"></i> ຄລີກເລືອກອະໄຫຼ່ລົດສ້ອມແປງ</h5>
                <form method="POST" action="?id=<?php echo htmlspecialchars($service_id, ENT_QUOTES, 'UTF-8'); ?>" id="part_form">
                    <input type="hidden" name="btn_save" value="1">
                    <input type="hidden" name="part_id" id="part_id_hidden" required>
                    <div class="mb-3">
                        <div class="input-group shadow-sm border rounded">
                            <span class="input-group-text bg-white text-muted border-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="part_filter_input" class="form-control border-0 ps-0 py-2 fs-6" placeholder="ພິມຄົ້ນຫາຊື່ ຫຼື ຍິງບາໂຄດຢູ່ບ່ອນນີ້..." autocomplete="off">
                        </div>
                    </div>
                    <div class="p-2 border rounded bg-light mb-3">
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2 overflow-y-auto" style="max-height: 520px; min-height: 350px;" id="parts_grid_display"></div>
                    </div>
                    <div class="p-3 bg-light rounded border mb-0 d-none">
                        <input type="text" name="description" id="description">
                        <input type="number" name="price" id="price">
                        <input type="number" name="qty" id="part_qty" value="1">
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4 col-xl-4">
            <div class="row mb-4 g-3">
                <div class="col-12">
                    <div class="card-custom p-3 h-100 d-flex align-items-center">
                        <form method="POST" action="?id=<?php echo htmlspecialchars($service_id, ENT_QUOTES, 'UTF-8'); ?>" class="w-100" id="labor_form">
                            <div class="row align-items-center g-2">
                                <div class="col-sm-3">
                                    <label class="form-label mb-0 fw-bold text-secondary" style="font-size:13px;"><i class="fas fa-tools text-warning me-1"></i> ຄ່າແຮງ</label>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0">₭</span>
                                        <input type="text" id="labor_cost_display" class="form-control border-start-0 ps-0 fw-bold text-end" value="<?php echo number_format($labor_cost); ?>" required placeholder="0">
                                        <input type="hidden" name="labor_cost" id="labor_cost_real" value="<?php echo $labor_cost; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-4 d-flex gap-1">
                                    <button type="submit" name="btn_save_labor" id="btn_labor" class="btn btn-warning btn-sm w-50 fw-bold py-1">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    
                                    <?php if ($labor_cost > 0): ?>
                                        <a href="?id=<?php echo $service_id; ?>&action=clear_labor" 
                                           class="btn btn-danger btn-sm w-50 py-1 d-flex align-items-center justify-content-center swal-confirm" 
                                           data-text="ຕ້ອງການລ້າງຄ່າແຮງງານໃຫ້ເປັນ 0 ແທ້ບໍ?">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary btn-sm w-50 py-1" disabled><i class="fas fa-times"></i></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12">
                    <div class="summary-box d-flex flex-column justify-content-center align-items-center text-center shadow-sm">
                        <span class="text-white-50 small text-uppercase mb-1">ຍອດລວມສຸດທິທັງໝົດ</span>
                        <h4 class="fw-bold mb-0 text-white"><?php echo number_format($grand_total); ?> ກີບ</h4>
                    </div>
                </div>
            </div>

            <div id="parts_section" class="card-custom p-0 overflow-hidden shadow-sm">
                <div class="p-2 bg-white border-bottom">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size:13px;"><i class="fas fa-file-invoice text-primary me-2"></i>ລາຍການອະໄຫຼ່ໃນບິນນີ້</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="35">#</th>
                                <th>ລາຍການ</th>
                                <th class="text-center" width="60">ຈຳນວນ</th>
                                <th class="text-end" width="100">ລວມ (ກີບ)</th>
                                <th class="text-center" width="40"></th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stmt_det = $connect->prepare("SELECT d.*, p.part_name FROM service_details d LEFT JOIN parts_profile p ON d.part_id = p.part_id WHERE d.service_id = ?");
                            $stmt_det->bind_param("i", $service_id);
                            $stmt_det->execute();
                            $res_det = $stmt_det->get_result();

                            if ($res_det->num_rows == 0) {
                                echo "<tr><td colspan='5' class='text-center text-muted py-4'><i class='fas fa-box-open fs-5 mb-2 d-block text-black-50'></i>ຍັງບໍ່ມີລາຍການອະໄຫຼ່</td></tr>";
                            } else {
                                $i = 1;
                                while($d = $res_det->fetch_assoc()) {
                                    $safe_name = htmlspecialchars($d['part_name'] ?? $d['description'], ENT_QUOTES, 'UTF-8');
                                    echo "<tr>
                                            <td class='text-center text-muted'>".$i."</td>
                                            <td class='fw-medium text-truncate' style='max-width:120px;' title='".$safe_name."'>".$safe_name."</td>
                                            <td class='text-center'><span class='badge bg-light text-dark border px-2 py-1'>".$d['qty']."</span></td>
                                            <td class='text-end fw-bold text-dark'>".number_format($d['total'])."</td>
                                            <td class='text-center'>
                                                <a href='?id=".$service_id."&action=delete_item&del_id=".$d['detail_id']."' 
                                                   class='text-danger swal-confirm' 
                                                   data-text='ຕ້ອງການຍົກເລີກລາຍການ: ".$safe_name." ແທ້ບໍ່?'>
                                                     <i class='fas fa-trash-alt'></i>
                                                </a>
                                            </td>
                                          </tr>";
                                    $i++;
                                }
                            }
                            $stmt_det->close();
                            ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end text-muted">ລວມຄ່າອາໄຫຼ່:</td>
                                <td class="text-end fw-bold text-secondary"><?php echo number_format($sum_parts); ?></td>
                                <td></td> 
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end border-0 text-muted">ຄ່າແຮງງານ:</td>
                                <td class="text-end border-0 fw-bold text-warning">+<?php echo number_format($labor_cost); ?></td>
                                <td class="border-0"></td> 
                            </tr>
                            <tr class="table-primary" style="border-top: 2px solid #0d6efd;">
                                <td colspan="3" class="text-end fw-bold text-primary">ຍອດລວມທັງໝົດ:</td>
                                <td class="text-end fw-bold text-primary fs-6"><?php echo number_format($grand_total); ?></td>
                                <td></td> 
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<iframe id="printFrame" style="display:none;"></iframe>

<script>
const partsStockList = <?php echo json_encode($parts_array); ?>;

function saveScrollPosition() {
    sessionStorage.setItem('scroll_window', $(window).scrollTop());
    sessionStorage.setItem('scroll_grid', $('#parts_grid_display').scrollTop());
}

$(document).ready(function() {
    renderPartsGrid(partsStockList);

    // ກວດສອບຂໍ້ຄວາມແຈ້ງເຕືອນຈາກ PHP ແລ້ວສະແດງຜົນຜ່ານ SweetAlert2
    <?php if(!empty($swal_message)): ?>
        <?php echo $swal_message; ?>
    <?php endif; ?>

    var savedWindowScroll = sessionStorage.getItem('scroll_window');
    var savedGridScroll = sessionStorage.getItem('scroll_grid');
    if (savedWindowScroll !== null || savedGridScroll !== null) {
        setTimeout(function() {
            if (savedWindowScroll !== null) { $(window).scrollTop(savedWindowScroll); sessionStorage.removeItem('scroll_window'); }
            if (savedGridScroll !== null) { $('#parts_grid_display').scrollTop(savedGridScroll); sessionStorage.removeItem('scroll_grid'); }
        }, 100);
    }

    // ແປງຄ່າເງິນໃຫ້ມີຈຸດ
    $('#labor_cost_display').on('input', function() {
        let val = $(this).val().replace(/[^0-9.]/g, '');
        if (val !== '') {
            let num = parseFloat(val);
            $(this).val(num.toLocaleString('en-US'));
            $('#labor_cost_real').val(val);
        } else {
            $(this).val('');
            $('#labor_cost_real').val(0);
        }
    });

    // ດັກຈັບການ Submit ບັນທຶກຄ່າແຮງ (ເພີ່ມ Loading State)
    $('#labor_form').on('submit', function() {
        saveScrollPosition();
        let rawVal = $('#labor_cost_display').val().replace(/[^0-9.]/g, '');
        $('#labor_cost_real').val(rawVal === '' ? 0 : rawVal);
        
        let $btn = $('#btn_labor');
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
    });

    // ດັກຈັບການຄລີກຍົກເລີກ/ລຶບ (ໃຊ້ SweetAlert ແທນ confirm ປົກກະຕິ)
    $(document).on('click', '.swal-confirm', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        let textMsg = $(this).data('text');
        
        Swal.fire({
            title: 'ຢືນຢັນການດຳເນີນການ',
            text: textMsg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ຢືນຢັນ',
            cancelButtonText: 'ຍົກເລີກ'
        }).then((result) => {
            if (result.isConfirmed) {
                saveScrollPosition();
                Swal.fire({ title: 'ກຳລັງປະມວນຜົນ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                window.location.href = url;
            }
        });
    });

    // ຄົ້ນຫາອະໄຫຼ່
    $('#part_filter_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var searchVal = $(this).val().trim().toLowerCase();
            var matched = partsStockList.find(item => item.barcode.toLowerCase() === searchVal || String(item.part_id) === searchVal);
            if (matched) {
                saveScrollPosition();
                autoSubmitPart(matched.part_id, matched.part_name, matched.sale_price);
            } else {
                // ປ່ຽນຈາກ alert ມາເປັນ SweetAlert
                Swal.fire({ icon: 'error', title: 'ບໍ່ພົບຂໍ້ມູນ', text: 'ບໍ່ພົບລະຫັດອະໄຫຼ່ນີ້ໃນລະບົບ!', confirmButtonText: 'ຕົກລົງ' });
                $(this).val('');
            }
        }
    });

    $('#part_filter_input').on('input', function() {
        var searchVal = $(this).val().trim().toLowerCase();
        var filtered = partsStockList.filter(item => item.part_name.toLowerCase().includes(searchVal) || item.barcode.toLowerCase().includes(searchVal));
        renderPartsGrid(filtered);
    });

    // ເລືອກອະໄຫຼ່ຈາກລາຍການ
    $(document).on('click', '.part-item-card', function() {
        saveScrollPosition();
        // ສະແດງ Loading ທັນທີທີ່ກົດເລືອກ ເພື່ອບໍ່ໃຫ້ຜູ້ໃຊ້ກົດຊ້ຳ
        Swal.fire({
            title: 'ກຳລັງເພີ່ມລາຍການ...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        autoSubmitPart($(this).data('id'), $(this).data('name'), $(this).data('price'));
    });
});

function autoSubmitPart(id, name, price) {
    $('#part_id_hidden').val(id);
    $('#description').val(name);
    $('#price').val(price);
    $('#part_qty').val('1'); 
    $('#part_form').submit();
}

function renderPartsGrid(items) {
    var grid = $('#parts_grid_display');
    grid.empty();
    if (items.length === 0) {
        grid.html('<div class="col-12 text-center text-muted py-4"><i class="fas fa-search-minus d-block fs-3 mb-2 text-black-50"></i>ບໍ່ພົບອະໄຫຼ່ທີ່ຄົ້ນຫາ</div>');
        return;
    }
    items.forEach(function(item) {
        var cardHtml = `
            <div class="col">
                <div class="card part-item-card p-2 text-center shadow-sm" data-id="${item.part_id}" data-name="${item.part_name.replace(/"/g, '&quot;')}" data-price="${item.sale_price}">
                    <div class="ratio ratio-1x1 mb-2 bg-light rounded overflow-hidden">
                        <img src="${item.part_image}" alt="${item.part_name.replace(/"/g, '&quot;')}" class="img-fluid" onerror="this.src='https://placehold.co/150x150?text=No+Image'">
                    </div>
                    <div class="small fw-bold text-dark text-truncate mb-2" style="font-size:12px;" title="${item.part_name.replace(/"/g, '&quot;')}">${item.part_name}</div>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="badge bg-primary-subtle text-primary px-2 py-1" style="font-size:11px;">${Number(item.sale_price).toLocaleString()} ₭</span>
                        <span class="badge bg-danger-subtle text-danger px-2 py-1" style="font-size:11px;">ສາງ: ${item.qty_stock}</span>
                    </div>
                </div>
            </div>
        `;
        grid.append(cardHtml);
    });
}

function printInvoice(serviceId) {
    let $btn = $('#btn_print');
    let originalHtml = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin"></i> ກຳລັງປະມວນຜົນ...').prop('disabled', true);
    
    $.ajax({
        url: window.location.pathname + '?id=' + serviceId + '&action=update_status_print',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                var iframe = document.getElementById('printFrame');
                iframe.src = 'print_service_logs.php?id=' + serviceId;
                iframe.onload = function() {
                    setTimeout(function() { iframe.contentWindow.focus(); iframe.contentWindow.print(); window.location.reload(); }, 300); 
                };
            } else { 
                Swal.fire({ icon: 'error', title: 'ຜິດພາດ', text: 'ເກີດຂໍ້ຜິດພາດ: ' + response.message });
                $btn.html(originalHtml).prop('disabled', false);
            }
        },
        error: function() {
            Swal.fire({ icon: 'error', title: 'ຜິດພາດ', text: 'ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບເຊີບເວີໄດ້' });
            $btn.html(originalHtml).prop('disabled', false);
        }
    });
}
</script>
</body>
</html>
<?php
?>