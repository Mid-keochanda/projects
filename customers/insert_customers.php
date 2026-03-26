<?php
include("../cennect_dbstock.php");

// ຮັບຄ່າຈາກຟອມ
$cust_name = $_POST['cust_name'];
$cust_surname = $_POST['cust_surname'];
$gender = $_POST['gender'];
$tel = $_POST['tel'];
$address = $_POST['address'];
$remark = isset($_POST['remark']) ? $_POST['remark'] : ""; 

$sql = "INSERT INTO customers (cust_name, cust_surname, gender, tel, address, remark) 
        VALUES ('$cust_name', '$cust_surname', '$gender', '$tel', '$address', '$remark')";

if(mysqli_query($connect, $sql)) {
    echo "success";
} else {
    echo mysqli_error($connect);
}
?>