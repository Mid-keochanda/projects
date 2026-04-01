<?php
include("../cennect_dbstock.php");

$pro_id = $_GET['pro_id'];
$dis_id = $_GET['dis_id'];
$vill_name = $_GET['vill_name'];
$remark = $_GET['remark'];

// ກວດສອບຄ່າວ່າງກ່ອນ insert
if(!empty($vill_name) && !empty($dis_id)){
    $sql = "INSERT INTO villages (vill_name, dis_id, pro_id, remark) 
            VALUES ('$vill_name', '$dis_id', '$pro_id', '$remark')";
    
    if(mysqli_query($connect, $sql)){
        echo "success";
    } else {
        echo "error: " . mysqli_error($connect);
    }
}
?>