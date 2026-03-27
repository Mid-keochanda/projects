<?php
include("../cennect_dbstock.php");

$cust_id   = $_POST['cust_id'] ?? "";
$car_plate = $_POST['car_plate'] ?? "";
$car_brand = $_POST['car_brand'] ?? "";
$car_model = $_POST['car_model'] ?? "";
$car_color = $_POST['car_color'] ?? "";
$remark    = $_POST['remark'] ?? "";

if(!empty($cust_id) && !empty($car_plate)) {
    
    // 1. ກວດສອບກ່ອນວ່າ ເລກທະບຽນນີ້ມີໃນລະບົບແລ້ວຫຼືບໍ່
    $check_sql = "SELECT car_plate FROM cars WHERE car_plate = '$car_plate'";
    $check_result = mysqli_query($connect, $check_sql);

    if(mysqli_num_rows($check_result) > 0) {
        // ຖ້າພົບຂໍ້ມູນຊ້ຳ
        echo "ເລກທະບຽນ '$car_plate' ນີ້ມີຢູ່ໃນລະບົບແລ້ວ! ກະລຸນາກວດສອບຄືນ.";
    } else {
        // 2. ຖ້າບໍ່ຊ້ຳ ຈຶ່ງອະນຸຍາດໃຫ້ບັນທຶກ
        $sql = "INSERT INTO cars (cust_id, car_plate, car_brand, car_model, car_color, remark) 
                VALUES ('$cust_id', '$car_plate', '$car_brand', '$car_model', '$car_color', '$remark')";
        
        if(mysqli_query($connect, $sql)) {
            echo "success";
        } else {
            echo "Error: " . mysqli_error($connect);
        }
    }
} else {
    echo "ກະລຸນາເລືອກລູກຄ້າ ແລະ ປ້ອນຂໍ້ມູນລົດໃຫ້ຄົບຖ້ວນ";
}
?>