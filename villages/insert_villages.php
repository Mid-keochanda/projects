<?php
include("../cennect_dbstock.php");

// ຮັບຄ່າຈາກ Ajax ($.get)
$pro_id    = mysqli_real_escape_string($connect, $_GET['pro_id']);
$dis_id    = mysqli_real_escape_string($connect, $_GET['dis_id']);
$vill_name = mysqli_real_escape_string($connect, $_GET['vill_name']);
$remark    = mysqli_real_escape_string($connect, $_GET['remark']);

// ເພີ່ມ pro_id ລົງໃນຄຳສັ່ງ SQL ໃຫ້ກົງກັບ CONSTRAINT ໃນ Database
$sql = "INSERT INTO villages (pro_id, dis_id, vill_name, remark) 
        VALUES ('$pro_id', '$dis_id', '$vill_name', '$remark')";

$query = mysqli_query($connect, $sql);

if($query){
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'ບັນທຶກສຳເລັດ',
            showConfirmButton: false,
            timer: 1500
        }).then(() => { location.reload(); });
    </script>";
} else {
    // ຖ້າ Error ຈະສະແດງຂໍ້ຄວາມເຕືອນ
    echo "Error: " . mysqli_error($connect);
}
?>