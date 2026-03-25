<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "waste_db";

// ເຊື່ອມຕໍ່ແບບ mysqli (ແບບທີ່ໂປຣເຈັກເຈົ້າໃຊ້ເປັນຫຼັກ)
$connect = mysqli_connect($host, $user, $pass, $db);

// ກວດສອບການເຊື່ອມຕໍ່
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// ຕັ້ງຄ່າໃຫ້ຮອງຮັບພາສາລາວ
mysqli_set_charset($connect, "utf8");
?>