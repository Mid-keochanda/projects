<?php
session_start();
// ກວດສອບການເຂົ້າເຖິງດ້ວຍ SweetAlert2
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body><script>
    Swal.fire({icon: 'error', title: 'ກະລຸນາລ໋ອກອິນກ່ອນ', confirmButtonText: 'ຕົກລົງ'}).then(() => { location='index.php'; });
    </script></body></html>";
    exit();
} 

require_once("../cennect_dbstock.php");
if (!isset($connect)) { die("Error: ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນ."); }
mysqli_set_charset($connect, "utf8");

$log_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($log_id <= 0) {
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body><script>
    Swal.fire({icon: 'error', title: 'ລະບົບບໍ່ພົບ ID ບິນ', confirmButtonText: 'ຕົກລົງ'}).then(() => { window.location='select_service_logs.php'; });
    </script></body></html>";
    exit();
}

$swal_message = '';

// 🛠️ API ສຳລັບ AJAX ປິດບິນ
if (isset($_POST['action']) && $_POST['action'] == 'update_status_print') {
    $p_log_id = intval($_POST['id']);
    $pay_status = $_POST['payment_status']; 
    $pay_type = $_POST['payment_type']; 
    $total = floatval($_POST['total_amount']);
    
    $stmt = $connect->prepare("UPDATE service_logs SET status = 'success', completed_at = NOW() WHERE log_id = ?");
    $stmt->bind_param("i", $p_log_id);
    $stmt->execute();
    $stmt->close();

    $stmt_chk = $connect->prepare("SELECT inv_id FROM invoices WHERE log_id = ?");
    $stmt_chk->bind_param("i", $p_log_id);
    $stmt_chk->execute();
    $res_chk = $stmt_chk->get_result();
    
    if($res_chk->num_rows == 0) {
        $inv_no = "INV" . date('Ymd') . str_pad($p_log_id, 4, "0", STR_PAD_LEFT);
        $stmt_inv = $connect->prepare("INSERT INTO invoices (inv_no, log_id, inv_date, total_amount, net_amount, payment_status, payment_type) VALUES (?, ?, CURDATE(), ?, ?, ?, ?)");
        $stmt_inv->bind_param("siddds", $inv_no, $p_log_id, $total, $total, $pay_status, $pay_type);
        $stmt_inv->execute();
        $stmt_inv->close();
    } else {
        $stmt_inv = $connect->prepare("UPDATE invoices SET payment_status = ?, payment_type = ?, total_amount = ?, net_amount = ? WHERE log_id = ?");
        $stmt_inv->bind_param("ssdii", $pay_status, $pay_type, $total, $total, $p_log_id);
        $stmt_inv->execute();
        $stmt_inv->close();
    }
    
    echo json_encode(['status' => 'success']);
    exit();
}

// 1. Logic ບັນທຶກຄ່າແຮງງານ (ປັບປຸງຮອງຮັບ AJAX)
if (isset($_POST['btn_save_labor'])) {
    $labor = floatval($_POST['labor_cost']);
    $stmt = $connect->prepare("UPDATE service_logs SET labor_cost = ? WHERE log_id = ?");
    $stmt->bind_param("di", $labor, $log_id);
    if ($stmt->execute()) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $stmt_sum = $connect->prepare("SELECT SUM(total) as sum_parts FROM service_details WHERE log_id = ?");
            $stmt_sum->bind_param("i", $log_id);
            $stmt_sum->execute();
            $sum_parts = floatval($stmt_sum->get_result()->fetch_assoc()['sum_parts'] ?? 0);
            $stmt_sum->close();

            echo json_encode(['status' => 'success', 'grand_total' => ($sum_parts + $labor)]);
            exit();
        }
        header("Location: ?id=$log_id");
        exit();
    }
    $stmt->close();
}

