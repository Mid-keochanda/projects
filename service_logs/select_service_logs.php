<?php 
include("../cennect_dbstock.php"); 
$search = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ລາຍການສ້ອມແປງທີ່ກຳລັງດຳເນີນການ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f7f6; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-hover tbody tr:hover { background-color: #f1f4f9; transition: 0.2s; }
        .badge-plate { font-weight: 700; padding: 0.6em 1em; border-radius: 6px; letter-spacing: 0.5px; }
        .btn-action { border-radius: 8px; width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; margin: 0 2px; }
        .table thead { background-color: #4e73df; color: white; }
        .table th { background-color: #4361ee !important; color: white !important; font-weight: 600; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="card p-4">
        
        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-6">
                <h4 class="text-primary m-0 fw-bold"><i class="fas fa-tools me-2"></i> ລາຍການສ້ອມແປງທີ່ກຳລັງດຳເນີນການ</h4>
                <p class="text-muted small mb-0 mt-1">* ລາຍການຈະຫາຍໄປຈາກໜ້ານີ້ອັດຕະໂນມັດ ເມື່ອເປີດໜ້າພິມໃບບິນສຳເລັດ</p>
            </div>
            <div class="col-md-6">
                <form action="" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="ຄົ້ນຫາ: ທະບຽນລົດ, ຊື່ລູກຄ້າ, ອາການ..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> ຄົ້ນຫາ
                        </button>
                        <?php if($search != ''): ?>
                            <a href="?" class="btn btn-outline-secondary">ລ້າງຄ່າ</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-center">
                        <th width="90">ເລກບິນ</th>
                        <th width="140">ທະບຽນລົດ</th>
                        <th class="text-start">ຊື່ລູກຄ້າ</th>
                        <th class="text-start">ຊ່າງຜູ້ຮັບຜິດຊອບ</th>
                        <th class="text-start">ອາການເບື້ອງຕົ້ນ</th>
                        <th width="180">ວັນທີ-ເວລາບັນທຶກ</th>
                        <th width="120">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL Query ດຶງຂໍ້ມູນບິນທີ່ຍັງບໍ່ທັນໄດ້ປິດ (status ບໍ່ແມ່ນ completed)
                    $sql = "SELECT l.*, c.car_plate, cust.cust_name, u.fname, u.lname 
                            FROM service_logs l
                            JOIN cars c ON l.car_id = c.car_id
                            JOIN customers cust ON c.cust_id = cust.cust_id
                            JOIN users u ON l.user_id = u.user_id
                            WHERE (l.status != 'completed' OR l.status IS NULL)";

                    if ($search != '') {
                        $sql .= " AND (c.car_plate LIKE '%$search%' 
                                  OR cust.cust_name LIKE '%$search%' 
                                  OR l.symptoms LIKE '%$search%')";
                    }

                    $sql .= " ORDER BY l.log_id DESC";
                    $res = mysqli_query($connect, $sql);
                    
                    if($res && mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_array($res)) {
                    ?>
                    <tr>
                        <td class="text-center fw-bold text-secondary">
                            #<?php echo str_pad($row['log_id'], 5, "0", STR_PAD_LEFT); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-dark badge-plate fs-6"><?php echo $row['car_plate']; ?></span>
                        </td>
                        <td class="text-start fw-bold"><?php echo $row['cust_name']; ?></td>
                        <td class="text-start"><i class="fas fa-user-nut me-1 text-muted"></i> <?php echo $row['fname'] . " " . $row['lname']; ?></td>
                        <td class="text-start text-danger text-truncate" style="max-width: 200px;">
                            <small><?php echo !empty($row['symptoms']) ? $row['symptoms'] : '---'; ?></small>
                        </td>
                        <td class="text-center text-muted small">
                            <i class="far fa-clock me-1"></i>
                            <?php echo date('d/m/Y H:i', strtotime($row['service_date'])); ?> 
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <a href="form_service_details.php?id=<?php echo $row['log_id']; ?>" 
                                   class="btn btn-primary btn-action" data-bs-toggle="tooltip" title="ເພີ່ມອາໄຫຼ່ / ໃສ່ຄ່າແຮງ">
                                    <i class="fas fa-cart-plus"></i>
                                </a>
                                
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center p-5 text-muted'><i class='fas fa-folder-open fa-2x mb-2 d-block text-black-50'></i>ບໍ່ມີລາຍການສ້ອມແປງທີ່ກຳລັງດຳເນີນການໃນລະບົບ</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ເປີດໃຊ້ງານ Tooltip ຂອງ Bootstrap 5 ເພື່ອຄວາມສວຍງາມເວລາເອົາເມົ້າສ໌ໄປຊີ້ໃສ່ປຸ່ມ
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // ຟັງຊັນ Auto Refresh ໜ້າຈໍເມື່ອກັບມາຈາກ Tab ພິມໃບບິນ ເພື່ອໃຫ້ລາຍການທີ່ປິດບິນແລ້ວຫາຍໄປທັນທີ
    window.addEventListener('focus', function() {
        location.reload();
    });
</script>

</body>
</html>