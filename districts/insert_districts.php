<?php
include("../cennect_dbstock.php");

if(isset($_GET['dis_name'])){
    $pro_id = mysqli_real_escape_string($connect, $_GET['pro_id']);
    $dis_name = mysqli_real_escape_string($connect, $_GET['dis_name']);
    $remark = mysqli_real_escape_string($connect, $_GET['remark']);

    $insert = mysqli_query($connect, "INSERT INTO districts (pro_id, dis_name, remark) VALUES ('$pro_id', '$dis_name', '$remark')");

    if($insert){
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'ບັນທຶກສຳເລັດ',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location.reload();
            });
        </script>";
    } else {
        echo "<script>Swal.fire('ຜິດພາດ', 'ບໍ່ສາມາດບັນທຶກໄດ້', 'error');</script>";
    }
}
?>