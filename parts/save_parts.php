<?php
include("../cennect_dbstock.php");

$part_id    = $_POST['part_id'];
$part_name  = $_POST['part_name'];
$category   = $_POST['category'];
$qty_stock  = $_POST['qty_stock'];
$unit       = $_POST['unit'];
$min_stock  = $_POST['min_stock'];
$cost_price = $_POST['cost_price'];
$sale_price = $_POST['sale_price'];
$remark     = $_POST['remark'];

$sql = "UPDATE parts SET 
        part_name = '$part_name', 
        category = '$category', 
        qty_stock = '$qty_stock', 
        unit = '$unit', 
        min_stock = '$min_stock', 
        cost_price = '$cost_price', 
        sale_price = '$sale_price',
        remark = '$remark',    
        updated_at = NOW()    
        WHERE part_id = '$part_id'";

if(mysqli_query($connect, $sql)) {
    header("location: form_parts.php?status=success");
} else {
    header("location: form_parts.php?status=error");
}
?>