<?php
    include("../cennect_dbstock.php");
    $user_id = $_POST['user_id'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $tel = $_POST['tel'];
    $pro_id = $_POST['pro_id'];
    $dis_id = $_POST['dis_id'];
    $vil_id = $_POST['vil_id'];
    $status = $_POST['status'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remark = $_POST['remark'];
    $update = mysqli_query($connect, "update users set fname='$fname',lname='$lname',gender='$gender',dob='$dob',tel='$tel',pro_id='$pro_id',dis_id='$dis_id',vil_id='$vil_id',status='$status',username='$username',password='$password',remark='$remark' where user_id='$user_id'");
    if($update){
        echo "<script>
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'ການເເກ້ໄຂຂໍ້ມູນສຳເລັດ!',
            showConfirmButton: false,
            timer: 1500,
        })
        window.setTimeout(function(){
        location='select_form_users.php';
    },1500)
        </script>";
    }else{
        echo "NO";
    }
?>  