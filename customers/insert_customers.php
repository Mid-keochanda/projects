<?php
include("../cennect_dbstock.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = mysqli_real_escape_string($connect, $_POST['cust_name']);
    $surname = mysqli_real_escape_string($connect, $_POST['cust_surname']);
    $gender  = mysqli_real_escape_string($connect, $_POST['gender']);
    $tel     = mysqli_real_escape_string($connect, $_POST['tel']);
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $remark  = mysqli_real_escape_string($connect, $_POST['remark']);

    $sql = "INSERT INTO customers (cust_name, cust_surname, gender, tel, address, remark) 
            VALUES ('$name', '$surname', '$gender', '$tel', '$address', '$remark')";

    if (mysqli_query($connect, $sql)) {
        echo "success";
    } else {
        echo "ເກີດຂໍ້ຜິດພາດ: " . mysqli_error($connect);
    }
}
?>