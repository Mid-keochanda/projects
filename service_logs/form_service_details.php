<?php
include("../cennect_dbstock.php");
mysqli_set_charset($connect, "utf8"); // ບັງຄັບໃຫ້ຮອງຮັບພາສາລາວ

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($service_id <= 0) {
    header("Location: ../service_logs/select_service_logs.php");
    exit();
}

// --- 1. ອັບເດດຄ່າແຮງງານ ---
if (isset($_POST['btn_update_labor'])) {
    $labor_cost = mysqli_real_escape_string($connect, $_POST['labor_cost']);
    mysqli_query($connect, "UPDATE service_logs SET labor_cost = '$labor_cost' WHERE log_id = '$service_id'");
    header("Location: form_service_details.php?id=$service_id");
    exit();
}

// --- 2. ບັນທຶກອາໄຫຼ່ ແລະ ຕັດສະຕອກ (ພ້ອມກວດເຊັກສະຕອກ) ---
if (isset($_POST['btn_save'])) {
    $part_id = intval($_POST['part_id']);
    $qty = intval($_POST['qty']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $total = $qty * $price;

    // ກວດສອບສະຕອກກ່ອນ (Double Check ໃນ PHP)
    if ($part_id > 0) {
        $check_stock = mysqli_query($connect, "SELECT qty_stock FROM parts WHERE part_id = '$part_id'");
        $stock = mysqli_fetch_array($check_stock);
        if ($stock['qty_stock'] < $qty) {
            echo "<script>alert('❌ ຂໍອະໄພ: ອາໄຫຼ່ບໍ່ພໍ! (ຍັງເຫຼືອແຕ່ ".$stock['qty_stock'].")'); window.history.back();</script>";
            exit();
        }
    }

    $sql_add = "INSERT INTO service_details (service_id, part_id, description, qty, price, total) 
                VALUES ('$service_id', '$part_id', '$description', '$qty', '$price', '$total')";
    
    if (mysqli_query($connect, $sql_add)) {
        if ($part_id > 0) {
            mysqli_query($connect, "UPDATE parts SET qty_stock = qty_stock - $qty WHERE part_id = '$part_id'");
        }
        header("Location: form_service_details.php?id=$service_id");
        exit();
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
    }
    header("Location: form_service_details.php?id=$service_id");
    exit();
}

// --- 4. ດຶງຂໍ້ມູນຫົວບິນ (ໃຊ້ LEFT JOIN ປ້ອງກັນຂໍ້ມູນຫາຍ) ---
$sql_head = "SELECT l.*, c.car_plate, cust.cust_name 
             FROM service_logs l
             LEFT JOIN cars c ON l.car_id = c.car_id
             LEFT JOIN customers cust ON c.cust_id = cust.cust_id
             WHERE l.log_id = '$service_id'";
$res_head = mysqli_query($connect, $sql_head);

// ແກ້ໄຂ Error ທີ່ເຈົ້າເຈິ: ຖ້າ Query ຜິດໃຫ້ແຈ້ງເຕືອນ
if (!$res_head) {
    die("Error SQL (Head): " . mysqli_error($connect));
}
$head = mysqli_fetch_array($res_head);
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການການສ້ອມແປງ #<?php echo $service_id; ?></title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../icon/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f7f6; }
        .readonly-bg { background-color: #e9ecef !important; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-tools"></i> ປ້ອນລາຍການສ້ອມແປງ: #<?php echo $service_id; ?></h5>
            <a href="../service_logs/select_service_logs.php" class="btn btn-light btn-sm">ກັບຄືນ</a>
        </div>
        <div class="card-body">
            <div class="row mb-3 border-bottom pb-2">
                <div class="col-md-6">
                    <p class="mb-1"><b>ທະບຽນ:</b> <span class="text-primary"><?php echo $head['car_plate'] ?? '---'; ?></span></p>
                    <p class="mb-1"><b>ລູກຄ້າ:</b> <span class="text-primary"><?php echo $head['cust_name'] ?? '---'; ?></span></p>
                </div>
            </div>

            <form method="POST" id="mainForm" class="row g-2">
                <div class="col-md-4">
                    <select name="part_id" id="part_select" class="form-select" onchange="updatePrice()" required>
                        <option value="0" data-stock="999999">-- ອາໄຫຼ່ນອກ (ພິມເອງ) --</option>
                        <?php 
                        $res_parts = mysqli_query($connect, "SELECT * FROM parts WHERE qty_stock > 0");
                        while($p = mysqli_fetch_array($res_parts)) {
                            echo "<option value='".$p['part_id']."' 
                                          data-price='".$p['sale_price']."' 
                                          data-name='".$p['part_name']."' 
                                          data-stock='".$p['qty_stock']."'>
                                          ".$p['part_name']." (ຍັງເຫຼືອ: ".$p['qty_stock'].")
                                  </option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="description" id="description" class="form-control" placeholder="ລາຍການອາໄຫຼ່..." required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="price" id="price" class="form-control text-end" placeholder="ລາຄາ" required>
                </div>
                <div class="col-md-1">
                    <input type="number" name="qty" id="qty_input" value="1" min="1" class="form-control text-center" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="btn_save" class="btn btn-success w-100">ເພີ່ມອາໄຫຼ່</button>
                </div>
            </form>
        </div>
    </div>

    </div>

<script>
// 1. ກວດສອບສະຕອກກ່ອນ Submit
document.getElementById('mainForm').onsubmit = function(e) {
    var select = document.getElementById("part_select");
    var selectedOption = select.options[select.selectedIndex];
    var stockAvailable = parseInt(selectedOption.getAttribute("data-stock"));
    var qtyInput = parseInt(document.getElementById("qty_input").value);
    
    if (select.value != "0" && qtyInput > stockAvailable) {
        alert("❌ ອາໄຫຼ່ບໍ່ພໍ! ຍັງເຫຼືອແຕ່ " + stockAvailable);
        e.preventDefault();
        return false;
    }
};

// 2. Lock Readonly ເມື່ອເລືອກອາໄຫຼ່ໃນລະບົບ
function updatePrice() {
    var select = document.getElementById("part_select");
    var priceInput = document.getElementById("price");
    var descInput = document.getElementById("description");
    var selectedOption = select.options[select.selectedIndex];
    
    if (select.value != "0") {
        priceInput.value = selectedOption.getAttribute("data-price");
        descInput.value = selectedOption.getAttribute("data-name");
        priceInput.readOnly = true;
        descInput.readOnly = true;
        priceInput.classList.add("readonly-bg");
        descInput.classList.add("readonly-bg");
    } else {
        priceInput.value = "";
        descInput.value = "";
        priceInput.readOnly = false;
        descInput.readOnly = false;
        priceInput.classList.remove("readonly-bg");
        descInput.classList.remove("readonly-bg");
    }
}
</script>
</body>
</html>