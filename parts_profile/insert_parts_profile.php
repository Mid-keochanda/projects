<?php
include("../cennect_dbstock.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $part_code = mysqli_real_escape_string($connect, $_POST['part_code']);
    $part_name = mysqli_real_escape_string($connect, $_POST['part_name']);
    $category_id = intval($_POST['category_id']);
    $cost_price = floatval($_POST['cost_price']);
    $sale_price = floatval($_POST['sale_price']);

    // ເເລີ່ມຕົ້ນໂຄ້ດ HTML ເພື່ອເອີ້ນໃຊ້ SweetAlert2
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

    // ເຊັກລະຫັດອາໄຫຼ່ຊ້ຳ
    $check = mysqli_query($connect, "SELECT part_id FROM parts_profile WHERE part_code = '$part_code'");
    if(mysqli_num_rows($check) > 0) {
        // ແຈ້ງເຕືອນເມື່ອລະຫັດອາໄຫຼ່ຊ້ຳກັນ (ໃຊ້ icon: 'warning')
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'ລະຫັດຊ້ຳກັນ!',
                    text: 'ລະຫັດອາໄຫຼ່ນີ້ມີໃນລະບົບແລ້ວ, ກະລຸນາກວດສອບຄືນໃໝ່.',
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

    $sql = "INSERT INTO parts_profile (part_code, part_name, category_id, cost_price, sale_price) 
            VALUES ('$part_code', '$part_name', '$category_id', '$cost_price', '$sale_price')";

    if (mysqli_query($connect, $sql)) {
        // ແຈ້ງເຕືອນເມື່ອບັນທຶກສຳເລັດ (ໃຊ້ icon: 'success')
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'ບັນທຶກສຳເລັດ!',
                    text: 'ເພີ່ມຂໍ້ມູນອາໄຫຼ່ໃໝ່ເຂົ້າສະຕັອກຮຽບຮ້ອຍແລ້ວ.',
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