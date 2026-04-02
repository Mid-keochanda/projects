<?php
    include('../cennect_dbstock.php');
    $car_id = $_GET['car_id'];
    $delete = mysqli_query($connect, "delete from cars where car_id='$car_id'");
    if($delete){
        echo "<script>location='form_cars.php'</script>";
    }
?>