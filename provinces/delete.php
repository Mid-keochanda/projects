<?php
    include('../cennect_dbstock.php');
    $pro_id = $_GET['pro_id'];
    $delete = mysqli_query($connect, "delete from provinces where pro_id='$pro_id'");
    if($delete){
        echo "<script>location='select_provinces.php'</script>";
    }
?>