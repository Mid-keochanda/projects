<?php
include("../cennect_dbstock.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id      = mysqli_real_escape_string($connect, $_POST['cust_id']);
    $name    = mysqli_real_escape_string($connect, $_POST['cust_name']);
    $surname = mysqli_real_escape_string($connect, $_POST['cust_surname']);
    $gender  = mysqli_real_escape_string($connect, $_POST['gender']);
    $tel     = mysqli_real_escape_string($connect, $_POST['tel']);
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $remark  = mysqli_real_escape_string($connect, $_POST['remark']);

    $sql = "UPDATE customers SET 
            cust_name = '$name', 
            cust_surname = '$surname', 
            gender = '$gender', 
            tel = '$tel', 
            address = '$address', 
            remark = '$remark' 
            WHERE cust_id = '$id'";

    if (mysqli_query($connect, $sql)) {
        echo "success";
    } else {
        echo "ບໍ່ສາມາດອັບເດດໄດ້: " . mysqli_error($connect);
    }
}
?>