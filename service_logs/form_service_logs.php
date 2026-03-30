<?php include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <style>body { font-family: 'Phetsarath OT', sans-serif; }</style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 500px;">
        <div class="card-header bg-primary text-white text-center">
            <h5 class="mb-0">ເປີດບິນສ້ອມແປງໃໝ່</h5>
        </div>
        <div class="card-body p-4">
            <form action="insert_service_logs.php" method="POST">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">ເລືອກລົດ</label>
                    <select name="car_id" class="form-select" required>
                        <option value="">-- ເລືອກທະບຽນລົດ --</option>
                        <?php 
                        $cars = mysqli_query($connect, "SELECT car_id, car_plate FROM cars");
                        while($c = mysqli_fetch_array($cars)) {
                            echo "<option value='".$c['car_id']."'>".$c['car_plate']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">ຊ່າງຜູ້ຮັບຜິດຊອບ</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- ເລືອກຊ່າງໃນລະບົບ --</option>
                        <?php 
                        $users = mysqli_query($connect, "SELECT user_id, fname FROM users");
                        if(mysqli_num_rows($users) > 0) {
                            while($u = mysqli_fetch_array($users)) {
                                echo "<option value='".$u['user_id']."'>".$u['fname']."</option>";
                            }
                        } else {
                            echo "<option value='' disabled>--- ບໍ່ມີຂໍ້ມູນຊ່າງໃນລະບົບ ---</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">ອາການເບື້ອງຕົ້ນ</label>
                    <textarea name="symptoms" class="form-control" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">ບັນທຶກເປີດບິນ</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>