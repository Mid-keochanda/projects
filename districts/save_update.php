
<?php
    include("../cennect_dbstock.php");
    $pro_id = $_GET['pro_id'];
    $dis_id = $_GET['dis_id'];
    $dis_name = $_GET['dis_name'];
    $remark = $_GET['remark'];
    $update = mysqli_query($connect, "update districts set dis_name='$dis_name',remark='$remark',pro_id='$pro_id' where dis_id='$dis_id'");
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
        location='select_districts.php';
    },1500)
        </script>";
    };
?>
