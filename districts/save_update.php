<?php
include("../cennect_dbstock.php");

if(isset($_GET['dis_id'])){
    $dis_id = mysqli_real_escape_string($connect, $_GET['dis_id']);
    $pro_id = mysqli_real_escape_string($connect, $_GET['pro_id']);
    $dis_name = mysqli_real_escape_string($connect, $_GET['dis_name']);
    $remark = mysqli_real_escape_string($connect, $_GET['remark']);

    $update = mysqli_query($connect, "UPDATE districts SET pro_id='$pro_id', dis_name='$dis_name', remark='$remark' WHERE dis_id='$dis_id'");

    if($update){
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'ແກ້ໄຂສຳເລັດ',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location.reload();
            });
        </script>";
    } else {
        echo "<script>Swal.fire('ຜິດພາດ', 'ບໍ່ສາມາດອັບເດດໄດ້', 'error');</script>";
    }
}
?>