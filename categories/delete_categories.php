<?php
include("../cennect_dbstock.php");

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($category_id > 0) {
    // ຄຳສັ່ງລຶບຂໍ້ມູນ
    $sql = "DELETE FROM part_categories WHERE category_id = '$category_id'";

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

    if (mysqli_query($connect, $sql)) {
        // ແຈ້ງເຕືອນເມື່ອລຶບສຳເລັດ (ຮູບ icon standard ເປັນ success)
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'ລຶບສຳເລັດ!',
                    text: 'ຂໍ້ມູນປະເພດອາໄຫຼ່ຖືກລຶບອອກຈາກລະບົບແລ້ວ.',
                    confirmButtonColor: '#4361ee',
                    confirmButtonText: 'ຕົກລົງ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href='form_categories.php';
                    }
                });
              </script>";
    } else {
        // ແຈ້ງເຕືອນເມື່ອຕິດ Foreign Key ຫຼື ເກີດ Error (ຮູບ icon ເປັນ error)
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ບໍ່ສາມາດລຶບໄດ້!',
                    text: 'ເນື່ອງຈາກປະເພດນີ້ກຳລັງຖືກນຳໃຊ້ໃນຕາຕະລາງຂໍ້ມູນອາໄຫຼ່.',
                    confirmButtonColor: '#ef233c',
                    confirmButtonText: 'ເຂົ້າໃຈແລ້ວ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href='form_categories.php';
                    }
                });
              </script>";
    }

    echo '</body></html>';
}
?>