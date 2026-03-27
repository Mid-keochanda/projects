<?php
    include('../cennect_dbstock.php');
    $part_id = $_GET['part_id'];
    $delete = mysqli_query($connect, "delete from parts where part_id='$part_id'");
    if($delete){
        echo "<script>location='select_parts.php'</script>";
    }
?>