// 🛠️ Logic ຍົກເລີກ/ລ້າງຄ່າແຮງງານ (ປັບປຸງຮອງຮັບ AJAX)
if (isset($_GET['action']) && $_GET['action'] == 'clear_labor') {
    $stmt = $connect->prepare("UPDATE service_logs SET labor_cost = 0 WHERE log_id = ?");
    $stmt->bind_param("i", $log_id);
    if ($stmt->execute()) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $stmt_sum = $connect->prepare("SELECT SUM(total) as sum_parts FROM service_details WHERE log_id = ?");
            $stmt_sum->bind_param("i", $log_id);
            $stmt_sum->execute();
            $sum_parts = floatval($stmt_sum->get_result()->fetch_assoc()['sum_parts'] ?? 0);
            $stmt_sum->close();

            echo json_encode(['status' => 'success', 'grand_total' => $sum_parts]);
            exit();
        }
        header("Location: ?id=$log_id");
        exit();
    }
    $stmt->close();
}

// 2. Logic ບັນທຶກລາຍການອະໄຫຼ່ (ປັບປຸງຮອງຮັບ AJAX)
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
            $msg = "ໃນສະຕັອກເຫຼືອພຽງ: " . ($row['qty_stock'] ?? 0);
            $can_save = false;
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['status' => 'warning', 'title' => 'ສະຕັອກບໍ່ພໍ!', 'text' => $msg]);
                exit();
            }
            $swal_message = "Swal.fire({icon: 'warning', title: 'ສະຕັອກບໍ່ພໍ!', text: '$msg', confirmButtonText: 'ຕົກລົງ'});";
        }
        $stmt_check->close();
    } else {
        $can_save = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'warning', 'title' => 'ແຈ້ງເຕືອນ', 'text' => 'ກະລຸນາເລືອກອະໄຫຼ່ລົດກ່ອນບັນທຶກ!']);
            exit();
        }
    }

    if ($can_save) {
        $stmt_exist = $connect->prepare("SELECT detail_id, qty FROM service_details WHERE log_id = ? AND part_id = ?");
        $stmt_exist->bind_param("ii", $log_id, $part_val);
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
            $stmt_in = $connect->prepare("INSERT INTO service_details (log_id, part_id, description, qty, price, total) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_in->bind_param("iisidd", $log_id, $part_val, $description, $qty, $price, $total);
            $stmt_in->execute();
            $stmt_in->close();
        }
        $stmt_exist->close();

        $stmt_stock = $connect->prepare("UPDATE parts_profile SET qty_stock = qty_stock - ? WHERE part_id = ?");
        $stmt_stock->bind_param("ii", $qty, $part_val);
        $stmt_stock->execute();
        $stmt_stock->close();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $stmt_sum = $connect->prepare("SELECT SUM(total) as sum_parts FROM service_details WHERE log_id = ?");
            $stmt_sum->bind_param("i", $log_id);
            $stmt_sum->execute();
            $sum_parts = floatval($stmt_sum->get_result()->fetch_assoc()['sum_parts'] ?? 0);
            $stmt_sum->close();

            $stmt_log = $connect->prepare("SELECT labor_cost FROM service_logs WHERE log_id = ?");
            $stmt_log->bind_param("i", $log_id);
            $stmt_log->execute();
            $labor_cost = floatval($stmt_log->get_result()->fetch_assoc()['labor_cost'] ?? 0);
            $stmt_log->close();

            echo json_encode(['status' => 'success', 'grand_total' => ($sum_parts + $labor_cost)]);
            exit();
        }

        header("Location: ?id=$log_id");
        exit();
    }
}

