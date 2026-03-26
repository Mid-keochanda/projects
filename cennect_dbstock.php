<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "garage_db"; // ຫຼື waste_db ຕາມທີ່ເຈົ້າຕັ້ງ

// ຕ້ອງຊື່ $connect ເພື່ອໃຫ້ຕົງກັບທີ່ໃຊ້ໃນ update.php
$connect = mysqli_connect($servername, $username, $password, $dbname);

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
?>