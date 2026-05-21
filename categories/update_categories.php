<?php
include("../cennect_dbstock.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = intval($_POST['category_id']);
    $category_name = mysqli_real_escape_string($connect, $_POST['category_name']);

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

    $sql = "UPDATE part_categories SET category_name = '$category_name' WHERE category_id = '$category_id'";

    if (mysqli_query($connect, $sql)) {
        // ແຈ້ງເຕືອນເມື່ອອັບເດດສຳເລັດ (ໃຊ້ icon: 'success' ພ້ອມປຸ່ມສີຂຽວ/ຟ້າ ງາມໆ)
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'ອັບເດດສຳເລັດ!',
                    text: 'ແກ້ໄຂຂໍ້ມູນປະເພດອາໄຫຼ່ຮຽບຮ້ອຍແລ້ວ.',
                    confirmButtonColor: '#4361ee',
                    confirmButtonText: 'ຕົກລົງ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href='form_categories.php';
                    }
                });
              </script>";
    } else {
        echo "Error: " . mysqli_error($connect);
    }

    echo '</body></html>';
}
?>