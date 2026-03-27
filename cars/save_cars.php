<?php
include("../cennect_dbstock.php");
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <script src="../jquery.js"></script>
    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script> 
    <style>*{ font-family:'phetsarath ot'; }</style>
</head>
<body>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['car_id'])) {
    
    // 1. ຮັບຄ່າ ແລະ ປ້ອງກັນ SQL Injection
    $car_id    = mysqli_real_escape_string($connect, $_POST['car_id']);
    $cust_id   = mysqli_real_escape_string($connect, $_POST['cust_id']);
    $car_plate = mysqli_real_escape_string($connect, $_POST['car_plate']);
    $car_brand = mysqli_real_escape_string($connect, $_POST['car_brand']);
    $car_model = mysqli_real_escape_string($connect, $_POST['car_model']);
    $car_color = mysqli_real_escape_string($connect, $_POST['car_color']);
    $remark    = mysqli_real_escape_string($connect, $_POST['remark']);

    // 2. ກວດສອບກ່ອນວ່າ cust_id ນີ້ມີຢູ່ໃນລະບົບແທ້ຫຼືບໍ່ (ເພື່ອປ້ອງກັນ Foreign Key Error)
    $check_customer = mysqli_query($connect, "SELECT cust_id FROM customers WHERE cust_id = '$cust_id'");
    
    if (mysqli_num_rows($check_customer) == 0) {
        // ຖ້າບໍ່ມີ ID ລູກຄ້ານີ້ ໃຫ້ແຈ້ງເຕືອນ
        echo "<script>
            Swal.fire({
                title: 'ບໍ່ພົບຂໍ້ມູນລູກຄ້າ!',
                text: 'ກະລຸນາເລືອກເຈົ້າຂອງລົດທີ່ຖືກຕ້ອງ',
                icon: 'warning'
            }).then(function() { window.history.back(); });
        </script>";
        exit();
    }

    // 3. ຄຳສັ່ງ SQL Update
    $sql = "UPDATE cars SET 
            car_plate = '$car_plate', 
            cust_id   = '$cust_id',
            car_brand = '$car_brand', 
            car_model = '$car_model', 
            car_color = '$car_color', 
            remark    = '$remark',
            updated_at = NOW() 
            WHERE car_id = '$car_id'";

    if (mysqli_query($connect, $sql)) {
        echo "<script>
            Swal.fire({
                title: 'ສຳເລັດ!',
                text: 'ແກ້ໄຂຂໍ້ມູນລົດຮຽບຮ້ອຍແລ້ວ',
                icon: 'success',
                confirmButtonColor: '#4361ee'
            }).then(function() { window.location.href = 'select_cars.php'; });
        </script>";
    } else {
        $error_msg = mysqli_error($connect);
        echo "<script>
            Swal.fire({
                title: 'ຜິດພາດ!',
                text: 'Error: $error_msg',
                icon: 'error'
            }).then(function() { window.history.back(); });
        </script>";
    }
} else {
    header("Location: select_cars.php");
    exit();
}
?>
</body>
</html>