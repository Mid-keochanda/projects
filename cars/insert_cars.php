<?php
include("../cennect_dbstock.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $car_plate = mysqli_real_escape_string($connect, $_POST['car_plate']);
    $cust_id   = mysqli_real_escape_string($connect, $_POST['cust_id']);
    $car_brand = mysqli_real_escape_string($connect, $_POST['car_brand']);
    $car_model = mysqli_real_escape_string($connect, $_POST['car_model']);
    $car_color = mysqli_real_escape_string($connect, $_POST['car_color']);
    $remark    = mysqli_real_escape_string($connect, $_POST['remark']);
    $datenow   = date("Y-m-d H:i:s");

    $sql = "INSERT INTO cars (car_plate, cust_id, car_brand, car_model, car_color, remark, created_at) 
            VALUES ('$car_plate', '$cust_id', '$car_brand', '$car_model', '$car_color', '$remark', '$datenow')";

    if (mysqli_query($connect, $sql)) {
        echo "<html><body><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({ icon: 'success', title: 'ບັນທຶກສຳເລັດ!', showConfirmButton: false, timer: 1500 })
            .then(() => { window.location.href = 'form_cars.php'; });
        </script></body></html>";
    } else {
        echo "Error: " . mysqli_error($connect);
    }
}
?>