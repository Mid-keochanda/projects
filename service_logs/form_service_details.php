<?php
include("../cennect_dbstock.php");
mysqli_set_charset($connect, "utf8");

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($service_id <= 0) {
    header("Location: ../service_logs/select_service_logs.php");
    exit();
}

$msg_status = "";
$current_stock = 0;

// --- 1. ບັນທຶກອາໄຫຼ່ ແລະ ຕັດສະຕອກ ---
if (isset($_POST['btn_save'])) {
    $part_id = intval($_POST['part_id']);
    $qty = intval($_POST['qty']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $total = $qty * $price;

    if ($part_id > 0) {
        $check_stock = mysqli_query($connect, "SELECT qty_stock FROM parts WHERE part_id = '$part_id'");
        $stock = mysqli_fetch_array($check_stock);
        if ($stock['qty_stock'] < $qty) {
            $msg_status = "stock_low";
            $current_stock = $stock['qty_stock'];
        }
    }

    if ($msg_status == "") {
        $sql_add = "INSERT INTO service_details (service_id, part_id, description, qty, price, total) 
                    VALUES ('$service_id', '$part_id', '$description', '$qty', '$price', '$total')";
        if (mysqli_query($connect, $sql_add)) {
            if ($part_id > 0) {
                mysqli_query($connect, "UPDATE parts SET qty_stock = qty_stock - $qty WHERE part_id = '$part_id'");
            }
            $msg_status = "success";
        }
    }
}

// --- 2. ອັບເດດຄ່າແຮງງານ ---
if (isset($_POST['btn_update_labor'])) {
    $labor_cost = floatval($_POST['labor_cost']);
    $sql_up_labor = "UPDATE service_logs SET labor_cost = '$labor_cost' WHERE log_id = '$service_id'";
    if (mysqli_query($connect, $sql_up_labor)) {
        $msg_status = "labor_updated";
    }
}

// --- 3. ລຶບລາຍການ ແລະ ຄືນສະຕອກ ---
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $res_old = mysqli_query($connect, "SELECT * FROM service_details WHERE detail_id = '$del_id'");
    $row_del = mysqli_fetch_array($res_old);
    if ($row_del) {
        if ($row_del['part_id'] > 0) {
            mysqli_query($connect, "UPDATE parts SET qty_stock = qty_stock + ".$row_del['qty']." WHERE part_id = '".$row_del['part_id']."'");
        }
        mysqli_query($connect, "DELETE FROM service_details WHERE detail_id = '$del_id'");
        $msg_status = "deleted";
    }
}

// --- 4. ດຶງຂໍ້ມູນຫົວບິນ (ລວມຄ່າແຮງງານຫຼ້າສຸດ) ---
$sql_head = "SELECT l.*, c.car_plate, cust.cust_name 
             FROM service_logs l
             LEFT JOIN cars c ON l.car_id = c.car_id
             LEFT JOIN customers cust ON c.cust_id = cust.cust_id
             WHERE l.log_id = '$service_id'";
