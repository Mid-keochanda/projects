<?php
include("../cennect_dbstock.php"); 
if(isset($_GET['pro_id'])) {
    $pro_id = mysqli_real_escape_string($connect, $_GET['pro_id']);
    $pro_name = mysqli_real_escape_string($connect, $_GET['pro_name']);
    $remark = mysqli_real_escape_string($connect, $_GET['remark']);

    $update = mysqli_query($connect, "UPDATE provinces SET pro_name='$pro_name', remark='$remark' WHERE pro_id='$pro_id'");
    if($update) {
        echo "<script>
            Swal.fire({ icon: 'success', title: 'ແກ້ໄຂສຳເລັດ!', showConfirmButton: false, timer: 1500 })
            .then(() => { location.reload(); });
        </script>";
    }
}
?>