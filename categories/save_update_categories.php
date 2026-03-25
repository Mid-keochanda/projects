<?php
include("../cennect_dbstock.php");

$id = $_POST['id'];
$name_la = $_POST['name_la'];
$name_en = $_POST['name_en'];
$is_recyclable = $_POST['is_recyclable'];

// ໃຊ້ $connect ໃຫ້ກົງກັບໄຟລ໌ເຊື່ອມຕໍ່
$update = mysqli_query($connect, "UPDATE categories SET 
    name_la = '$name_la', 
    name_en = '$name_en', 
    is_recyclable = '$is_recyclable' 
    WHERE id = '$id'");

if($update){
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            location='select_categories.php';
        });
    </script>";
} else {
    echo "<script>Swal.fire('ເກີດຂໍ້ຜິດພາດ', '" . mysqli_error($connect) . "', 'error');</script>";
}
?>