<?php
    include('../cennect_dbstock.php');
    $vill_id = $_GET['vill_id'];
    $delete = mysqli_query($connect, "delete from villages where vill_id='$vill_id'");
    if($delete){
        echo "<script>location='select_villages.php'</script>";
    }
?>