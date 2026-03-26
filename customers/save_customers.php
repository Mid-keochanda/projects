<?php
include("../cennect_dbstock.php");
?>
<!DOCTYPE html>
<html lang="lo">
<head>
      <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../icon/css/all.min.css">
    <script src="../jquery.js"></script>
    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script> 
     <style>*{
        font-family:'phetsarath ot';
    }</style>
</head>
<body>

<?php
// ກວດສອບວ່າມີການສົ່ງຂໍ້ມູນມາແບບ POST ຫຼື ບໍ່
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cust_id'])) {
    
    // ຮັບຄ່າ ແລະ Clean ຂໍ້ມູນກັນ SQL Injection
    $id      = mysqli_real_escape_string($connect, $_POST['cust_id']);
    $name    = mysqli_real_escape_string($connect, $_POST['cust_name']);
    $surname = mysqli_real_escape_string($connect, $_POST['cust_surname']);
    $gender  = mysqli_real_escape_string($connect, $_POST['gender']);
    $tel     = mysqli_real_escape_string($connect, $_POST['tel']);
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $remark  = mysqli_real_escape_string($connect, $_POST['remark']);

    // ຄຳສັ່ງ SQL Update
    $sql = "UPDATE customers SET 
                cust_name = '$name', 
                cust_surname = '$surname', 
                gender = '$gender', 
                tel = '$tel', 
                address = '$address', 
                remark = '$remark',
                updated_at = NOW() 
            WHERE cust_id = '$id'";

    if (mysqli_query($connect, $sql)) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'ສຳເລັດ!',
                text: 'ຂໍ້ມູນລູກຄ້າຖືກອັບເດດແລ້ວ',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = 'select_customers.php';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ຜິດພາດ!',
                text: 'ບໍ່ສາມາດບັນທຶກໄດ້: " . mysqli_error($connect) . "'
            });
        </script>";
    }
} else {
    // ຖ້າບໍ່ມີການສົ່ງຂໍ້ມູນມາ ໃຫ້ກັບໄປໜ້າລາຍງານ
    header("Location: select_customers.php");
    exit;
}
?>
</body>
</html>