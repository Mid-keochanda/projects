<?php 
include("../cennect_dbstock.php"); 

// 1. ດຶງຂໍ້ມູນມາສະແດງໃນ Form
$data = [];
if(isset($_GET['part_id'])) {
    $id = mysqli_real_escape_string($connect, $_GET['part_id']);
    $res = mysqli_query($connect, "SELECT * FROM parts WHERE part_id = '$id'");
    if(mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_array($res);
    } else {
        die("<div class='alert alert-danger'>ບໍ່ພົບຂໍ້ມູນອາໄຫຼ່ນີ້!</div>");
    }
}

// 2. ສ່ວນການ Update ຂໍ້ມູນ
if(isset($_POST['btnUpdate'])) {
    $id = mysqli_real_escape_string($connect, $_POST['part_id']);
    $part_name  = mysqli_real_escape_string($connect, $_POST['part_name']);
    $category   = mysqli_real_escape_string($connect, $_POST['category']);
    $qty_stock  = mysqli_real_escape_string($connect, $_POST['qty_stock']);
    $min_stock  = mysqli_real_escape_string($connect, $_POST['min_stock']);
    $unit       = mysqli_real_escape_string($connect, $_POST['unit']);
    $cost_price = mysqli_real_escape_string($connect, $_POST['cost_price']);
    $sale_price = mysqli_real_escape_string($connect, $_POST['sale_price']);
    $remark     = mysqli_real_escape_string($connect, $_POST['remark']);

    $sql = "UPDATE parts SET 
            part_name = '$part_name', 
            category = '$category', 
            qty_stock = '$qty_stock', 
            min_stock = '$min_stock', 
            unit = '$unit', 
            cost_price = '$cost_price', 
            sale_price = '$sale_price', 
            remark = '$remark', 
            updated_at = NOW() 
            WHERE part_id = '$id'";

    if(mysqli_query($connect, $sql)) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            setTimeout(function() {
                Swal.fire({
                    title: 'ອັບເດດສຳເລັດ!',
                    text: 'ຂໍ້ມູນອາໄຫຼ່ຖືກແກ້ໄຂແລ້ວ',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'select_parts.php';
                });
            }, 100);
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ແກ້ໄຂຂໍ້ມູນອາໄຫຼ່</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f1f5f9; }
        .custom-card { border: none; border-radius: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #475569; font-size: 0.9rem; }
        .form-control { border-radius: 0.75rem; padding: 0.6rem 1rem; border: 1px solid #e2e8f0; }
        .form-control:focus { border-color: #4361ee; box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card p-4 p-md-5 bg-white">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                        <i class="fas fa-edit text-primary fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold m-0">ແກ້ໄຂຂໍ້ມູນອາໄຫຼ່</h4>
                        <p class="text-muted small m-0">ປັບປຸງລາຍລະອຽດ ແລະ ຈຳນວນໃນສາງ</p>
                    </div>
                </div>

                <form action="" method="POST">
                    <input type="hidden" name="part_id" value="<?= $data['part_id'] ?>">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">ຊື່ອາໄຫຼ່</label>
                            <input type="text" name="part_name" class="form-control" value="<?= htmlspecialchars($data['part_name']) ?>" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">ໝວດໝູ່</label>
                            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($data['category']) ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ຈຳນວນໃນສາງ</label>
                            <input type="number" name="qty_stock" class="form-control" value="<?= $data['qty_stock'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ຕ່ຳສຸດທີ່ເຕືອນ</label>
                            <input type="number" name="min_stock" class="form-control" value="<?= $data['min_stock'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ຫົວໜ່ວຍ</label>
                            <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($data['unit']) ?>" placeholder="ໂຕ, ກ່ອງ, ລິດ...">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ລາຄາຕົ້ນທຶນ</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="cost_price" class="form-control" value="<?= $data['cost_price'] ?>">
                                <span class="input-group-text bg-light text-muted small">ກີບ</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ລາຄາຂາຍ</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="sale_price" class="form-control" value="<?= $data['sale_price'] ?>">
                                <span class="input-group-text bg-light text-muted small">ກີບ</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">ໝາຍເຫດ</label>
                            <textarea name="remark" class="form-control" rows="3"><?= htmlspecialchars($data['remark']) ?></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="d-flex gap-2">
                                <button type="submit" name="btnUpdate" class="btn btn-primary px-4 py-2 fw-bold rounded-3 shadow-sm">
                                    <i class="fas fa-save me-2"></i> ບັນທຶກການແກ້ໄຂ
                                </button>
                                <a href="select_parts.php" class="btn btn-light px-4 py-2 fw-bold rounded-3 border">
                                    ຍົກເລີກ
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>