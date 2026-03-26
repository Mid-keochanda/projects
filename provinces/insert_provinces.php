<?php
session_start();
include("../cennect_dbstock.php");

// ກວດສອບວ່າມີຂໍ້ມູນສົ່ງມາຫຼືບໍ່
if(isset($_GET['pro_name'])){
    // ປ້ອງກັນ SQL Injection
    $pro_name = mysqli_real_escape_string($connect, $_GET['pro_name']);
    $remark = mysqli_real_escape_string($connect, $_GET['remark']);
    
    // ກວດສອບ user_id (ຖ້າບໍ່ມີໃນ Table ໃຫ້ຕັດສ່ວນນີ້ອອກ)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; 

    // 1. ກວດສອບຊື່ຊ້ຳ
    $select = mysqli_query($connect, "SELECT * FROM provinces WHERE pro_name='$pro_name'");
    
    if(mysqli_num_rows($select) > 0){
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ຂໍອະໄພ!',
                text: 'ຊື່ແຂວງນີ້ມີຢູ່ໃນລະບົບແລ້ວ',
            });
        </script>";
    } else {
        // 2. ຄຳສັ່ງ INSERT (ກວດສອບຊື່ Column ໃຫ້ຕົງກັບ Database)
       $sql = "INSERT INTO provinces (pro_name, remark) VALUES ('$pro_name', '$remark')";
        
        if(mysqli_query($connect, $sql)){
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'ບັນທຶກສຳເລັດ',
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function(){
                    location.reload(); 
                }, 1500);
            </script>";
        } else {
            // ຖ້າບັນທຶກບໍ່ໄດ້ ໃຫ້ສະແດງ Error ຈາກ Database ອອກມາເບິ່ງ
            $db_error = mysqli_error($connect);
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ບັນທຶກບໍ່ໄດ້!',
                    text: 'ສາເຫດ: $db_error',
                });
            </script>";
        }
    }
}
?>