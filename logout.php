<?php
session_start();//ປະກາດໃຊ້ຕົວປ່ຽນ session
include("cennect_dbstock.php");
unset($_SESSION['fname']);
unset($_SESSION['lname']);
unset($_SESSION['user_id']);
unset($_SESSION['check']);

session_destroy();
header("location:index.php");
exit;
?>