// 3. Logic ຍົກເລີກລາຍການອະໄຫຼ່ (ປັບປຸງຮອງຮັບ AJAX ບໍ່ໃຫ້ກະພິບ)
if (isset($_GET['action']) && $_GET['action'] == 'delete_item') {
    $del_id = intval($_GET['del_id']);
    
    $stmt_item = $connect->prepare("SELECT part_id, qty FROM service_details WHERE detail_id = ?");
    $stmt_item->bind_param("i", $del_id);
    $stmt_item->execute();
    $res_item = $stmt_item->get_result();
    
    $p_id = 0; $p_qty = 0;
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
    
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $stmt_sum = $connect->prepare("SELECT SUM(total) as sum_parts FROM service_details WHERE log_id = ?");
        $stmt_sum->bind_param("i", $log_id);
        $stmt_sum->execute();
        $sum_parts = floatval($stmt_sum->get_result()->fetch_assoc()['sum_parts'] ?? 0);
        $stmt_sum->close();

        $stmt_log = $connect->prepare("SELECT labor_cost FROM service_logs WHERE log_id = ?");
        $stmt_log->bind_param("i", $log_id);
        $stmt_log->execute();
        $labor_cost = floatval($stmt_log->get_result()->fetch_assoc()['labor_cost'] ?? 0);
        $stmt_log->close();

        echo json_encode(['status' => 'success', 'part_id' => $p_id, 'qty' => $p_qty, 'grand_total' => ($sum_parts + $labor_cost)]);
        exit();
    }
    
    header("Location: ?id=$log_id#parts_section");
    exit();
}

// 4. ດຶງຂໍ້ມູນສະແດງຜົນ
$stmt_sum = $connect->prepare("SELECT SUM(total) as sum_parts FROM service_details WHERE log_id = ?");
$stmt_sum->bind_param("i", $log_id);
$stmt_sum->execute();
$total_data = $stmt_sum->get_result()->fetch_assoc();
$sum_parts = floatval($total_data['sum_parts'] ?? 0); 
$stmt_sum->close();

$stmt_log = $connect->prepare("SELECT labor_cost, status FROM service_logs WHERE log_id = ?");
$stmt_log->bind_param("i", $log_id);
$stmt_log->execute();
$log_data = $stmt_log->get_result()->fetch_assoc();
$labor_cost = floatval($log_data['labor_cost'] ?? 0); 
$current_status = $log_data['status'] ?? 'pending';
$stmt_log->close();

$grand_total = $sum_parts + $labor_cost;

// 5. ດຶງຂໍ້ມູນການຊຳລະເງິນເກົ່າ
$stmt_inv = $connect->prepare("SELECT payment_status, payment_type FROM invoices WHERE log_id = ?");
$stmt_inv->bind_param("i", $log_id);
$stmt_inv->execute();
$inv_data = $stmt_inv->get_result()->fetch_assoc();
$curr_pay_status = $inv_data['payment_status'] ?? 'Paid';
$curr_pay_type = $inv_data['payment_type'] ?? 'ເງິນໂອນ';
$stmt_inv->close();

