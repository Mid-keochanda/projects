<?php
include("../connect.php");
$name_la = $_POST['name_la'];
$name_en = $_POST['name_en'];
$is_recyclable = $_POST['is_recyclable'];

$insert = mysqli_query($conn, "INSERT INTO categories (name_la, name_en, is_recyclable) VALUES ('$name_la', '$name_en', '$is_recyclable')");

if($insert){
    echo "<script>
        Swal.fire('ບັນທຶກສຳເລັດ', '', 'success').then(() => {
            location='select_categories.php';
        });
    </script>";
}
?>