<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../cennect_dbstock.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $part_id = intval($_POST['part_id']);
    $part_code = mysqli_real_escape_string($connect, $_POST['part_code']);
    $part_name = mysqli_real_escape_string($connect, $_POST['part_name']);
    $category_id = intval($_POST['category_id']);
    $cost_price = floatval(str_replace(',', '', $_POST['cost_price']));
    $sale_price = floatval(str_replace(',', '', $_POST['sale_price']));

    // --- ສ່ວນຈັດການອັບເດດຮູບພາບ ---
    $sql_image = "";
    if (isset($_FILES['part_image']) && $_FILES['part_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        // 1. ດຶງຊື່ຮູບເກົ່າມາເພື່ອລຶບຖິ້ມ
        $query_old = mysqli_query($connect, "SELECT part_image FROM parts_profile WHERE part_id = '$part_id'");
        $row_old = mysqli_fetch_array($query_old);
        if (!empty($row_old['part_image']) && file_exists($target_dir . $row_old['part_image'])) {
            unlink($target_dir . $row_old['part_image']);
        }

        // 2. ອັບໂຫຼດຮູບໃໝ່
        $file_ext = strtolower(pathinfo($_FILES["part_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = time() . "_" . uniqid() . "." . $file_ext;
        if (move_uploaded_file($_FILES["part_image"]["tmp_name"], $target_dir . $new_filename)) {
            $sql_image = ", part_image = '$new_filename'";
        }
    }

    // SQL ອັບເດດ
    $sql = "UPDATE parts_profile SET 
            part_code = '$part_code', 
            part_name = '$part_name', 
            category_id = '$category_id', 
            cost_price = '$cost_price', 
            sale_price = '$sale_price' 
            $sql_image
            WHERE part_id = '$part_id'";

    echo '<!DOCTYPE html><html lang="lo"><head><meta charset="UTF-8">
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          <style>@import url("https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap");
          body { font-family: "Noto Sans Lao", sans-serif; }</style>
          </head><body>';

    if (mysqli_query($connect, $sql)) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'ອັບເດດສຳເລັດ!',
                    text: 'ແກ້ໄຂຂໍ້ມູນອາໄຫຼ່ຮຽບຮ້ອຍແລ້ວ.',
                    confirmButtonColor: '#4361ee',
                    confirmButtonText: 'OK'
                }).then(() => { window.location.href='form_parts_profile.php'; });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ຜິດພາດ!',
                    text: 'ບໍ່ສາມາດອັບເດດຂໍ້ມູນໄດ້: " . mysqli_error($connect) . "'
                }).then(() => { window.history.back(); });
              </script>";
    }
    echo '</body></html>';
}
?>