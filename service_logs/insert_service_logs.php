<?php
include("../cennect_dbstock.php");

if(isset($_POST['car_id'])) {
    $car_id   = mysqli_real_escape_string($connect, $_POST['car_id']);
    $user_id  = mysqli_real_escape_string($connect, $_POST['user_id']); 
    $symptoms = mysqli_real_escape_string($connect, $_POST['symptoms']);

    $sql = "INSERT INTO service_logs (car_id, user_id, symptoms, service_date) 
            VALUES ('$car_id', '$user_id', '$symptoms', NOW())";
    
    if(mysqli_query($connect, $sql)) {
        $last_id = mysqli_insert_id($connect);
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            setTimeout(function() {
                Swal.fire({
                    title: 'ເປີດບິນສຳເລັດ!',
                    text: 'ກຳລັງພາເຈົ້າໄປໜ້າເພີ່ມອາໄຫຼ່...',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(function() {
                    // ສົ່ງ ID ໄປໜ້າເພີ່ມອາໄຫຼ່ທັນທີ
                    window.location.href = 'add_service_item.php?id=$last_id';
                });
            }, 100);
        </script>";
    }
}
?>