<?php 
include("../cennect_dbstock.php"); 

// ກວດສອບວ່າ ມີການສົ່ງ car_id ມາຫຼືບໍ່
if(isset($_GET['car_id'])) {
    $car_id = mysqli_real_escape_string($connect, $_GET['car_id']);
    $sql = "SELECT * FROM cars WHERE car_id = '$car_id'";
    $result = mysqli_query($connect, $sql);
    $row = mysqli_fetch_array($result);
    
    if(!$row) {
        header("Location: select_cars.php");
        exit();
    }
} else {
    header("Location: select_cars.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ແກ້ໄຂຂໍ້ມູນລົດ - Garage Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f7fa; }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h4 class="mb-4 text-primary"><i class="fas fa-edit me-2"></i> ແກ້ໄຂຂໍ້ມູນລົດ</h4>
                <form action="save_cars.php" method="POST">
                    <input type="hidden" name="car_id" value="<?= $row['car_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">ເລກທະບຽນ</label>
                        <input type="text" name="car_plate" class="form-control" value="<?= htmlspecialchars($row['car_plate']) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ຍີ່ຫໍ້</label>
                            <input type="text" name="car_brand" class="form-control" value="<?= htmlspecialchars($row['car_brand']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ລຸ້ນ</label>
                            <input type="text" name="car_model" class="form-control" value="<?= htmlspecialchars($row['car_model']) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ສີລົດ</label>
                        <input type="text" name="car_color" class="form-control" value="<?= htmlspecialchars($row['car_color']) ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">ໝາຍເຫດ</label>
                        <textarea name="remark" class="form-control" rows="2"><?= htmlspecialchars($row['remark']) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="btnUpdate" class="btn btn-primary w-100">ບັນທຶກການແກ້ໄຂ</button>
                        <a href="select_cars.php" class="btn btn-light w-100">ຍົກເລີກ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>