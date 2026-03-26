<?php
    include('../cennect_dbstock.php');
    $cust_id = $_GET['cust_id'];
    $delete = mysqli_query($connect, "delete from customers where cust_id='$cust_id'");
    if($delete){
        echo "<script>location='select_customers.php'</script>";
    }
?>