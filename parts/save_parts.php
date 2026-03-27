<?php
include("../cennect_dbstock.php"); // ກວດເບິ່ງວ່າຟາຍ connect ຢູ່ Folder ທາງນອກແທ້ບໍ່

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $part_name  = mysqli_real_escape_string($connect, $_POST['part_name']);
    $category   = mysqli_real_escape_string($connect, $_POST['category']);
    $unit       = mysqli_real_escape_string($connect, $_POST['unit']);
    $qty_stock  = $_POST['qty_stock'];
    $min_stock  = $_POST['min_stock'];
    $cost_price = $_POST['cost_price'];
    $sale_price = $_POST['sale_price'];
    $remark     = mysqli_real_escape_string($connect, $_POST['remark']);

    $sql = "INSERT INTO parts (part_name, category, unit, qty_stock, min_stock, cost_price, sale_price, remark, created_at) 
            VALUES ('$part_name', '$category', '$unit', '$qty_stock', '$min_stock', '$cost_price', '$sale_price', '$remark', NOW())";

    if (mysqli_query($connect, $sql)) {
        echo "<script>alert('ບັນທຶກສຳເລັດ'); window.location.href='select_parts.php';</script>";
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}
?>