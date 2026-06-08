<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../cennect_dbstock.php");

// ຖ້າບໍ່ມີການສົ່ງຄ່າ POST ມາ ໃຫ້ກັບໄປໜ້າຟອມ
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: form_parts_profile.php");
    exit();
}

$part_code = mysqli_real_escape_string($connect, $_POST['part_code']);
$part_name = mysqli_real_escape_string($connect, $_POST['part_name']);
$category_id = intval($_POST['category_id']);
$cost_price = floatval(str_replace(',', '', $_POST['cost_price']));
$sale_price = floatval(str_replace(',', '', $_POST['sale_price']));

// --- ສ່ວນຈັດການອັບໂຫຼດຮູບພາບ ---
$image_name = NULL;
if (isset($_FILES['part_image']) && $_FILES['part_image']['error'] == 0) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_ext = strtolower(pathinfo($_FILES["part_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $target_dir . $new_filename;
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($file_ext, $allowed)) {
        if (move_uploaded_file($_FILES["part_image"]["tmp_name"], $target_file)) {
            $image_name = $new_filename;
        }
    }
}

// --- SQL INSERT ---
$sql = "INSERT INTO parts_profile (part_code, part_name, part_image, category_id, cost_price, sale_price) 
        VALUES ('$part_code', '$part_name', '$image_name', '$category_id', '$cost_price', '$sale_price')";
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap');

    body {
        font-family: 'Noto Sans Lao', sans-serif !important;
        background-color: #f1f5f9;
        color: #334155;
    }

    /* ປັບໃຫ້ Text ໃນຕາຕະລາງ ແລະ ປຸ່ມຕ່າງໆ ເບິ່ງຊັດເຈນ */
    .table, .btn, .form-control, .modal-title, label {
        font-family: 'Noto Sans Lao', sans-serif !important;
    }

    /* ປັບຂະໜາດຟອນໃຫ້ອ່ານງ່າຍ */
    h3, h5 { font-weight: 700 !important; }
    .small { font-size: 0.85rem; }
    
    /* ປັບສີຟອນໃຫ້ມີຄວາມຄົມຊັດ */
    .text-dark { color: #1e293b !important; }
    .text-muted { color: #64748b !important; }
</style>
</head>
<body>
<?php
if (mysqli_query($connect, $sql)) {
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'ບັນທຶກສຳເລັດ!',
                text: 'ເພີ່ມຂໍ້ມູນອາໄຫຼ່ໃໝ່ເຂົ້າສະຕັອກຮຽບຮ້ອຍແລ້ວ.',
                confirmButtonColor: '#4361ee'
            }).then(() => { window.location.href='form_parts_profile.php'; });
          </script>";
} else {
    echo "<h1>Database Error: " . mysqli_error($connect) . "</h1>";
}
?>
</body>
</html>