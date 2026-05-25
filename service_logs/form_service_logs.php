<?php
include("../cennect_dbstock.php");
mysqli_set_charset($connect, "utf8");

// --- ດຶງຂໍ້ມູນລາຍການບິນສ້ອມແປງທັງໝົດ ---
$sql_logs = "SELECT l.*, c.car_plate, cust.cust_name, u.fname, u.lname,
            (SELECT SUM(total) FROM service_details WHERE service_id = l.log_id) as total_parts
            FROM service_logs l
            LEFT JOIN cars c ON l.car_id = c.car_id
            LEFT JOIN customers cust ON c.cust_id = cust.cust_id
            LEFT JOIN users u ON l.user_id = u.user_id
            ORDER BY l.log_id DESC";
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
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f7f6; }
        .card { border: none; border-radius: 15px; }
        .table thead { background-color: #4e73df; color: white; }
        .badge-pending { background-color: #f6c23e; color: #fff; }
        .badge-success { background-color: #1cc88a; color: #fff; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="text-primary fw-bold mb-0"><i class="fas fa-tools me-2"></i> ລະບົບຈັດການການສ້ອມແປງ</h4>
                <p class="text-muted mb-0">ຕາຕະລາງຕິດຕາມ ແລະ ເປີດບິນແປງລົດຂອງລູກຄ້າ</p>
            </div>
            <button type="button" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" 
                    data-bs-toggle="modal" data-bs-target="#openServiceModal" 
                    style="border-radius: 10px;">
                <i class="fas fa-plus-circle me-2"></i> ເປີດບິນສ້ອມແປງໃໝ່
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="80">ເລກບິນ</th>
                            <th>ທະບຽນລົດ (ເຈົ້າຂອງ)</th>
                            <th>ຊ່າງຜູ້ຮັບຜິດຊອບ</th>
                            <th>ອາການເບື້ອງຕົ້ນ</th>
                            <th class="text-end">ຍອດລວມສຸດທິ</th>
                            <th class="text-center">ສະຖານະ</th>
                            <th class="text-center" width="120">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        while($row = mysqli_fetch_array($res_logs)) { 
                            $parts_cost = $row['total_parts'] ?? 0;
                            $labor_cost = $row['labor_cost'] ?? 0;
                            $grand_total = $parts_cost + $labor_cost;
                        ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?php echo str_pad($row['log_id'], 5, "0", STR_PAD_LEFT); ?></td>
                            <td>
                                <span class="fw-bold"><?php echo $row['car_plate']; ?></span><br>
                                <small class="text-muted"><?php echo $row['cust_name'] ?? '---'; ?></small>
                            </td>
                            <td><?php echo $row['fname'] . " " . $row['lname']; ?></td>
                            <td><div class="text-truncate" style="max-width: 200px;"><?php echo $row['symptoms']; ?></div></td>
                            <td class="text-end fw-bold text-danger"><?php echo number_format($grand_total); ?> ກີບ</td>
                            <td class="text-center">
                                <?php if($row['status'] == 'pending'): ?>
                                    <span class="badge badge-pending px-3 py-2 rounded-pill">ກຳລັງສ້ອມແປງ</span>
                                <?php else: ?>
                                    <span class="badge badge-success px-3 py-2 rounded-pill">ສຳເລັດແລ້ວ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="form_service_details.php?id=<?php echo $row['log_id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-cog"></i> ຈັດການ
                                </a>
                            </td>
                        </tr>
                        <?php } if(mysqli_num_rows($res_logs) == 0) { ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">--- ບໍ່ມີຂໍ້ມູນບິນສ້ອມແປງໃນລະບົບ ---</td>
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
        <form action="insert_service_logs.php" method="POST" class="modal-content" style="border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 30px rgba(0,0,0,0.15);">
            <div class="modal-header text-white text-center d-block position-relative" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 1.5rem; border: none;">
                <h4 class="modal-title fw-bold mb-1" id="openServiceModalLabel">
                    <i class="fas fa-file-invoice me-2"></i> ເປີດບິນສ້ອມແປງໃໝ່
                </h4>
                <p class="text-white-50 small mb-0">ກະລຸນາປ້ອນຂໍ້ມູນຍານພາຫະນະ ແລະ ອາການເບື້ອງຕົ້ນ</p>
                <button type="button" class="btn-close btn-close-white position-absolute" style="top: 1.5rem; right: 1.5rem;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4" style="background-color: #fff;">
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small"><i class="fas fa-car text-muted me-1"></i> ເለືອກລົດ</label>
                    <select name="car_id" class="form-select" style="border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem 1rem;" required>
                        <option value="">-- ເລືອກທະບຽນລົດ --</option>
                        <?php 
                        $cars = mysqli_query($connect, "
                            SELECT c.car_id, c.car_plate, cust.cust_name 
                            FROM cars c 
                            LEFT JOIN customers cust ON c.cust_id = cust.cust_id 
                            ORDER BY c.car_plate ASC
                        ");
                        while($c = mysqli_fetch_array($cars)) {
                            $client_info = !empty($c['cust_name']) ? " (" . $c['cust_name'] . ")" : "";
                            echo "<option value='".$c['car_id']."'>".$c['car_plate'].$client_info."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
    <label class="form-label fw-bold text-dark small">
        <i class="fas fa-user-gear text-muted me-1"></i> ຊ່າງຜູ້ຮັບຜິດຊອບ
    </label>
    <select name="user_id" class="form-select" style="border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem 1rem;" required>
        <option value="">-- ເລືອກຊ່າງແປງລົດ --</option>
        <?php 
        // ດຶງສະເພາະພະນັກງານທີ່ມີສະຖານະເປັນ 'ຊ່າງແປງລົດ'
        $users = mysqli_query($connect, "SELECT user_id, fname, lname FROM users WHERE status = 'ຊ່າງແປງລົດ' ORDER BY fname ASC");
        
        if (mysqli_num_rows($users) > 0) {
            while($u = mysqli_fetch_array($users)) {
                echo "<option value='".$u['user_id']."'>".$u['fname']." ".$u['lname']."</option>";
            }
        } else {
            echo "<option value='' disabled>ບໍ່ພົບຂໍ້ມູນຊ່າງໃນລະບົບ</option>";
        }
        ?>
    </select>
</div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small"><i class="fas fa-notes-medical text-muted me-1"></i> ອາການເບື້ອງຕົ້ນ / ວຽກທີ່ຕ້ອງເຮັດ</label>
                    <textarea name="symptoms" class="form-control" rows="4" style="border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem 1rem;" required placeholder="ລະບຸອາການເພີ່ມເຕີມ ເຊັ່ນ: ປ່ຽນນ້ຳມັນເຄື່ອງ, ແອບໍ່ເຢັນ..."></textarea>
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
</body>
</html>