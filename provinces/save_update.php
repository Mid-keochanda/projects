<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../icon/css/all.min.css">
    <script src="../jquery.js"></script>
    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script> 
    <title>Document</title>
    <style>*{
        font-family:'phetsarath ot';
    }</style>
    </head>
    <body>
<?php
    include("../cennect_dbstock.php");
    $pro_id = $_GET['pro_id'];
    $pro_name = $_GET['pro_name'];
    $remark = $_GET['remark'];
    $update = mysqli_query($connect, "update provinces set pro_name='$pro_name',remark='$remark' where pro_id='$pro_id'");
    if($update){
        echo "<script>
        Swal.fire({
        position:'center',
        icon:'success',
        title:'ການແກ້ໄຂຂໍ້ມູນສຳເລັດ !',
        showConfirmButton: false,
        timer:1500
    })
        window.setTimeout(function(){
        location='select_provinces.php';
    },1500)
        </script>";
    };
?>
</body>
</html>