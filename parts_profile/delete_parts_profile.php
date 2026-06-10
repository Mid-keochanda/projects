<?php
include("../cennect_dbstock.php");

// ກວດສອບວ່າມີການສົ່ງ ID ມາຫຼືບໍ່
if (isset($_GET['id'])) {
    $part_id = intval($_GET['id']);

    // ເປີດໂຄ້ດ HTML ເພື່ອເອີ້ນໃຊ້ SweetAlert2
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

    // ສ້າງຄຳສັ່ງລຶບ
    $sql = "DELETE FROM parts_profile WHERE part_id = '$part_id'";

    // ທຳການລັນຄຳສັ່ງ ຖ້າຜ່ານແມ່ນລຶບເລີຍ ຖ້າບໍ່ຜ່ານຈະຕົກໄປບ່ອນ else
    if (mysqli_query($connect, $sql)) {
        // ກໍລະນີລຶບສຳເລັດ (ບໍ່ຕິດ Foreign Key)
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'ລຶບອາໄຫຼ່ສຳເລັດ!',
                    text: 'ຂໍ້ມູນອາໄຫຼ່ຖືກລຶບອອກຈາກລະບົບແລ້ວ.',
                    confirmButtonColor: '#4361ee',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    window.location.href='form_parts_profile.php';
                });
              </script>";
    } else {
        // 🌟 ຈຸດສຳຄັນ: ກວດສອບ Error Code ຂອງ MySQL
        $error_code = mysqli_errno($connect);

        if ($error_code == 1451) {
            // 🛑 ລະຫັດ 1451 ໝາຍຄວາມວ່າຕິດ Foreign Key Constraint (ມີຂໍ້ມູນຢູ່ໃນຕາຕະລາງລູກ)
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'ບໍ່ສາມາດລຶບອາໄຫຼ່ນີ້ໄດ້!',
                        text: 'ເນື່ອງຈາກອາໄຫຼ່ລາຍການນີ້ ມີປະຫວັດການນຳເຂົ້າ (ຊື້) ຢູ່ໃນລະບົບແລ້ວ. ຫາກຕ້ອງການລຶບ ຕ້ອງໄປລຶບປະຫວັດການນຳເຂົ້າຂອງອາໄຫຼ່ນີ້ອອກກ່ອນ.',
                        confirmButtonColor: '#ef233c',
                        confirmButtonText: 'ຮັບຊາບ'
                    }).then((result) => {
                        window.location.href='form_parts_profile.php';
                    });
                  </script>";
        } else {
            // ກໍລະນີ Error ຈາກສາເຫດອື່ນໆ
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'ເກີດຂໍ້ຜິດພາດ!',
                        text: 'ບໍ່ສາມາດລຶບໄດ້ເນື່ອງຈາກ: " . mysqli_escape_string($connect, mysqli_error($connect)) . "',
                        confirmButtonColor: '#ef233c',
                        confirmButtonText: 'ກັບຄືນ'
                    }).then((result) => {
                        window.location.href='form_parts_profile.php';
                    });
                  </script>";
        }
    }

    echo '</body></html>';
} else {
    // ຖ້າບໍ່ມີການສົ່ງ ID ມາ ໃຫ້ເດັ້ງກັບໜ້າຫຼັກ
    header("Location: form_parts_profile.php");
    exit();
}
?>