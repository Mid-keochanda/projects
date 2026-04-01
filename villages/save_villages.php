<?php
    include("../cennect_dbstock.php");
   $pro_id = $_GET['pro_id'];
   $vill_id = $_GET['vill_id'];
    $dis_id = $_GET['dis_id'];
    $vill_name = $_GET['vill_name'];
    $remark = $_GET['remark'];
  $c= mysqli_query($connect, "update villages set pro_id='$pro_id',dis_id='$dis_id',vill_name='$vill_name',remark='$remark' where vill_id='$vill_id'");
if($c){
    echo    "<script>Swal.fire({
        icon: 'success', //error, warning, success, question
        title: 'ສຳເລັດ',//ຫົວຂໍ້
    })
        window.setTimeout(function(){
        location='form_villages.php';
},1500)
    </script>";
}
?>     