<?php
include("../cennect_dbstock.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $part_id = intval($_POST['part_id']);
    $part_code = mysqli_real_escape_string($connect, $_POST['part_code']);
    $part_name = mysqli_real_escape_string($connect, $_POST['part_name']);
    $category_id = intval($_POST['category_id']);
    $cost_price = floatval($_POST['cost_price']);
    $sale_price = floatval($_POST['sale_price']);
    

    // ເລີ່ມຕົ້ນໂຄ້ດ HTML ເພື່ອເອີ້ນໃຊ້ SweetAlert2
    echo '<!DOCTYPE html>
    <html lang="lo">
    <head>
        <meta charset="UTF-8">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap");
            .swal2-popup { font-family: "Noto Sans Lao", sans-serif !important; border-radius: 15px !important; }
        </style>
    </head>
    <body>';

    $sql = "UPDATE parts_profile SET 
            part_code = '$part_code', 
            part_name = '$part_name', 
            category_id = '$category_id', 
            cost_price = '$cost_price', 
            sale_price = '$sale_price'
            WHERE part_id = '$part_id'";

    if (mysqli_query($connect, $sql)) {
        // ແຈ້ງເຕືອນເມື່ອອັບເດດສຳເລັດ (ໃຊ້ icon: 'success' ພ້ອມປຸ່ມສີຟ້າ/ຂຽວ ເຂົ້າກັບ Theme ລະບົບ)
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'ອັບເດດສຳເລັດ!',
                    text: 'ແກ້ໄຂຂໍ້ມູນອາໄຫຼ່ໃນສະຕັອກຮຽບຮ້ອຍແລ້ວ.',
                    confirmButtonColor: '#4361ee',
                    confirmButtonText: 'ຕົກລົງ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href='form_parts_profile.php';
                    }
                });
              </script>";
    } else {
        echo "Error: " . mysqli_error($connect);
    }

    echo '</body></html>';
}
?>