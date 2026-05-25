<?php
// ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
require_once("../cennect_dbstock.php");

// ກວດສອບວ່າເຊື່ອມຕໍ່ສຳເລັດ
if (!isset($connect)) {
    die("Error: ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນໄດ້.");
}

mysqli_set_charset($connect, "utf8");

// ຮັບຄ່າ ID ແລະ ກວດສອບຄວາມຖືກຕ້ອງ
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($service_id <= 0) {
    echo "<script>alert('ລະບົບບໍ່ພົບ ID ບິນ'); window.location='select_service_logs.php';</script>";
    exit();
}

// Logic ການບັນທຶກ (Insert)
if (isset($_POST['btn_save'])) {
    $part_val = intval($_POST['part_id']);
    $part_id_sql = ($part_val > 0) ? $part_val : "NULL";
    $qty = intval($_POST['qty']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $total = $qty * $price;

    $can_save = true;
    if ($part_val > 0) {
        $check = mysqli_query($connect, "SELECT qty_stock FROM parts_profile WHERE part_id = $part_val");
        $row = mysqli_fetch_array($check);
        if (!$row || $row['qty_stock'] < $qty) {
            echo "<script>alert('ສະຕັອກບໍ່ພໍ!');</script>";
            $can_save = false;
        }
    }

    if ($can_save) {
        $sql = "INSERT INTO service_details (service_id, part_id, description, qty, price, total) VALUES ($service_id, $part_id_sql, '$description', $qty, $price, $total)";
        if (mysqli_query($connect, $sql)) {
            if ($part_val > 0) {
                mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock - $qty WHERE part_id = $part_val");
            }
            echo "<script>window.location='?id=$service_id';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການບິນ #<?php echo $service_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f6f9; }
        .card { border: none; border-radius: 15px; }
        .table thead { background-color: #2c3e50; color: white; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary">ລາຍການສ້ອມແປງ</h3>
            <p class="text-muted">ເລກທີບິນ: #<?php echo $service_id; ?></p>
        </div>
        <button type="button" class="btn btn-success shadow" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus-circle me-2"></i>ເພີ່ມລາຍການ
        </button>
    </div>

    <div class="card shadow-sm p-3">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ລາຍການ</th>
                    <th class="text-center">ຈຳນວນ</th>
                    <th class="text-end">ລາຄາ</th>
                    <th class="text-end">ລວມ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $res_det = mysqli_query($connect, "SELECT d.*, p.part_name FROM service_details d LEFT JOIN parts_profile p ON d.part_id = p.part_id WHERE d.service_id = $service_id");
                $grand_total = 0;
                if ($res_det && mysqli_num_rows($res_det) > 0) {
                    while($d = mysqli_fetch_array($res_det)) {
                        $grand_total += $d['total'];
                        echo "<tr>
                                <td>".($d['part_name'] ?? $d['description'])."</td>
                                <td class='text-center'>".$d['qty']."</td>
                                <td class='text-end'>".number_format($d['price'])."</td>
                                <td class='text-end'>".number_format($d['total'])."</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>ຍັງບໍ່ມີລາຍການສ້ອມແປງ</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <div class="d-flex justify-content-end p-3 bg-light rounded">
            <h4 class="mb-0">ຍອດລວມ: <span class="text-success fw-bold"><?php echo number_format($grand_total); ?> ກີບ</span></h4>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">ເພີ່ມລາຍການ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
            <div class="mb-3">
                <select name="part_id" id="part_select" class="form-select" onchange="updatePrice()">
                    <option value="0">-- ເລືອກອາໄຫຼ່ (ຫຼື ພິມເອງ) --</option>
                    <?php 
                    $res = mysqli_query($connect, "SELECT * FROM parts_profile WHERE qty_stock > 0");
                    while($p = mysqli_fetch_array($res)) {
                        echo "<option value='".$p['part_id']."' data-price='".$p['sale_price']."' data-name='".$p['part_name']."'>".$p['part_name']." (ມີ: ".$p['qty_stock'].")</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3"><input type="text" name="description" id="description" class="form-control" placeholder="ຊື່ລາຍການ" required></div>
            <div class="row">
                <div class="col-6"><input type="number" name="price" id="price" class="form-control" placeholder="ລາຄາ" required></div>
                <div class="col-6"><input type="number" name="qty" class="form-control" value="1" required></div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="btn_save" class="btn btn-primary w-100">ບັນທຶກລາຍການ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updatePrice() {
    var select = document.getElementById("part_select");
    var pInput = document.getElementById("price");
    var dInput = document.getElementById("description");
    var opt = select.options[select.selectedIndex];
    if(select.value != "0") {
        pInput.value = opt.getAttribute("data-price");
        dInput.value = opt.getAttribute("data-name");
    } else {
        pInput.value = "";
        dInput.value = "";
    }
}
</script>
</body>
</html>