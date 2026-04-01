<?php
    include("../cennect_dbstock.php");
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $tel = $_POST['tel'];
    $pro_id = $_POST['pro_id'];
    $dis_id = $_POST['dis_id'];
    $vill_id = $_POST['vill_id'];
    $status = $_POST['status'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remark = $_POST['remark'];
    
    $sql = mysqli_query($connect, "select *from users where fname='$fname'");
    $check = mysqli_num_rows($sql);
    if($check <> 0){
        echo    "<script>Swal.fire({
            icon: 'error', //error, warning, success, question
            title: 'ຊື່ນີ້ມີຢູ່ເເລ້ວ!',//ຫົວຂໍ້
        });
        </script>";
    }else{
        $insert = mysqli_query($connect, "insert into users(fname,lname,gender,dob,tel,pro_id,dis_id,vill_id,status,username,password,remark)
        value('$fname','$lname','$gender','$dob','$tel','$pro_id','$dis_id','$vill_id','$status','$username',password('$password'),'$remark')");
        if($insert){
            echo"<script>Swal.fire({
                postion: 'top',
                icon:'success',
                title:'ບັນທືກສຳເລັດ',
                showConfirmButton: false,
                timer: 1500,
            })
                window.setTimeout(function(){
                location.reload();
                },1500);
                </script>";
        }else{
            echo"<script>Swal.fire({
                postion: 'top',
                icon:'error',
                title:'ການບັນທືກບໍ່ສຳເລັດ',
            })
                </script>";
        }
    }
?>