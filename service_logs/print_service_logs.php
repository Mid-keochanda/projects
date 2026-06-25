<?php
session_start();
// ກວດສອບການລ໋ອກອິນ
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<script>alert('ກະລຸນາລ໋ອກອິນກ່ອນ');location='index.php';</script>";
    exit();
} else {
include("../cennect_dbstock.php");
mysqli_set_charset($connect, "utf8"); 

// 🎯 1. ດຶງຊື່ພະນັກງານລ໋ອກອິນປະຈຸບັນ
$current_logged_in_staff = '';
if (isset($_SESSION['fname']) || isset($_SESSION['lname'])) {
    $f = isset($_SESSION['fname']) ? $_SESSION['fname'] : '';
    $l = isset($_SESSION['lname']) ? $_SESSION['lname'] : '';
    $current_logged_in_staff = trim($f . " " . $l);
} 
if (empty($current_logged_in_staff)) {
    $current_logged_in_staff = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'mid keochanda';
}

$log_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($log_id <= 0) {
    die("<h3 style='text-align:center; padding-top:50px; font-family:\"Noto Sans Lao\";'>ບໍ່ພົບຂໍ້ມູນບິນເລກທີ: $log_id</h3>");
}

// 🎯 2. ດຶງຂໍ້ມູນຫຼັກແບບ l.* ເພື່ອປ້ອງກັນ Error Unknown Column ໃນ On Clause 100%
$sql_head = "SELECT 
                l.*, 
                c.car_plate, c.car_brand, c.car_model, 
                cust.cust_name, cust.tel,
                inv.payment_status, inv.payment_type
             FROM service_logs l
             LEFT JOIN cars c ON l.car_id = c.car_id
             LEFT JOIN customers cust ON c.cust_id = cust.cust_id
             LEFT JOIN invoices inv ON l.log_id = inv.log_id
             WHERE l.log_id = '$log_id'";

$res_head = mysqli_query($connect, $sql_head) or die("ຂໍ້ຜິດພາດ SQL: " . mysqli_error($connect));
$head = mysqli_fetch_array($res_head);

if (!$head) {
    die("<h3 style='text-align:center; padding-top:50px; font-family:\"Noto Sans Lao\";'>ບໍ່ພົບຂໍ້ມູນບິນເລກທີ: $log_id</h3>");
}

// 🎯 3. ໃຫ້ PHP ກວດຊອກຫາ ID ຫຼື ຊື່ຂອງຊ່າງ ຈາກຕາຕະລາງ service_logs ແບບອັດຕະໂນມັດ
$mechanic_display = '........................'; 
$found_mechanic_id = 0;

if (isset($head['mechanic_id']) && !empty($head['mechanic_id'])) {
    $found_mechanic_id = intval($head['mechanic_id']);
} elseif (isset($head['user_id']) && !empty($head['user_id'])) {
    $found_mechanic_id = intval($head['user_id']);
} elseif (isset($head['staff_id']) && !empty($head['staff_id'])) {
    $found_mechanic_id = intval($head['staff_id']);
}

if ($found_mechanic_id > 0) {
    $sql_mech = "SELECT fname, lname FROM users WHERE user_id = '$found_mechanic_id'";
    $res_mech = mysqli_query($connect, $sql_mech);
    if ($mech = mysqli_fetch_array($res_mech)) {
        $mechanic_display = trim($mech['fname'] . " " . $mech['lname']);
    }
} 
if ($mechanic_display == '........................' || empty($mechanic_display)) {
    if (isset($head['mechanic']) && !empty($head['mechanic'])) {
        $mechanic_display = $head['mechanic'];
    } elseif (isset($head['mechanic_name']) && !empty($head['mechanic_name'])) {
        $mechanic_display = $head['mechanic_name'];
    }
}

// ຄຳນວນຍອດລວມທັງໝົດ
$res_total = mysqli_query($connect, "SELECT SUM(total) as parts_sum FROM service_details WHERE log_id = '$log_id'");
$row_total = mysqli_fetch_array($res_total);
$total_parts_only = $row_total['parts_sum'] ? floatval($row_total['parts_sum']) : 0;
$labor_cost = isset($head['labor_cost']) ? floatval($head['labor_cost']) : 0;
$total_all_bill = $total_parts_only + $labor_cost;
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ໃບບິນສ້ອມແປງ #<?php echo str_pad($log_id, 5, "0", STR_PAD_LEFT); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        
        body { font-family: 'Noto Sans Lao', sans-serif; font-size: 14px; color: #333; background: #fff; padding: 10px; margin: 0; }
        .invoice-card { background: #fff; max-width: 100%; margin: 0 auto; padding: 10px; position: relative; page-break-inside: avoid; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #333; padding-bottom: 15px; margin-bottom: 15px; }
        .logo-area { display: flex; align-items: center; gap: 15px; }
        .logo-circle { width: 65px; height: 65px; background: #0056b3; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 28px; }
        
        .info-table { width: 100%; margin-bottom: 20px; border: none; }
        .info-table td { padding: 6px 4px; vertical-align: middle; }
        
        table.item-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .item-table th { background: #333; color: #fff; padding: 10px; text-align: center; font-weight: 700; }
        .item-table td { padding: 8px 10px; border: 1px solid #ddd; }
        
        .summary-flex { display: flex; justify-content: space-between; margin-top: 20px; align-items: flex-start; }
        .qr-payment { text-align: center; padding: 10px; border: 1px solid #eee; border-radius: 10px; background: #fafafa; width: 160px; }
        .qr-payment img { width: 110px; height: 110px; margin-bottom: 5px; border: 3px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .total-details { width: 340px; }
        .row-total { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; border-bottom: 1px dashed #eee; }
        .grand-total { border-top: 2px solid #333; border-bottom: none; margin-top: 8px; padding-top: 10px; font-weight: bold; font-size: 20px; color: #d9534f; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; }
        .badge-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        @page { size: A4; margin: 0; }
        @media print { 
            body * { visibility: hidden !important; }
            .invoice-card, .invoice-card * { visibility: visible !important; }
            .invoice-card { position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; padding: 15mm !important; box-sizing: border-box !important; page-break-inside: avoid !important; box-shadow: none !important; background: #fff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .header-flex { display: flex !important; }
            .logo-area { display: flex !important; }
            .summary-flex { display: flex !important; }
            .signature-area { display: flex !important; margin-top: 40px !important; }
        }
    </style>
</head>
<body>

<div class="invoice-card">
    <div class="header-flex">
        <div class="logo-area">
            <div class="logo-circle">GD</div>
            <div>
                <h2 style="margin:0; color:#0056b3; font-weight: 700;">ຮ້ານສ້ອມແປງລົດ ມິດ ການຊ່າງ</h2>
                <p style="margin:4px 0; font-size: 12px; color: #666;">ຊ່ຽວຊານດ້ານເຄື່ອງຈັກ ແລະ ອາໄຫຼ່ແທ້ 100%</p>
                <p style="margin:0; font-size: 13px;">📞 020 78894401 | 📍 ບ້ານ ນາດີ, ເມືອງ ແມດ, ແຂວງ ວຽງຈັນ</p>
            </div>
        </div>
        <div style="text-align: right;">
            <h1 style="margin:0; color: #333; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; font-size: 26px;">ໃບບິນ</h1>
            <p style="margin:4px 0; font-size: 14px;">ເລກທີ: <b>#<?php echo str_pad($log_id, 5, "0", STR_PAD_LEFT); ?></b></p>
            <p style="margin:0; font-size: 13px; color: #555;">ວັນທີ: <?php echo date('d/m/Y', strtotime($head['service_date'])); ?></p>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>ຜູ້ອອກບິນ:</strong></td>
            <td><span style="color: #28a745; font-weight: bold;"><i class="fa fa-user-check"></i> <?php echo $current_logged_in_staff; ?></span></td>
            <td><strong>ຊ່າງຮັບຜິດຊອບ:</strong></td>
            <td><span style="color: #0056b3; font-weight: bold;"><i class="fa fa-wrench"></i> <?php echo $mechanic_display; ?></span></td>
        </tr>
        
        <tr>
            <td width="15%"><strong>#ຊື່ລູກຄ້າ:</strong></td>
            <td width="40%"><?php echo $head['cust_name'] ?? 'ລູກຄ້າໜ້າຮ້ານ (POS)'; ?></td>
            <td width="15%"><strong>ທະບຽນລົດ:</strong></td>
            <td width="30%"><span style="background: #333; color:#fff; padding: 2px 8px; border-radius: 4px; font-weight: bold;"><?php echo $head['car_plate'] ?? '---'; ?></span></td>
        </tr>
        
        <tr>
            <td><strong>ເບີໂທ:</strong></td>
            <td><?php echo $head['tel'] ?? '---'; ?></td>
            <td><strong>ຍີ່ຫໍ້/ລຸ້ນ:</strong></td>
            <td><?php echo (!empty($head['car_brand']) || !empty($head['car_model'])) ? trim($head['car_brand'] . " " . $head['car_model']) : '---'; ?></td>
        </tr>

      <tr>
            <td><strong>ວິທີການຊຳລະ:</strong></td>
            <td>
                <span style="font-weight: 500; color: #555;">
                    <?php 
                    // ດຶງຂໍ້ມູນວິທີການຊຳລະຈາກຕາຕະລາງ invoices
                    $pay_method = isset($head['payment_type']) ? trim($head['payment_type']) : '';
                    $pay_method_lower = strtolower($pay_method);
                    
                    if ($pay_method_lower == 'cash' || $pay_method == 'ເງິນສົດ') {
                        echo '<i class="fa fa-money-bill-wave" style="color: #28a745;"></i> ເງິນສົດ';
                    } elseif ($pay_method_lower == 'transfer' || $pay_method == 'ໂອນ' || $pay_method == 'bcel' || $pay_method == 'qr' || $pay_method == 'ເງິນໂອນ') {
                        echo '<i class="fa fa-mobile-alt" style="color: #17a2b8;"></i> ໂອນຜ່ານທະນາຄານ';
                    } else {
                        echo !empty($pay_method) ? $pay_method : '---';
                    }
                    ?>
                </span>
            </td>

              <td style="vertical-align: top; padding-top: 8px;"><strong>ອາການລົດ:</strong></td>
            <td colspan="3" style="color: #d9534f; padding-top: 8px; font-weight: 500;">
                <?php echo !empty($head['symptoms']) ? $head['symptoms'] : 'ບໍ່ໄດ້ລະບຸອາການ (ຊື້ອະໄຫຼ່ໜ້າຮ້ານ)'; ?>
            </td>
        </tr>

    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="50">#</th>
                <th>ລາຍການອາໄຫຼ່</th>
                <th width="90">ຈຳນວນ</th>
                <th width="140">ລາຄາ/ໜ່ວຍ</th>
                <th width="150">ລວມ (ກີບ)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $res_det = mysqli_query($connect, "SELECT * FROM service_details WHERE log_id = '$log_id'");
            $i = 1;
            if (mysqli_num_rows($res_det) == 0) {
                echo "<tr><td colspan='5' style='text-align:center; color:#999; padding: 15px;'>ບໍ່ມີລາຍການອາໄຫຼ່ທີ່ປ່ຽນ</td></tr>";
            }
            while($row = mysqli_fetch_array($res_det)) {
                echo "<tr>
                        <td style='text-align:center;'>$i</td>
                        <td>".$row['description']."</td>
                        <td style='text-align:center;'>".number_format($row['qty'])."</td>
                        <td style='text-align:right;'>".number_format($row['price'])."</td>
                        <td style='text-align:right; font-weight:bold;'>".number_format($row['total'])."</td>
                      </tr>";
                $i++;
            }
            ?>
        </tbody>
    </table>

    <div class="summary-flex">
        <div class="qr-payment">
            <p style="margin: 0 0 6px; font-size: 11px; font-weight: bold; color: #0056b3; text-transform: uppercase;">ສະແກນຊຳລະເງິນ</p>
            <?php 
                $bank_name = "BCEL MyBank"; 
                $account_name = "MID KEOCHANDA"; 
                $account_number = "141122531890"; 
                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode("BANK:$bank_name|ACC:$account_number|NAME:$account_name|AMOUNT:$total_all_bill|BILL:$log_id");
            ?>
            <img src="<?php echo $qr_url; ?>" alt="QR Code Payment">
            <p style="margin: 3px 0 0; font-size: 11px; font-weight: bold; color: #333;"><?php echo $account_name; ?></p>
            <p style="margin: 1px 0 0; font-size: 10px; color: #666; font-family: monospace;"><?php echo $account_number; ?></p>
        </div>

        <div class="total-details">
            <div class="row-total">
                <span>ລວມຄ່າອາໄຫຼ່:</span>
                <span style="font-weight: 500;"><?php echo number_format($total_parts_only); ?> ກີບ</span>
            </div>
            <div class="row-total" style="color: #0056b3;">
                <span>ຄ່າແຮງງານຊ່າງ:</span>
                <span style="font-weight: 500;">+ <?php echo number_format($labor_cost); ?> ກີບ</span>
            </div>
            <div class="row-total grand-total">
                <span>ຍອດລວມສຸດທິ:</span>
                <span><?php echo number_format($total_all_bill); ?> ກີບ</span>
            </div>

            <?php if(isset($_GET['received']) && isset($_GET['change'])): ?>
            <div class="row-total" style="margin-top: 5px;">
                <span>ຮັບເງິນມາ (ເງິນສົດ):</span>
                <span style="font-weight: bold; color: #28a745;"><?php echo number_format(floatval($_GET['received'])); ?> ກີບ</span>
            </div>
            <div class="row-total">
                <span>ເງິນທອນ:</span>
                <span style="font-weight: bold; color: #d9534f;"><?php echo number_format(floatval($_GET['change'])); ?> ກີບ</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="signature-area" style="margin-top: 45px; display: flex; justify-content: space-around; text-align: center; gap: 10px;">
        <div style="width: 190px;">
            <p style="font-weight: bold; margin-bottom: 5px; font-size: 13px;">ລາຍເຊັນລູກຄ້າ</p>
            <div style="height: 40px; border-bottom: 1px solid #999; margin-bottom: 5px;"></div>
            <p style="font-size: 11px; color: #666; margin: 0;">ວັນທີ: ..../..../....</p>
        </div>

        <div style="width: 190px;">
            <p style="font-weight: bold; margin-bottom: 5px; font-size: 13px;">ຊ່າງຮັບຜິດຊອບ</p>
            <div style="height: 40px; border-bottom: 1px solid #999; margin-bottom: 5px;"></div>
            <p style="font-size: 12px; color: #0056b3; margin: 0; font-weight: bold;">( ຊ່າງ: <?php echo $mechanic_display; ?> )</p>
            <p style="font-size: 11px; color: #666; margin: 2px 0 0;">ຮ້ານ ມິດ ການຊ່າງ</p>
        </div>

        <div style="width: 190px;">
            <p style="font-weight: bold; margin-bottom: 5px; font-size: 13px;">ພະນັກງານອອກບິນ</p>
            <div style="height: 40px; border-bottom: 1px solid #999; margin-bottom: 5px;"></div>
            <p style="font-size: 12px; color: #28a745; margin: 0; font-weight: bold;">( ຜູ້ອອກບິນ: <?php echo $current_logged_in_staff; ?> )</p>
            <p style="font-size: 11px; color: #666; margin: 2px 0 0;">ຮ້ານ ມິດ ການຊ່າງ</p>
        </div>
    </div>
    
    <p style="text-align: center; margin-top: 25px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 12px;">
        * ຂອບໃຈທີ່ເລືອກໃຊ້ບໍລິການກັບພວກເຮົາ. ອາໄຫຼ່ທີ່ປ່ຽນທຸກຊິ້ນມີການຮັບປະກັນເປັນເວລາ 30 ວັນ *
    </p>
</div>

</body>
</html>
<?php
}
?>