$parts_array = [];
$res_parts = mysqli_query($connect, "SELECT * FROM parts_profile");
while ($p = mysqli_fetch_array($res_parts)) {
    $barcode_key = (!empty($p['part_code'])) ? $p['part_code'] : $p['part_id'];
    $img_raw = trim($p['part_image']);
    if (!empty($img_raw)) {
        $part_image_path = (strpos($img_raw, 'uploads/') !== false) ? str_replace('uploads/', '../parts_profile/uploads/', $img_raw) : '../parts_profile/uploads/' . $img_raw;
    } else {
        $part_image_path = 'https://placehold.co/150x150?text=No+Image';
    }
    
    $parts_array[] = [
        'part_id'    => $p['part_id'],
        'barcode'    => strval($barcode_key),
        'part_name'  => $p['part_name'], 
        'sale_price' => floatval($p['sale_price']),
        'part_image' => $part_image_path,
        'qty_stock'  => intval($p['qty_stock'])
    ];
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການບິນ #<?php echo str_pad($log_id, 5, "0", STR_PAD_LEFT); ?></title>
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
        .summary-box { background: linear-gradient(135deg, #20c997 0%, #198754 100%); border-radius: 12px; padding: 15px; color: white; }
        .form-control, .form-select { border-radius: 8px; }
        .part-item-card { cursor: pointer; transition: all 0.2s ease-in-out; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #fff; display: flex; flex-direction: column; height: 100%; position: relative;}
        .part-item-card:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); border-color: #3b82f6; }
        .part-item-card img { object-fit: contain !important; width: 100%; height: 110px; background-color: #fafafa; }
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
                <p class="text-muted mb-0" style="font-size: 14px;">... ເລກທີບິນ: <span class="fw-bold text-primary">#<?php echo str_pad($log_id, 5, "0", STR_PAD_LEFT); ?></span></p>
                <?php if($current_status == 'pending'): ?>
                    <span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-spinner fa-spin me-1"></i> ກຳລັງສ້ອມແປງ</span>
                <?php else: ?>
                    <span class="badge bg-success rounded-pill px-3"><i class="fas fa-check-circle me-1"></i> ສຳເລັດແລ້ວ</span>
                <?php endif; ?>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-success d-flex align-items-center gap-2 px-4 py-2 shadow-sm fs-5 fw-bold" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="fas fa-print"></i> ພິມໃບບິນ & ປິດບິນ
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8 col-xl-8">
            <div class="card-custom p-4 shadow-sm h-100">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-boxes me-2"></i> ຄລີກເລືອກອະໄຫຼ່ລົດສ້ອມແປງ</h5>
                <form method="POST" action="?id=<?php echo htmlspecialchars($log_id, ENT_QUOTES, 'UTF-8'); ?>" id="part_form">
                    <input type="hidden" name="btn_save" value="1">
                    <input type="hidden" name="part_id" id="part_id_hidden" required>
                    <div class="mb-3">
                        <div class="input-group shadow-sm border rounded">
                            <span class="input-group-text bg-white text-muted border-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="part_filter_input" class="form-control border-0 ps-0 py-2 fs-6" placeholder="ພິມຄົ້ນຫາຊື່ ຫຼື ຍິງບາໂຄດຢູ່ບ່ອນນີ້..." autocomplete="off">
                        </div>
                    </div>
                    <div class="p-2 border rounded bg-light mb-3">
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 g-2 overflow-y-auto" style="max-height: 520px; min-height: 350px;" id="parts_grid_display"></div>
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
                        <form method="POST" action="?id=<?php echo htmlspecialchars($log_id, ENT_QUOTES, 'UTF-8'); ?>" class="w-100" id="labor_form">
                            <input type="hidden" name="btn_save_labor" value="1">
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
                                    <button type="submit" id="btn_labor" class="btn btn-warning btn-sm w-50 fw-bold py-1">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <?php if ($labor_cost > 0): ?>
                                        <a href="?id=<?php echo $log_id; ?>&action=clear_labor" class="btn btn-danger btn-sm w-50 py-1 d-flex align-items-center justify-content-center swal-confirm" data-text="ຕ້ອງການລ້າງຄ່າແຮງງານໃຫ້ເປັນ 0 ແທ້ບໍ?">
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
                        <h4 class="fw-bold mb-0 text-white" id="grand_total_text"><?php echo number_format($grand_total); ?> ກີບ</h4>
                    </div>
                </div>
            </div>

            <!-- ຕາຕະລາງສະແດງລາຍການອະໄຫຼ່ -->
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
                            $stmt_det = $connect->prepare("SELECT d.*, p.part_name FROM service_details d LEFT JOIN parts_profile p ON d.part_id = p.part_id WHERE d.log_id = ?");
                            $stmt_det->bind_param("i", $log_id);
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
                                                <a href='?id=".$log_id."&action=delete_item&del_id=".$d['detail_id']."' class='text-danger swal-confirm' data-text='ຕ້ອງການຍົກເລີກລາຍການ: ".$safe_name." ແທ້ບໍ່?'>
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
                                <td></td> 
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

<!-- Modal ຢືນຢັນການຊຳລະເງິນ -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow" style="border-radius: 15px; border: none;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%); border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold" id="paymentModalLabel"><i class="fas fa-money-bill-wave me-2"></i> ຢືນຢັນການຊຳລະເງິນ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted mb-1">ຍອດລວມທີ່ຕ້ອງຊຳລະ:</p>
                <h3 class="text-danger fw-bold mb-4"><?php echo number_format($grand_total); ?> ກີບ</h3>
                
                 <div class="mb-3 text-start">
                    <label class="form-label fw-bold"><i class="fas fa-wallet text-success me-1"></i> ວິທີຊຳລະເງິນ</label>
                    <select id="pay_type_select" class="form-select form-select-lg" style="border-radius: 10px;">
                        <option value="ເງິນສົດ">💵 ເງິນສົດ (Cash)</option>
                        <option value="ເງິນໂອນ">📱 ເງິນໂອນ (Transfer)</option>
                    </select>
                </div>

                <div id="cash_calc_block" class="mb-4 text-start" style="display: none;">
                    <div class="p-3 bg-light rounded border border-warning">
                        <label class="form-label fw-bold"><i class="fas fa-hand-holding-usd text-warning me-1"></i> ຮັບເງິນມາ (ກີບ)</label>
                        <input type="text" id="received_amount_display" class="form-control form-control-lg text-end fw-bold text-success" placeholder="ປ້ອນຈຳນວນເງິນ...">
                        <input type="hidden" id="received_amount_real" value="0">
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-2 border-top">
                            <span class="fw-bold text-secondary">ເງິນທອນ:</span>
                            <span id="change_amount_display" class="fw-bold fs-5 text-danger">0 ກີບ</span>
                        </div>
                    </div>
                </div>

                <div id="qr_code_block" class="mb-4 text-center" style="display: none;">
                    <div class="p-3 bg-light rounded border border-primary">
                        <p class="fw-bold text-primary mb-2"><i class="fas fa-qrcode me-1"></i> ສະແກນ QR Code ເພື່ອໂອນເງິນ</p>
                        <?php 
                            $bank_name = "BCEL"; $account_name = "MID KEOCHANDA"; $account_number = "141122531890"; 
                            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("BANK:$bank_name|ACC:$account_number|NAME:$account_name|AMOUNT:$grand_total|BILL:$log_id");
                        ?>
                        <img src="<?php echo $qr_url; ?>" alt="QR" class="img-fluid border rounded p-2 bg-white shadow-sm" style="max-width: 180px;">
                        <p class="small text-muted mt-2 mb-0 fw-bold">ຊື່ບັນຊີ: <?php echo $account_name; ?><br>ເລກບັນຊີ: <?php echo $account_number; ?></p>
                    </div>
                </div>
                
                <button type="button" id="btn_confirm_print" onclick="confirmAndPrint()" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow-sm mt-2" style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-2"></i> ຢືນຢັນ & ພິມບິນ
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const partsStockList = <?php echo json_encode($parts_array); ?>;
let grandTotal = <?php echo $grand_total; ?>; // 🛠️ ປ່ຽນເປັນ let ເພື່ອໃຫ້ JS ອັບເດດຄ່າເງິນລວມໄດ້ຕະຫຼອດເວລາ

function renderPartsGrid(data) {
    let html = '';
    data.forEach(item => {
        let stockClass = item.qty_stock <= 0 ? 'opacity-50' : '';
        let qtyDisplay = item.qty_stock > 0 ? item.qty_stock : 'ໝົດ';
        
        // ກຳນົດສີຂອງປ້າຍ (Badge) ຕາມຈຳນວນສະຕັອກ
        let badgeClass = '';
        if (item.qty_stock <= 0) {
            badgeClass = 'bg-danger text-white'; // ສີແດງ: ໝົດສະຕັອກ
        } else if (item.qty_stock <= 5) {
            badgeClass = 'bg-warning text-dark'; // ສີສົ້ມ/ເຫຼືອງ: ໃກ້ຈະໝົດ
        } else {
            badgeClass = 'bg-success text-white'; // ສີຂຽວ: ມີເຄື່ອງປົກກະຕິ
        }

        html += `
            <div class="col">
                <div class="part-item-card position-relative ${stockClass}" data-id="${item.part_id}" data-name="${item.part_name}" data-price="${item.sale_price}" style="cursor: pointer;">
                    
                    <!-- ປ້າຍສະແດງຈຳນວນອາໄຫຼ່ -->
                    <span class="badge ${badgeClass} position-absolute top-0 end-0 m-1 shadow-sm" style="font-size: 10px;">
                        ${qtyDisplay}
                    </span>
                    
                    <img src="${item.part_image}" height="80" class="w-100" style="object-fit: contain;">
                    <div class="p-1 text-center">
                        <small class="d-block fw-bold text-truncate" style="font-size:11px;" title="${item.part_name}">${item.part_name}</small>
                        <small class="text-primary fw-bold" style="font-size:11px;">${item.sale_price.toLocaleString()} ກີບ</small>
                    </div>
                </div>
            </div>`;
    });
    $('#parts_grid_display').html(html);
}
// 🛠️ ຟັງຊັນ AJAX ບັນທຶກອະໄຫຼ່
function autoSubmitPart(part_id, part_name, price) {
    $('#part_id_hidden').val(part_id);
    $('#description').val(part_name);
    $('#price').val(price);
    $('#part_qty').val(1);

    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: $('#part_form').serialize(),
        dataType: 'json',
        success: function(response) {
            Swal.close();
            if (response.status === 'success') {
                let part = partsStockList.find(item => item.part_id == part_id);
                if (part) {
                    part.qty_stock -= 1;
                    renderPartsGrid(partsStockList); 
                }
                if (response.grand_total !== undefined) {
                    grandTotal = response.grand_total;
                    $('.modal-body h3.text-danger').text(Number(grandTotal).toLocaleString() + ' ກີບ');
                }
                $('#parts_section').load(window.location.href + ' #parts_section > *');
                $('#grand_total_text').load(window.location.href + ' #grand_total_text > *');
                $('#part_filter_input').val('').focus();
                checkCashPayment();
            } else if (response.status === 'warning') {
                Swal.fire({ icon: 'warning', title: response.title, text: response.text, confirmButtonText: 'ຕົກລົງ' });
            }
        },
        error: function() {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'ຜິດພາດ', text: 'ບໍ່ສາມາດເຊື່ອມຕໍ່ເຊີເວີໄດ້!', confirmButtonText: 'ຕົກລົງ' });
        }
    });
}

