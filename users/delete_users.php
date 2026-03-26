<?php
    include("../cennect_dbstock.php");
    $user_id = $_GET['user_id'];
    $delete = mysqli_query($connect, "delete from users where user_id='$user_id'");
    if($delete){
        echo "<script>location='select_form_users.php'</script>";
    }
?>