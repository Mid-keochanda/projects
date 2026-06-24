<?php
session_start();
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<script>alert('ກະລຸນາລ໋ອກອິນກ່ອນ');location='index.php';</script>";
    exit();
} else {
include("../cennect_dbstock.php");
mysqli_set_charset($connect, "utf8");

// =========================================================================
// 🌟 🛠️ ສ່ວນປະມວນຜົນ AJAX (ດຶງຂໍ້ມູນລູກຄ້າ ແລະ ລົດອັດຕາໂນມັດ)
// =========================================================================
if (isset($_POST['action']) && $_POST['action'] == 'get_customer_cars' && isset($_POST['cust_id'])) {
    header('Content-Type: application/json; charset=utf-8');
    $cust_id = mysqli_real_escape_string($connect, $_POST['cust_id']);
    
    // 1. ກວດສອບຂໍ້ມູນລູກຄ້າ
    $cust_query = mysqli_query($connect, "SELECT cust_name FROM customers WHERE cust_id = '$cust_id'");
    
    if (mysqli_num_rows($cust_query) > 0) {
        $cust_data = mysqli_fetch_assoc($cust_query);
        
        // 2. ດຶງຂໍ້ມູນລົດທັງໝົດຂອງລູກຄ້າຄົນນີ້
        $cars_query = mysqli_query($connect, "SELECT car_id, car_plate FROM cars WHERE cust_id = '$cust_id' ORDER BY car_plate ASC");
        $cars = [];
        while ($c = mysqli_fetch_assoc($cars_query)) {
            $cars[] = [
                'car_id' => $c['car_id'],
                'car_plate' => $c['car_plate']
            ];
        }
        
        // ສົ່ງຂໍ້ມູນກັບໄປເປັນ JSON
        echo json_encode([
            'status' => 'success',
            'customer' => ['cust_name' => $cust_data['cust_name']],
            'cars' => $cars
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'not_found'], JSON_UNESCAPED_UNICODE);
    }
    exit(); 
}
// =========================================================================

// --- ຈັດລຽງ SQL ---
// ສັງເກດ: ປ່ຽນ service_id ເປັນ service_details.log_id ເພື່ອໃຫ້ກົງກັບຖານຂໍ້ມູນໃໝ່
$sql_logs = "SELECT l.*, c.car_plate, cust.cust_name, u.fname, u.lname,
            (SELECT SUM(total) FROM service_details WHERE service_details.log_id = l.log_id) as total_parts
            FROM service_logs l
            LEFT JOIN cars c ON l.car_id = c.car_id
            LEFT JOIN customers cust ON c.cust_id = cust.cust_id
            LEFT JOIN users u ON l.user_id = u.user_id
            ORDER BY 
                CASE WHEN l.status = 'pending' THEN 0 ELSE 1 END ASC,
                l.completed_at DESC,
                l.log_id DESC";
$res_logs = mysqli_query($connect, $sql_logs);
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລາຍການບິນສ້ອມແປງ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; height: 100%; }
        .container-fluid { max-width: 1400px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); margin-bottom: 20px; }
        .table-container { background: #ffffff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 10px; }
        .table thead { background-color: #067ef7; }
        .table th { font-size: 15px; text-transform: uppercase; color: #f3f5f7; padding: 12px !important; background-color: #4361ee !important;}
        .table td { padding: 12px 10px !important; vertical-align: middle; white-space: nowrap; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .btn-sm { font-size: 12px; padding: 5px 12px; }
        .badge { font-size: 11px; padding: 6px 10px; }
        .badge-pending { background-color: #ffc107; color: #000; font-weight: 500; }
        .badge-success { background-color: #20c997; color: #fff; font-weight: 500; }
        .time-text-only { font-size: 13px; font-weight: 500; font-family: 'Courier New', Courier, monospace; }
        .form-control, .form-select { border-radius: 10px; }
        
        /* Modal Customization */
        .modal-content { border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 30px rgba(0,0,0,0.15); }
        .modal-header-custom { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 1.5rem; border: none; }
        .input-group-text { border-radius: 12px 0 0 12px; border-right: none; }
        .input-group .form-control { border-radius: 0 12px 12px 0; border-left: none; }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="text-primary fw-bold mb-1"><i class="fas fa-tools me-2"></i> ລະບົບຈັດການການສ້ອມແປງ</h4>
                <p class="text-muted mb-0 small">ຕາຕະລາງຕິດຕາມ ແລະ ເປີດບິນແປງລົດຂອງລູກຄ້າ</p>
            </div>
            <button type="button" class="btn btn-primary fw-bold px-4 py-2 shadow-sm d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" data-bs-target="#openServiceModal" style="border-radius: 10px;">
                <i class="fas fa-plus-circle"></i> ເປີດບິນສ້ອມແປງໃໝ່
            </button>
        </div>
    </div>

    <div class="card table-container">
        <div class="card-body p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" width="90">ເລກບິນ</th>
                            <th>ທະບຽນລົດ</th>
                            <th class="text-nowrap">ເຈົ້າຂອງລົດ</th>
                            <th>ຊ່າງຜູ້ຮັບຜິດຊອບ</th>
                            <th>ອາການເບື້ອງຕົ້ນ</th>
                            <th class="text-end">ຍອດລວມ</th>
                            <th class="text-center" width="130">ສະຖານະ</th>
                            <th>ເວລາເປີດບິນ</th>
                            <th>ເວລາແປງແລ້ວ</th>
                            <th class="text-center" width="120">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while($row = mysqli_fetch_array($res_logs)) { 
                            $parts_cost = $row['total_parts'] ?? 0;
                            $labor_cost = $row['labor_cost'] ?? 0;
                            $grand_total = $parts_cost + $labor_cost;

                            $created_time = !empty($row['service_date']) ? date('d/m/Y H:i', strtotime($row['service_date'])) : '---';
                            $completed_time = !empty($row['completed_at']) ? date('d/m/Y H:i', strtotime($row['completed_at'])) : '---';
                        ?>
                        <tr style="<?php echo $row['status'] == 'pending' ? 'background-color: #fffdf5;' : ''; ?>">
                            <td class="fw-bold text-primary ps-4">#<?php echo str_pad($row['log_id'], 5, "0", STR_PAD_LEFT); ?></td>
                            <td class="fw-bold text-dark"><i class="fas fa-car text-muted me-1"></i> <?php echo htmlspecialchars($row['car_plate']); ?></td>
                            <td><?php echo $row['cust_name'] ?? '<span class="text-muted">---</span>'; ?></td>
                            <td><i class="far fa-user text-muted me-1"></i> <?php echo htmlspecialchars($row['fname'] . " " . $row['lname']); ?></td>
                            <td>
                                <div class="text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($row['symptoms']); ?>">
                                    <?php echo htmlspecialchars($row['symptoms']); ?>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-danger"><?php echo number_format($grand_total); ?> ₭</td>
                            
                            <td class="text-center">
                                <?php if($row['status'] == 'pending'): ?>
                                    <span class="badge badge-pending rounded-pill"><i class="fas fa-spinner fa-spin me-1"></i> ກຳລັງສ້ອມແປງ</span>
                                <?php else: ?>
                                    <span class="badge badge-success rounded-pill"><i class="fas fa-check-circle me-1"></i> ສຳເລັດແລ້ວ</span>
                                <?php endif; ?>
                            </td>

                            <td class="time-text-only text-secondary"><?php echo $created_time; ?></td>

                            <td class="time-text-only text-success fw-bold">
                                <?php if($row['status'] == 'pending'): ?>
                                    <span class="text-muted">---</span>
                                <?php else: ?>
                                    <?php echo $completed_time; ?>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <?php if($row['status'] == 'pending'): ?>
                                    <a href="form_service_details.php?id=<?php echo $row['log_id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                                        <i class="fas fa-cog me-1"></i> ຈັດການ
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">---</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } 
                        if(mysqli_num_rows($res_logs) == 0) { ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fs-3 d-block mb-2 text-black-50"></i>
                                ບໍ່ມີຂໍ້ມູນບິນສ້ອມແປງໃນລະບົບ
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="openServiceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="openServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;">
        <form action="insert_service_logs.php" method="POST" class="modal-content">
            <div class="modal-header-custom text-white text-center position-relative">
                <h4 class="modal-title fw-bold mb-1" id="openServiceModalLabel">
                    <i class="fas fa-file-invoice me-2"></i> ເປີດບິນສ້ອມແປງໃໝ່
                </h4>
                <p class="text-white-50 small mb-0">ກະລຸນາປ້ອນຂໍ້ມູນຍານພາຫະນະ ແລະ ອາການເບື້ອງຕົ້ນ</p>
                <button type="button" class="btn-close btn-close-white position-absolute" style="top: 1.5rem; right: 1.5rem;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-white">
    
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small"><i class="fas fa-id-card text-muted me-1"></i> ປ້ອນລະຫັດລູກຄ້າ</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="modal_cust_id" class="form-control bg-light" placeholder="ພິມລະຫັດລູກຄ້າ..." style="padding: 0.75rem 1rem;" autocomplete="off">
                    </div>
                    <div id="modal_cust_name_display" class="form-text mt-2 fw-bold" style="display: none; font-size: 14px;"></div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small"><i class="fas fa-car text-muted me-1"></i> ເລືອກທະບຽນລົດຂອງລູກຄ້າ</label>
                    <div id="modal_car_display">
                        <div class="form-control bg-light text-muted" style="border-radius: 12px; padding: 0.75rem 1rem;">
                            ກະລຸນາພິມລະຫັດລູກຄ້າກ່ອນ...
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small">
                        <i class="fas fa-user-gear text-muted me-1"></i> ຊ່າງຜູ້ຮັບຜິດຊອບ
                    </label>
                    <select name="user_id" class="form-select bg-light shadow-sm" style="padding: 0.75rem 1rem;" required>
                        <option value="">-- ເລືອກຊ່າງແປງລົດ --</option>
                        <?php 
                        $users = mysqli_query($connect, "SELECT user_id, fname, lname FROM users WHERE status = 'ช่างแปลงรถ' OR status = 'ຊ່າງແປງລົດ' ORDER BY fname ASC");
                        if (mysqli_num_rows($users) > 0) {
                            while($u = mysqli_fetch_array($users)) {
                                echo "<option value='".$u['user_id']."'>".$u['fname']." ".$u['lname']."</option>";
                            }
                        } else {
                            $all_users = mysqli_query($connect, "SELECT user_id, fname, lname, status FROM users ORDER BY fname ASC");
                            while($u = mysqli_fetch_array($all_users)) {
                                echo "<option value='".$u['user_id']."'>".$u['fname']." ".$u['lname']." (".$u['status'].")</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small"><i class="fas fa-notes-medical text-muted me-1"></i> ອາການເບື້ອງຕົ້ນ / ວຽກທີ່ຕ້ອງເຮັດ</label>
                    <textarea name="symptoms" class="form-control bg-light shadow-sm" rows="3" style="border-radius: 12px; padding: 0.75rem 1rem;" required placeholder="ລະບຸອາການເພີ່ມເຕີມ ເຊັ່ນ: ປ່ຽນນ້ຳມັນເຄື່ອງ..."></textarea>
                </div>

                <hr class="text-muted my-4">

                <div class="row g-2">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="border-radius: 12px; padding: 0.75rem;">
                            <i class="fas fa-save me-1"></i> ບັນທຶກເປີດບິນ
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-light border w-100 fw-bold text-muted" data-bs-dismiss="modal" style="border-radius: 12px; padding: 0.75rem;">
                            ຍົກເລີກ
                        </button>
                    </div>
                </div>

            </div> 
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    var typingTimer;
    var doneTypingInterval = 400; // ໜ່ວງເວລາພິມໜ້ອຍໜຶ່ງເພື່ອລົດພາລະ Server

    $('#modal_cust_id').on('input', function() {
        clearTimeout(typingTimer);
        var custId = $(this).val().trim();
        
        if (custId !== '') {
            $('#modal_cust_name_display').removeClass('text-success text-danger').addClass('text-muted')
                .html('<i class="fas fa-spinner fa-spin"></i> ກຳລັງຄົ້ນຫາ...').show();
            typingTimer = setTimeout(fetchModalCarData, doneTypingInterval);
        } else {
            $('#modal_car_display').html('<div class="form-control bg-light text-muted" style="border-radius: 12px; padding: 0.75rem 1rem;">ກະລຸນາພິມລະຫັດລູກຄ້າກ່ອນ...</div>');
            $('#modal_cust_name_display').hide();
        }
    });

    function fetchModalCarData() {
        var custId = $('#modal_cust_id').val().trim();
        var carDisplay = $('#modal_car_display');
        var nameDisplay = $('#modal_cust_name_display');

        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: { action: 'get_customer_cars', cust_id: custId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // ສະແດງຊື່ລູກຄ້າ
                    nameDisplay.html('<span class="text-success"><i class="fas fa-check-circle"></i> ເຈົ້າຂອງລົດ: ' + response.customer.cust_name + '</span>');
                    
                    // ສ້າງ Dropdown ໃຫ້ເລືອກລົດ (ຮອງຮັບກໍລະນີລູກຄ້າມີລົດຫຼາຍຄັນ)
                    if (response.cars.length > 0) {
                        var selectHTML = '<select name="car_id" class="form-select border-primary shadow-sm" style="border-radius: 12px; padding: 0.75rem 1rem;" required>';
                        if(response.cars.length > 1) {
                            selectHTML += '<option value="">-- ກະລຸນາເລືອກທະບຽນລົດ --</option>';
                        }
                        response.cars.forEach(function(car) {
                            selectHTML += '<option value="' + car.car_id + '">' + car.car_plate + '</option>';
                        });
                        selectHTML += '</select>';
                        carDisplay.html(selectHTML);
                    } else {
                        carDisplay.html('<div class="form-control bg-light text-danger" style="border-radius: 12px; padding: 0.75rem 1rem;">ລູກຄ້າຄົນນີ້ຍັງບໍ່ມີລົດໃນລະບົບ</div>');
                    }
                } else {
                    nameDisplay.html('<span class="text-danger"><i class="fas fa-times-circle"></i> ບໍ່ພົບລະຫັດລູກຄ້ານີ້</span>');
                    carDisplay.html('<div class="form-control bg-light text-muted" style="border-radius: 12px; padding: 0.75rem 1rem;">ກະລຸນາພິມລະຫັດລູກຄ້າກ່ອນ...</div>');
                }
            },
            error: function() {
                nameDisplay.html('<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> ລະບົບຂັດຂ້ອງ ກະລຸນາລອງໃໝ່</span>');
            }
        });
    }
});
</script>

</body>
</html>
<?php
}    
?>