// 🛠️ ຟັງຊັນຄິດໄລ່ເງິນທອນ
function checkCashPayment() {
    let received = parseInt($('#received_amount_real').val()) || 0;
    if (received < grandTotal) {
        $('#change_amount_display').text('0 ກີບ').removeClass('text-success').addClass('text-danger');
        $('#btn_confirm_print').prop('disabled', true);
    } else {
        let change = received - grandTotal;
        $('#change_amount_display').text(change.toLocaleString() + ' ກີບ').removeClass('text-danger').addClass('text-success');
        $('#btn_confirm_print').prop('disabled', false);
    }
}

// 🛠️ ຟັງຊັນຢືນຢັນການປິດບິນ
function confirmAndPrint() {
    let payType = $('#pay_type_select').val();
    let payStatus = 'Paid'; 

    Swal.fire({
        title: 'ຢືນຢັນການປິດບິນ?',
        text: "ລະບົບຈະບັນທຶກການຊຳລະເງິນ ແລະ ປ່ຽນສະຖານະບິນນີ້ເປັນສຳເລັດທັນທີ",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ຢືນຢັນ',
        cancelButtonText: 'ຍົກເລີກ'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'ກຳລັງບັນທຶກ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            
            $.ajax({
                type: 'POST',
                url: window.location.href,
                data: {
                    action: 'update_status_print',
                    id: <?php echo $log_id; ?>,
                    payment_status: payStatus,
                    payment_type: payType,
                    total_amount: grandTotal
                },
                dataType: 'json',
                success: function(res) {
                    Swal.close();
                    if(res.status === 'success') {
                        var f = document.getElementById('printFrame');
                        f.src = 'print_service_logs.php?id=<?php echo $log_id; ?>';
                        f.onload = function() {
                            f.contentWindow.print();
                            setTimeout(function(){ window.location = 'form_service_logs.php'; }, 1000);
                        };
                    }
                }
            });
        }
    });
}

