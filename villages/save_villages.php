<?php
    include("../cennect_dbstock.php");
   $pro_id = $_GET['pro_id'];
   $vil_id = $_GET['vil_id'];
    $dis_id = $_GET['dis_id'];
    $vil_name = $_GET['vil_name'];
    $remark = $_GET['remark'];
  $c= mysqli_query($connect, "update villages set pro_id='$pro_id',dis_id='$dis_id',vil_name='$vil_name',remark='$remark' where vil_id='$vil_id'");
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