<?php
session_start();
include("../cennect_dbstock.php");

if(isset($_POST['username'])){
    // ຮັບຄ່າ ແລະ ປ້ອງກັນ SQL Injection
    $fname    = mysqli_real_escape_string($connect, $_POST['fname']);
    $lname    = mysqli_real_escape_string($connect, $_POST['lname']);
    $gender   = mysqli_real_escape_string($connect, $_POST['gender']);
    $dob      = mysqli_real_escape_string($connect, $_POST['dob']);
    $tel      = mysqli_real_escape_string($connect, $_POST['tel']);
    $vill_id  = mysqli_real_escape_string($connect, $_POST['vill_id']);
    $status   = mysqli_real_escape_string($connect, $_POST['status']);
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $password = mysqli_real_escape_string($connect, $_POST['password']); 
    $remark   = mysqli_real_escape_string($connect, $_POST['remark']);

    // ຄຳສັ່ງ Insert (ກວດສອບຊື່ Column ໃນ Database ຂອງເຈົ້າໃຫ້ກົງກັນ)
    $sql = "INSERT INTO users (fname, lname, gender, dob, tel, vill_id, status, username, password, remark) 
            VALUES ('$fname', '$lname', '$gender', '$dob', '$tel', '$vill_id', '$status', '$username', '$password', '$remark')";
    
    if(mysqli_query($connect, $sql)){
        echo "success";
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}
?>