<?php
include("../cennect_dbstock.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ຮັບຄ່າ ແລະ ປ້ອງກັນການ SQL Injection
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

    // ກວດເຊັກກ່ອນວ່າ ຊື່ປະເພດນີ້ມີແລ້ວຫຼືບໍ່ ເພື່ອບໍ່ໃຫ້ຊື່ຊ້ຳກັນ
    $check = mysqli_query($connect, "SELECT category_id FROM part_categories WHERE category_name = '$category_name'");
    
    if (mysqli_num_rows($check) > 0) {
        // แจ้งเตือนเมื่อชื่อซ้ำกัน (ใช้ icon: 'warning')
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'ຂໍ້ອະໄພ!',
                    text: 'ມີປະເພດອາໄຫຼ່ນີ້ໃນລະບົບແລ້ວ, ກະລຸນາໃຊ້ຊື່ອນ.',
                    confirmButtonColor: '#ef233c',
                    confirmButtonText: 'ກັບຄືນ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.history.back();
                    }
                });
              </script>";
        echo '</body></html>';
        exit();
    }

    // ຄຳສັ່ງ SQL ບັນທຶກຂໍ້ມູນ
    $sql = "INSERT INTO part_categories (category_name) VALUES ('$category_name')";

    if (mysqli_query($connect, $sql)) {
        // ແຈ້ງເຕືອນເມື່ອບັນທຶກສຳເລັດ (ใช้ icon: 'success')
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'ບັນທຶກສຳເລັດ!',
                    text: 'ເພີ່ມປະເພດອາໄຫຼ່ໃໝ່ເຂົ້າຮຽບຮ້ອຍແລ້ວ.',
                    confirmButtonColor: '#4361ee',
                    confirmButtonText: 'ຕົກລົງ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href='form_categories.php';
                    }
                });
              </script>";
    } else {
        echo "ເກີດຂໍ້ຜິດພາດ: " . mysqli_error($connect);
    }

    echo '</body></html>';
}
?>