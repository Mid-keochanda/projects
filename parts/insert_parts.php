<?php
include("../cennect_dbstock.php");

if(isset($_POST['part_name'])) {
    $part_name = $_POST['part_name'];
    $category  = $_POST['category'];
    $qty_stock = $_POST['qty_stock'];
    $min_stock = $_POST['min_stock'];
    $unit      = $_POST['unit'];
    $cost_price = $_POST['cost_price'];
    $sale_price = $_POST['sale_price'];
    $remark    = $_POST['remark'];

    // SQL Insert ໂດຍໃຊ້ NOW() ເພື່ອບັນທຶກເວລາປັດຈຸບັນ
    $sql = "INSERT INTO parts (part_name, category, qty_stock, min_stock, unit, cost_price, sale_price, remark, created_at) 
            VALUES ('$part_name', '$category', '$qty_stock', '$min_stock', '$unit', '$cost_price', '$sale_price', '$remark', NOW())";

    if(mysqli_query($connect, $sql)) {
        // ເມື່ອບັນທຶກສຳເລັດ ໃຫ້ດີດກັບໄປໜ້າຕາຕະລາງທັນທີ
        header("location:form_parts.php?save=success");
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}
?>