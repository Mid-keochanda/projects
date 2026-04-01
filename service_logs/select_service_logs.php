<?php 
include("../cennect_dbstock.php"); 
$search = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ລາຍການສ້ອມແປງທີ່ກຳລັງດຳເນີນການ</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; }
        .table-hover tbody tr:hover { background-color: #f1f4f9; transition: 0.3s; }
        .badge { font-weight: 500; padding: 0.5em 0.8em; }
        .btn-group .btn { border-radius: 6px; margin: 0 2px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="card border-0 shadow-sm p-4">
        
        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-6">
                <h4 class="text-primary m-0 fw-bold"><i class="fas fa-tools me-2"></i> ລາຍການສ້ອມແປງທີ່ກຳລັງດຳເນີນການ</h4>
                <p class="text-muted small mb-0">* ລາຍການຈະຫາຍໄປເມື່ອພິມໃບບິນສຳເລັດ</p>
            </div>
            <div class="col-md-6">
                <form action="" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="ຄົ້ນຫາ: ທະບຽນລົດ, ຊື່ລູກຄ້າ..." 
                               value="<?php echo $search; ?>">
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
                <thead class="table-light">
                    <tr>
                        <th width="80">ID</th>
                        <th>ທະບຽນລົດ</th>
                        <th>ຊື່ລູກຄ້າ</th>
                        <th>ຊື່ຊ່າງ</th>
                        <th>ອາການ</th>
                        <th>ວັນທີ</th>
                        <th class="text-center">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // --- 🌟 ສ່ວນທີ່ປັບປຸງ Query 🌟 ---
                    // ເພີ່ມເງື່ອນໄຂ (status != 'completed' OR status IS NULL)
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
                        <td>#<?php echo str_pad($row['log_id'], 5, "0", STR_PAD_LEFT); ?></td>
                        <td><span class="badge bg-dark fs-6"><?php echo $row['car_plate']; ?></span></td>
                        <td><?php echo $row['cust_name']; ?></td>
                        <td><?php echo $row['fname']; ?></td>
                        <td><small><?php echo $row['symptoms']; ?></small></td>
                        <td><?php echo date('d/m/Y', strtotime($row['service_date'])); ?></td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="form_service_details.php?id=<?php echo $row['log_id']; ?>" 
                                   class="btn btn-outline-primary btn-sm" title="ເພີ່ມອາໄຫຼ່">
                                   <i class="fas fa-cart-plus"></i>
                                </a>
                                <a href="print_service_logs.php?id=<?php echo $row['log_id']; ?>" 
                                   target="_blank" class="btn btn-info btn-sm text-white" title="ພິມໃບບິນ">
                                   <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center p-5 text-muted'>ບໍ່ມີລາຍການສ້ອມແປງທີ່ຄ້າງຢູ່ໃນລະບົບ</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // ຟັງຊັນ Refresh ໜ້າຈໍເມື່ອກັບມາຈາກ Tab ພິມໃບບິນ ເພື່ອໃຫ້ລາຍການຫາຍໄປທັນທີ
    window.addEventListener('focus', function() {
        location.reload();
    });
</script>

</body>
</html>