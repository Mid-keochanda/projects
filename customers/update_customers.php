<?php 
include("../cennect_dbstock.php"); 

// ກວດສອບ ID ທີ່ສົ່ງມາຈາກໜ້າລາຍງານ
if(isset($_GET['cust_id'])){
    $id = mysqli_real_escape_string($connect, $_GET['cust_id']);
    $sql = mysqli_query($connect, "SELECT * FROM customers WHERE cust_id = '$id'");
    $row = mysqli_fetch_array($sql);
    
    // ຖ້າບໍ່ມີຂໍ້ມູນ ID ນີ້ໃນ DB ໃຫ້ດີດກັບ
    if(!$row){ header("Location: select_customers.php"); exit; }
} else {
    header("Location: select_customers.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ແກ້ໄຂຂໍ້ມູນລູກຄ້າ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Noto Sans Lao', sans-serif; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .header-gradient { background: linear-gradient(45deg, #4e73df, #224abe); color: white; border-radius: 20px 20px 0 0 !important; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header header-gradient py-3 text-center">
                        <h4 class="mb-0 fw-bold">ແກ້ໄຂຂໍ້ມູນລູກຄ້າ</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="save_customers.php" method="POST">
                            <input type="hidden" name="cust_id" value="<?= $row['cust_id'] ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ຊື່ລູກຄ້າ</label>
                                    <input type="text" name="cust_name" class="form-control" value="<?= $row['cust_name'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">ນາມສະກຸນ</label>
                                    <input type="text" name="cust_surname" class="form-control" value="<?= $row['cust_surname'] ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ເພດ</label>
                                <select name="gender" class="form-select">
                                    <option value="ຊາຍ" <?= ($row['gender'] == 'ຊາຍ') ? 'selected' : '' ?>>ຊາຍ</option>
                                    <option value="ຍິງ" <?= ($row['gender'] == 'ຍິງ') ? 'selected' : '' ?>>ຍິງ</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ເບີໂທລະສັບ</label>
                                <input type="text" name="tel" class="form-control" value="<?= $row['tel'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ທີ່ຢູ່</label>
                                <textarea name="address" class="form-control" rows="3"><?= $row['address'] ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">ໝາຍເຫດ</label>
                                <input type="text" name="remark" class="form-control" value="<?= $row['remark'] ?>">
                            </div>
                            
                            <hr>
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">ບັນທຶກການແກ້ໄຂ</button>
                                <a href="select_customers.php" class="btn btn-outline-secondary w-100 rounded-pill py-2">ຍົກເລີກ</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>