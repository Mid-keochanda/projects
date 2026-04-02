<?php
include("../cennect_dbstock.php");

$vill_id   = mysqli_real_escape_string($connect, $_GET['vill_id']);
$dis_id    = mysqli_real_escape_string($connect, $_GET['dis_id']);
$vill_name = mysqli_real_escape_string($connect, $_GET['vill_name']);
$remark    = mysqli_real_escape_string($connect, $_GET['remark']);

$sql = "UPDATE villages SET 
            dis_id = '$dis_id', 
            vill_name = '$vill_name', 
            remark = '$remark' 
        WHERE vill_id = '$vill_id'";

$query = mysqli_query($connect, $sql);

if($query){
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ',
            showConfirmButton: false,
            timer: 1500
        }).then(() => { location.reload(); });
    </script>";
} else {
    echo "Error: " . mysqli_error($connect);
}
?>