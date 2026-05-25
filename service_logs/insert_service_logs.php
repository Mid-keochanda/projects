<?php
include("../cennect_dbstock.php");
mysqli_set_charset($connect, "utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_id = intval($_POST['car_id']);
    $user_id = intval($_POST['user_id']);
    $symptoms = mysqli_real_escape_string($connect, $_POST['symptoms']);
    
    $labor_cost = 0; 
    $status = 'pending'; 

    if ($car_id <= 0 || $user_id <= 0 || empty($symptoms)) {
        echo "<script>
            alert('ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ'); 
            window.history.back();
        </script>";
        exit();
    }

    $sql = "INSERT INTO service_logs (car_id, user_id, symptoms, labor_cost, status) 
            VALUES ('$car_id', '$user_id', '$symptoms', '$labor_cost', '$status')";

    ?>
    <!DOCTYPE html>
    <html lang="lo">
    <head>
        <meta charset="UTF-8">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>@import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao&display=swap'); body{font-family:'Noto Sans Lao',sans-serif;}</style>
    </head>
    <body>
    <?php
    if (mysqli_query($connect, $sql)) {
        $new_log_id = mysqli_insert_id($connect);
        // ✅ ປ່ຽນມາໃຊ້ SweetAlert2 ເພື່ອຄວາມສວຍງາມ ແລະ ສະໝໍ່າສະເໝີ
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'ເປີດບິນສຳເລັດ!',
                text: 'ກຳລັງພາໄປໜ້າຈັດການອາໄຫຼ່...',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'form_service_details.php?id=" . $new_log_id . "';
            });
        </script>";
    } else {
        $error_msg = mysqli_real_escape_string($connect, mysqli_error($connect));
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ເກີດຂໍ້ຜິດພາດ!',
                text: '" . $error_msg . "',
                confirmButtonText: 'ຕົກລົງ'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
    ?>
    </body>
    </html>
    <?php
}
?>