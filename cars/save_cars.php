<?php
include("../cennect_dbstock.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_id    = mysqli_real_escape_string($connect, $_POST['car_id']);
    $car_plate = mysqli_real_escape_string($connect, $_POST['car_plate']);
    $cust_id   = mysqli_real_escape_string($connect, $_POST['cust_id']);
    $car_brand = mysqli_real_escape_string($connect, $_POST['car_brand']);
    $car_model = mysqli_real_escape_string($connect, $_POST['car_model']);
    $car_color = mysqli_real_escape_string($connect, $_POST['car_color']);
    $chassis_no = mysqli_real_escape_string($connect, $_POST['chassis_no']);
    $engine_no  = mysqli_real_escape_string($connect, $_POST['engine_no']);
    $car_year   = mysqli_real_escape_string($connect, $_POST['car_year']);
    
    $remark    = mysqli_real_escape_string($connect, $_POST['remark']);
    $datenow   = date("Y-m-d H:i:s");

    $sql = "UPDATE cars SET 
                car_plate  = '$car_plate', 
                cust_id    = '$cust_id', 
                car_brand  = '$car_brand', 
                car_model  = '$car_model', 
                car_color  = '$car_color', 
                chassis_no = '$chassis_no',
                engine_no  = '$engine_no',
                car_year   = '$car_year',
                remark     = '$remark',
                updated_at = '$datenow' 
            WHERE car_id = '$car_id'";

    if (mysqli_query($connect, $sql)) {
        echo "
        <html>
        <head>
            <link href='https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap' rel='stylesheet'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <style>
                body { font-family: 'Noto Sans Lao', sans-serif !important; }
            </style>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'ອັບເດດຂໍ້ມູນສຳເລັດ!',
                    text: 'ຂໍ້ມູນລົດຂອງທ່ານຖືກແກ້ໄຂຮຽບຮ້ອຍແລ້ວ',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = 'form_cars.php';
                });
            </script>
        </body>
        </html>";
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}
?>