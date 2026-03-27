<?php 
include("../cennect_dbstock.php"); 

// 1. ກວດສອບ car_id ທີ່ສົ່ງມາ
if(isset($_GET['car_id'])) {
    $car_id = mysqli_real_escape_string($connect, $_GET['car_id']);
    
    // ດຶງຂໍ້ມູນລົດຄັນທີ່ຈະແກ້ໄຂ
    $sql_car = "SELECT * FROM cars WHERE car_id = '$car_id'";
    $res_car = mysqli_query($connect, $sql_car);
    $data = mysqli_fetch_array($res_car);
    
    if(!$data) { die("ບໍ່ພົບຂໍ້ມູນລົດຄັນນີ້!"); }
} else {
    header("Location: select_cars.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ແກ້ໄຂຂໍ້ມູນລົດ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8fafc; }
        .form-card { border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card form-card p-4">
                <h4 class="fw-bold mb-4 text-primary">ແກ້ໄຂຂໍ້ມູນລົດ</h4>
                
                <form action="save_cars.php" method="POST">
                    <input type="hidden" name="car_id" value="<?= $data['car_id'] ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ເລກທະບຽນ</label>
                            <input type="text" name="car_plate" class="form-control" value="<?= $data['car_plate'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ເຈົ້າຂອງລົດ (Customers)</label>
                            <select name="cust_id" class="form-select" required>
                                <option value="">--- ເລືອກເຈົ້າຂອງລົດ ---</option>
                                <?php 
                                // ດຶງລາຍຊື່ລູກຄ້າທັງໝົດມາສະແດງ
                                $sql_cust = "SELECT cust_id, cust_name, cust_surname FROM customers ORDER BY cust_name ASC";
                                $res_cust = mysqli_query($connect, $sql_cust);
                                while($cust = mysqli_fetch_array($res_cust)) {
                                    // ຖ້າ cust_id ກົງກັບຂໍ້ມູນເກົ່າ ໃຫ້ເລືອກ (selected)
                                    $selected = ($cust['cust_id'] == $data['cust_id']) ? "selected" : "";
                                    echo "<option value='".$cust['cust_id']."' $selected>".$cust['cust_name']." ".$cust['cust_surname']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">ຍີ່ຫໍ້</label>
                            <input type="text" name="car_brand" class="form-control" value="<?= $data['car_brand'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ລຸ້ນ</label>
                            <input type="text" name="car_model" class="form-control" value="<?= $data['car_model'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ສີ</label>
                            <input type="text" name="car_color" class="form-control" value="<?= $data['car_color'] ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">ໝາຍເຫດ</label>
                        <textarea name="remark" class="form-control" rows="3"><?= $data['remark'] ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">ບັນທຶກການແກ້ໄຂ</button>
                        <a href="select_cars.php" class="btn btn-light px-4">ຍົກເລີກ</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>