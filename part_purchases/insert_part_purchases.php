<?php
include("../cennect_dbstock.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 🌟 ປ່ຽນການຮັບຄ່າ $_POST ໃຫ້ກົງກັບຊື່ Field ໃໝ່
    $part_id = intval($_POST['part_id']);
    $qty_bought = intval($_POST['qty_bought']);
    $buyer_price = floatval($_POST['buyer_price']);
    $supplier_name = mysqli_real_escape_string($connect, $_POST['supplier_name']);

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

    if ($part_id <= 0) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ບໍ່ພົບຂໍ້ມູນອາໄຫຼ່!',
                    text: 'ກະລຸນາກວດສອບລະຫັດອາໄຫຼ່ ຫຼື ບາໂຄດໃຫ້ຖືກຕ້ອງກ່ອນບັນທຶກ.',
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

    // 🌟 ປ່ຽນຄຳສັ່ງ INSERT ໃຫ້ກົງກັບໂຄງສ້າງ garage_db ເປະໆ
    $sql_insert = "INSERT INTO part_purchases (part_id, qty_bought, buyer_price, supplier_name) 
                   VALUES ('$part_id', '$qty_bought', '$buyer_price', '$supplier_name')";

    if (mysqli_query($connect, $sql_insert)) {
        
        // 🌟 ບວກເພີ່ມສະຕັອກໃນ parts_profile ໂດຍໃຊ້ຄ່າ $qty_bought
        $sql_update_stock = "UPDATE parts_profile SET qty_stock = qty_stock + $qty_bought WHERE part_id = '$part_id'";
        mysqli_query($connect, $sql_update_stock);

        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'ນຳເຂົ້າອາໄຫຼ່ສຳເລັດ!',
                    text: 'ເພີ່ມຈຳນວນອາໄຫຼ່ເຂົ້າຄັງສະຕັອກຮຽບຮ້ອຍແລ້ວ.',
                    confirmButtonColor: '#4361ee',
                    confirmButtonText: 'ຕົກລົງ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href='form_part_purchases.php';
                    }
                });
              </script>";
    } else {
        echo "Error: " . mysqli_error($connect);
    }

    echo '</body></html>';
}
?>