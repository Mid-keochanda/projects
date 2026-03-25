<?php
include("../connect.php");
$id = $_GET['id'];
$delete = mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");
if($delete){
    header("location:select_categories.php");
}
?>