<?php
include("../cennect_dbstock.php");
if(isset($_GET['pro_name'])){
    $pro_name = mysqli_real_escape_string($connect, $_GET['pro_name']);
    $remark = mysqli_real_escape_string($connect, $_GET['remark']);
    
    // ກວດສອບຊື່ຊ້ຳ
    $check = mysqli_query($connect, "SELECT pro_name FROM provinces WHERE pro_name='$pro_name'");
    if(mysqli_num_rows($check) > 0){
        echo "<script>Swal.fire({ icon: 'error', title: 'ຊື່ແຂວງນີ້ມີແລ້ວ!' });</script>";
    } else {
        $sql = "INSERT INTO provinces (pro_name, remark) VALUES ('$pro_name', '$remark')";
        if(mysqli_query($connect, $sql)){
            echo "<script>
                Swal.fire({ icon: 'success', title: 'ບັນທຶກສຳເລັດ', showConfirmButton: false, timer: 1500 })
                .then(() => { location.reload(); });
            </script>";
        }
    }
}
?>