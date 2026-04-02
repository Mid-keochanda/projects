<?php
    include('../cennect_dbstock.php');
    $dis_id = $_GET['dis_id'];
    $delete = mysqli_query($connect, "delete from districts where dis_id='$dis_id'");
    if($delete){
        echo "<script>location='form_districts.php'</script>";
    }
?>