$head_res = mysqli_query($connect, $sql_head);
$head = mysqli_fetch_array($head_res);
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການການສ້ອມແປງ #<?php echo $service_id; ?></title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f7f6; }
        .readonly-bg { background-color: #e9ecef !important; }
        .card { border: none; border-radius: 15px; }
        .table thead { background-color: #4e73df; color: white; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4 class="text-primary fw-bold mb-0">ບິນສ້ອມແປງ #<?php echo str_pad($service_id, 5, "0", STR_PAD_LEFT); ?></h4>
                <p class="text-muted mb-0">ລູກຄ້າ: <?php echo $head['cust_name'] ?? '---'; ?> | ທະບຽນ: <?php echo $head['car_plate'] ?? '---'; ?></p>
            </div>
            <a href="select_service_logs.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> ກັບຄືນ</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3 text-success"><i class="fas fa-plus-circle"></i> ເພີ່ມລາຍການອາໄຫຼ່</h5>
            <form method="POST" id="mainForm" class="row g-2">
                <div class="col-md-4">
                    <label class="small">ເລືອກອາໄຫຼ່ໃນສະຕອກ</label><br>
                    <select name="part_id" id="part_select" class="form-select" onchange="updatePrice()" required>
                        <option value="0" data-stock="999999">-- ອາໄຫຼ່ນອກ (ພິມເຍງ) --</option>
                        <?php 
                        $res_parts = mysqli_query($connect, "SELECT * FROM parts WHERE qty_stock > 0");
                        while($p = mysqli_fetch_array($res_parts)) {
                            echo "<option value='".$p['part_id']."' data-price='".$p['sale_price']."' data-name='".$p['part_name']."' data-stock='".$p['qty_stock']."'>
                                    ".$p['part_name']." (ຍັງເຫຼືອ: ".$p['qty_stock'].")
                                  </option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small">ລາຍການ</label>
                    <input type="text" name="description" id="description" class="form-control" placeholder="ພິມລາຍການ..." required>
                </div>
                <div class="col-md-2">
                    <label class="small">ລາຄາ/ໜ່ວຍ</label>
                    <input type="number" name="price" id="price" class="form-control text-end" required>
                </div>
                <div class="col-md-1">
                    <label class="small">ຈຳນວນ</label>
                    <input type="number" name="qty" id="qty_input" value="1" min="1" class="form-control text-center" required>
                </div>
                <div class="col-md-2">
                    <label class="small">&nbsp;</label>
                    <button type="submit" name="btn_save" class="btn btn-success w-100">ເພີ່ມ</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-list text-primary"></i> ລາຍການທີ່ເພີ່ມແລ້ວ</h5>
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>ລາຍການ</th>
                        <th class="text-center">ຈຳນວນ</th>
                        <th class="text-end">ລາຄາ</th>
                        <th class="text-end">ລວມ</th>
                        <th class="text-center">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res_det = mysqli_query($connect, "SELECT * FROM service_details WHERE service_id = '$service_id'");
                    $parts_total = 0;
                    $i = 1;
                    while($d = mysqli_fetch_array($res_det)) {
                        $parts_total += $d['total'];
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $d['description']; ?></td>
                        <td class="text-center"><?php echo $d['qty']; ?></td>
                        <td class="text-end"><?php echo number_format($d['price']); ?></td>
                        <td class="text-end fw-bold"><?php echo number_format($d['total']); ?></td>
                        <td class="text-center">
                            <a href="?id=<?php echo $service_id; ?>&delete_id=<?php echo $d['detail_id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('ຢືນຢັນການລຶບ?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="row mt-4 border-top pt-3">
                <div class="col-md-6 border-end">
                    <form method="POST" class="row g-2 align-items-end p-2 bg-light rounded">
                        <div class="col-md-7">
                            <label class="small fw-bold text-primary"><i class="fas fa-wrench me-1"></i> ຄ່າແຮງງານ (ກີບ)</label>
                            <input type="number" name="labor_cost" class="form-control" value="<?php echo $head['labor_cost'] ?? 0; ?>" required>
                        </div>
                        <div class="col-md-5">
                            <button type="submit" name="btn_update_labor" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> ບັນທຶກຄ່າແຮງ
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-end fw-bold">ລວມຄ່າອາໄຫຼ່:</td>
                            <td class="text-end" width="180"><?php echo number_format($parts_total); ?> ກີບ</td>
                        </tr>
                        <tr>
                            <td class="text-end fw-bold border-bottom">ຄ່າແຮງງານ:</td>
                            <td class="text-end border-bottom"><?php echo number_format($head['labor_cost'] ?? 0); ?> ກີບ</td>
                        </tr>
                        <tr>
                            <td class="text-end fw-bold fs-5 text-dark">ຍອດລວມສຸດທິ:</td>
                            <td class="text-end fw-bold text-danger fs-4">
                                <?php echo number_format($parts_total + ($head['labor_cost'] ?? 0)); ?> ກີບ
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="print_service_logs.php?id=<?php echo $service_id; ?>" target="_blank" class="btn btn-dark px-4"><i class="fas fa-print"></i> ພິມໃບບິນ</a>
            </div>
        </div>
    </div>
</div>

<script>
// 1. Alert ຫຼັງຈາກການເຮັດວຽກ
<?php if($msg_status == "success"): ?>
    Swal.fire({ icon: 'success', title: 'ເພີ່ມອາໄຫຼ່ແລ້ວ!', timer: 1200, showConfirmButton: false });
<?php elseif($msg_status == "labor_updated"): ?>
    Swal.fire({ icon: 'success', title: 'ບັນທຶກຄ່າແຮງງານແລ້ວ!', timer: 1200, showConfirmButton: false });
<?php elseif($msg_status == "stock_low"): ?>
    Swal.fire({ icon: 'error', title: 'ສະຕອກບໍ່ພໍ!', text: 'ຍັງເຫຼືອແຕ່ <?php echo $current_stock; ?>' });
<?php elseif($msg_status == "deleted"): ?>
    Swal.fire({ icon: 'info', title: 'ລຶບລາຍການແລ້ວ', timer: 1200, showConfirmButton: false });
<?php endif; ?>

// 2. Lock Readonly ເມື່ອເລືອກອາໄຫຼ່
function updatePrice() {
    var select = document.getElementById("part_select");
    var priceInput = document.getElementById("price");
    var descInput = document.getElementById("description");
    var selectedOption = select.options[select.selectedIndex];
    
    if (select.value != "0") {
        priceInput.value = selectedOption.getAttribute("data-price");
        descInput.value = selectedOption.getAttribute("data-name");
        priceInput.readOnly = true; descInput.readOnly = true;
        priceInput.classList.add("readonly-bg"); descInput.classList.add("readonly-bg");
    } else {
        priceInput.value = ""; descInput.value = "";
        priceInput.readOnly = false; descInput.readOnly = false;
        priceInput.classList.remove("readonly-bg"); descInput.classList.remove("readonly-bg");
    }
}
</script>
</body>
</html>