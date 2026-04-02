<?php
include("../cennect_dbstock.php");

// ຮັບຄ່າຈາກ Form
$part_name  = $_POST['part_name'];
$category   = $_POST['category'];
$qty_stock  = $_POST['qty_stock'];
$unit       = $_POST['unit'];
$min_stock  = $_POST['min_stock'];
$cost_price = $_POST['cost_price'];
$sale_price = $_POST['sale_price'];
$remark     = $_POST['remark']; 

$sql = "INSERT INTO parts (part_name, category, qty_stock, unit, min_stock, cost_price, sale_price, remark, created_at) 
        VALUES ('$part_name', '$category', '$qty_stock', '$unit', '$min_stock', '$cost_price', '$sale_price', '$remark', NOW())";

if(mysqli_query($connect, $sql)) {
    header("location: form_parts.php?status=success");
} else {
    header("location: form_parts.php?status=error");
}
?>