$(document).ready(function() {
    renderPartsGrid(partsStockList);

    // 🛠️ AJAX ສຳລັບຟອມບັນທຶກຄ່າແຮງງານ (ບໍ່ໃຫ້ກະພິບ)
    $('#labor_form').on('submit', function(e) {
        e.preventDefault();
        let rawVal = $('#labor_cost_display').val().replace(/[^0-9.]/g, '');
        $('#labor_cost_real').val(rawVal === '' ? 0 : rawVal);
        $('#btn_labor').html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: $('#labor_form').serialize(),
            dataType: 'json',
            success: function(response) {
                $('#btn_labor').html('<i class="fas fa-save"></i>').prop('disabled', false);
                if (response.status === 'success') {
                    if (response.grand_total !== undefined) {
                        grandTotal = response.grand_total;
                        $('.modal-body h3.text-danger').text(Number(grandTotal).toLocaleString() + ' ກີບ');
                    }
                    $('#parts_section').load(window.location.href + ' #parts_section > *');
                    $('#grand_total_text').load(window.location.href + ' #grand_total_text > *');
                    checkCashPayment();
                    Swal.fire({ icon: 'success', title: 'ບັນທຶກຄ່າແຮງງານສຳເລັດ!', timer: 1000, showConfirmButton: false });
                }
            },
            error: function() {
                $('#btn_labor').html('<i class="fas fa-save"></i>').prop('disabled', false);
                Swal.fire({ icon: 'error', title: 'ຜິດພາດ', text: 'ບໍ່ສາມາດບັນທຶກຄ່າແຮງໄດ້!', confirmButtonText: 'ຕົກລົງ' });
            }
        });
    });

    // 🛠️ AJAX ສຳລັບປຸ່ມ ລົບລາຍການ ແລະ ລ້າງຄ່າແຮງ (ແກ້ໄຂບໍ່ໃຫ້ໜ້າຈໍກະພິບ)
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
                Swal.fire({ title: 'ກຳລັງປະມວນຜົນ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                
                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.status === 'success') {
                            // ຄືນສະຕັອກເຂົ້າໄປໃນ Grid ແບບ Realtime ຖ້າເປັນການລົບອະໄຫຼ່
                            if (response.part_id && response.qty) {
                                let part = partsStockList.find(item => item.part_id == response.part_id);
                                if (part) {
                                    part.qty_stock += parseInt(response.qty);
                                    renderPartsGrid(partsStockList);
                                }
                            }
                            
                            // ອັບເດດຍອດເງິນລວມທັງໝົດ
                            if (response.grand_total !== undefined) {
                                grandTotal = response.grand_total;
                                $('.modal-body h3.text-danger').text(Number(grandTotal).toLocaleString() + ' ກີບ');
                            }

                            // ໂຫຼດສ່ວນສະແດງຜົນໃໝ່ແບບ Smooth
                            $('#parts_section').load(window.location.href + ' #parts_section > *');
                            $('#grand_total_text').load(window.location.href + ' #grand_total_text > *');
                            
                            // ຖ້າເປັນການລ້າງຄ່າແຮງ ໃຫ້ເຄຼຍຊ່ອງປ້ອນເປັນ 0
                            if (url.includes('action=clear_labor')) {
                                $('#labor_cost_display').val('0');
                                $('#labor_cost_real').val(0);
                            }
                            
                            checkCashPayment();
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({ icon: 'error', title: 'ຜິດພາດ', text: 'ບໍ່ສາມາດເຊື່ອມຕໍ່ເຊີເວີໄດ້!', confirmButtonText: 'ຕົກລົງ' });
                    }
                });
            }
        });
    });

    // ຄົ້ນຫາດ້ວຍການ ຍິງບາໂຄດ (Enter)
    $('#part_filter_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var searchVal = $(this).val().trim().toLowerCase();
            var matched = partsStockList.find(item => item.barcode.toLowerCase() === searchVal || String(item.part_id) === searchVal);
            if (matched) {
                if(matched.qty_stock <= 0) {
                    Swal.fire({ icon: 'warning', title: 'ສະຕັອກໝົດ!', text: 'ອະໄຫຼ່ລາຍການນີ້ໝົດສະຕັອກແລ້ວ', confirmButtonText: 'ຕົກລົງ' });
                    $(this).val('');
                    return;
                }
                Swal.fire({ title: 'ກຳລັງເພີ່ມລາຍການ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                autoSubmitPart(matched.part_id, matched.part_name, matched.sale_price);
            } else {
                Swal.fire({ icon: 'error', title: 'ບໍ່ພົບຂໍ້ມູນ', text: 'ບໍ່ພົບລະຫັດອະໄຫຼ່ນີ້ໃນລະບົບ!', confirmButtonText: 'ຕົກລົງ' });
                $(this).val('');
            }
        }
    });

    // ຄົ້ນຫາແບບ Realtime (ພິມໄປ ຊອກໄປ)
    $('#part_filter_input').on('input', function() {
        var searchVal = $(this).val().trim().toLowerCase();
        var filtered = partsStockList.filter(item => item.part_name.toLowerCase().includes(searchVal) || item.barcode.toLowerCase().includes(searchVal));
        renderPartsGrid(filtered);
    });

    // ເຫດການຄລີກເລືອກອະໄຫຼ່ຈາກ Grid
    $(document).on('click', '.part-item-card', function() {
        if($(this).hasClass('opacity-50')) {
            Swal.fire({ icon: 'warning', title: 'ສະຕັອກໝົດ!', text: 'ອະໄຫຼ່ລາຍການນີ້ໝົດສະຕັອກແລ້ວ', confirmButtonText: 'ຕົກລົງ' });
            return;
        }
        Swal.fire({ title: 'ກຳລັງເພີ່ມລາຍການ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        autoSubmitPart($(this).data('id'), $(this).data('name'), $(this).data('price'));
    });

    // ຈັດການປ່ຽນວິທີຊຳລະເງິນ
    $('#pay_type_select').on('change', function() {
        if ($(this).val() === 'ເງິນສົດ') {
            $('#cash_calc_block').slideDown();
            $('#qr_code_block').slideUp();
            checkCashPayment(); 
        } else {
            $('#cash_calc_block').slideUp();
            $('#qr_code_block').slideDown();
            $('#btn_confirm_print').prop('disabled', false); 
        }
    }).trigger('change');

    // ປ້ອນເງິນສົດທີ່ຮັບມາ
    $('#received_amount_display').on('input', function() {
        let val = $(this).val().replace(/[^0-9]/g, ''); 
        if (val !== '') {
            let num = parseInt(val);
            $(this).val(num.toLocaleString('en-US'));
            $('#received_amount_real').val(num);
        } else {
            $(this).val('');
            $('#received_amount_real').val(0);
        }
        checkCashPayment();
    });
    
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
});
</script>
<iframe id="printFrame" style="display:none;"></iframe>
</body>
</html>