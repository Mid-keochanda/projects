<?php
session_start();
include("../cennect_dbstock.php");

// ຮັບຄ່າຈາກ jQuery $.get
$pro_id = $_GET['pro_id'];
$dis_name = $_GET['dis_name'];
$remark = $_GET['remark'];

if($pro_id == "" || $dis_name == ""){
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'ຜິດພາດ',
            text: 'ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ'
        });
    </script>";
} else {
    // ຄຳສັ່ງ SQL ບັນທຶກຂໍ້ມູນ
    $sql = "INSERT INTO districts (pro_id, dis_name, remark) VALUES ('$pro_id', '$dis_name', '$remark')";
    $query = mysqli_query($connect, $sql);

    if($query){
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'ບັນທຶກສຳເລັດ',
                text: 'ຂໍ້ມູນເມືອງຖືກເພີ່ມເຂົ້າໃນລະບົບແລ້ວ',
                timer: 2000,
                showConfirmButton: false
            }).then(function() {
                window.location.reload(); // Refresh ໜ້າເພື່ອໂຊຂໍ້ມູນໃໝ່ໃນຕາຕະລາງ
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ລົ້ມເຫຼວ',
                text: 'ບໍ່ສາມາດບັນທຶກຂໍ້ມູນໄດ້: " . mysqli_error($connect) . "'
            });
        </script>";
    }
}
?>