<?php
include("../cennect_dbstock.php");
mysqli_set_charset($connect, "utf8"); 

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($service_id <= 0) {
    die("<h3 style='text-align:center; padding-top:50px; font-family:\"Noto Sans Lao\";'>ບໍ່ພົບຂໍ້ມູນບິນເລກທີ: $service_id</h3>");
}

// 1. ດຶງຂໍ້ມູນຫົວບິນ
$sql_head = "SELECT 
                l.log_id, l.service_date, l.labor_cost, l.symptoms,
                c.car_plate, c.car_brand, c.car_model, 
                cust.cust_name, cust.tel 
             FROM service_logs l
             LEFT JOIN cars c ON l.car_id = c.car_id
             LEFT JOIN customers cust ON c.cust_id = cust.cust_id
             WHERE l.log_id = '$service_id'";

$res_head = mysqli_query($connect, $sql_head);
$head = mysqli_fetch_array($res_head);

if (!$head) {
    die("<h3 style='text-align:center; padding-top:50px; font-family:\"Noto Sans Lao\";'>ບໍ່ພົບຂໍ້ມູນບິນເລກທີ: $service_id</h3>");
}

// ຄຳນວນຍອດລວມທັງໝົດກ່ອນເພື່ອໃຊ້ໃນ QR Code
$res_total = mysqli_query($connect, "SELECT SUM(total) as parts_sum FROM service_details WHERE service_id = '$service_id'");
$row_total = mysqli_fetch_array($res_total);
$total_parts_only = $row_total['parts_sum'] ? floatval($row_total['parts_sum']) : 0;
$labor_cost = isset($head['labor_cost']) ? floatval($head['labor_cost']) : 0;
$total_all_bill = $total_parts_only + $labor_cost;
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ໃບບິນສ້ອມແປງ #<?php echo str_pad($service_id, 5, "0", STR_PAD_LEFT); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
        
        body { font-family: 'Noto Sans Lao', sans-serif; font-size: 14px; color: #333; background: #fff; padding: 10px; margin: 0; }
        .invoice-card { background: #fff; max-width: 100%; margin: 0 auto; padding: 10px; position: relative; page-break-inside: avoid; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #333; padding-bottom: 15px; margin-bottom: 15px; }
        .logo-area { display: flex; align-items: center; gap: 15px; }
        .logo-circle { width: 65px; height: 65px; background: #0056b3; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 28px; }
        
        .info-table { width: 100%; margin-bottom: 20px; border: none; }
        .info-table td { padding: 5px 4px; vertical-align: middle; }
        
        table.item-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .item-table th { background: #333; color: #fff; padding: 10px; text-align: center; font-weight: 700; }
        .item-table td { padding: 8px 10px; border: 1px solid #ddd; }
        
        .summary-flex { display: flex; justify-content: space-between; margin-top: 20px; align-items: flex-start; }
        .qr-payment { text-align: center; padding: 10px; border: 1px solid #eee; border-radius: 10px; background: #fafafa; width: 160px; }
        .qr-payment img { width: 110px; height: 110px; margin-bottom: 5px; border: 3px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .total-details { width: 340px; }
        .row-total { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; border-bottom: 1px dashed #eee; }
        .grand-total { border-top: 2px solid #333; border-bottom: none; margin-top: 8px; padding-top: 10px; font-weight: bold; font-size: 20px; color: #d9534f; }
        
        /* 🌟 ຕັ້ງຄ່າຂອບເຈ້ຍເປັນ 0 ເພື່ອຕັດຂໍ້ຄວາມ URL (localhost/...) ທີ່ Browser ແນບມາອອກໃຫ້ໝົດ */
        @page {
            size: A4;
            margin: 0;
        }
        
        @media print { 
            /* 1. ສັ່ງເຊື່ອງທຸກໆຢ່າງໃນໜ້າເວັບທັງໝົດແບບເດັດຂາດ (ລວມເຖິງ menu_admin ທີ່ຫຼຸດເຂົ້າມາ) */
            body * { 
                visibility: hidden !important; 
            }
            
            /* 2. ເປີດໃຫ້ສະແດງສະເພາະກ່ອງໃບບິນ .invoice-card ແລະ ເນື້ອຫາທາງໃນຂອງມັນເທົ່ານັ້ນ */
            .invoice-card, .invoice-card * { 
                visibility: visible !important; 
            }
            
            /* 3. ຈັດຕຳແໜ່ງໃບບິນໃໝ່ໃຫ້ໄປຢູ່ຈຸດເທິງສຸດ ແລະ ເພີ່ມຂອບເຈ້ຍທາງໃນແທນເພື່ອໃຫ້ຢູ່ໜ້າດຽວພໍດີ */
            .invoice-card { 
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                padding: 15mm !important; /* ຂອບເຈ້ຍດ້ານໃນ */
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                box-shadow: none !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            
            /* ບັງຄັບຄືນຄ່າການສະແດງຜົນແບບ Flexbox ໃຫ້ເຮັດວຽກໄດ້ປົກກະຕິຕອນປິ່ນ */
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
                <h2 style="margin:0; color:#0056b3; font-weight: 700;">ຮ້ານສ້ອມແປງລົດ ເກັ່ງດີ (Garage)</h2>
                <p style="margin:4px 0; font-size: 12px; color: #666;">ຊ່ຽວຊານດ້ານເຄື່ອງຈັກ ແລະ ອາໄຫຼ່ແທ້ 100%</p>
                <p style="margin:0; font-size: 13px;">📞 020 77xxxxxx | 📍 ບ້ານ ສີຫອມ, ວຽງຈັນ</p>
            </div>
        </div>
        <div style="text-align: right;">
            <h1 style="margin:0; color: #333; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; font-size: 26px;">ໃບບິນ</h1>
            <p style="margin:4px 0; font-size: 14px;">ເລກທີ: <b>#<?php echo str_pad($service_id, 5, "0", STR_PAD_LEFT); ?></b></p>
            <p style="margin:0; font-size: 13px; color: #555;">ວັນທີ: <?php echo date('d/m/Y', strtotime($head['service_date'])); ?></p>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>#ຊື່ລູກຄ້າ:</strong></td>
            <td width="40%"><?php echo $head['cust_name'] ?? '---'; ?></td>
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
            <td style="vertical-align: top; padding-top: 8px;"><strong>ອາການລົດ:</strong></td>
            <td colspan="3" style="color: #d9534f; padding-top: 8px; font-weight: 500;">
                <?php echo !empty($head['symptoms']) ? $head['symptoms'] : 'ບໍ່ໄດ້ລະບຸອາການ'; ?>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="50">#</th>
                <th>ລາຍການອາໄຫຼ່ / ວຽກທີ່ເຮັດ</th>
                <th width="90">ຈຳນວນ</th>
                <th width="140">ລາຄາ/ໜ່ວຍ</th>
                <th width="150">ລວມ (ກີບ)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $res_det = mysqli_query($connect, "SELECT * FROM service_details WHERE service_id = '$service_id'");
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
                $account_name = "KEONGDEE GARAGE"; 
                $account_number = "160123456789"; 
                
                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . 
                          urlencode("BANK:$bank_name|ACC:$account_number|NAME:$account_name|AMOUNT:$total_all_bill|BILL:$service_id");
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
        </div>
    </div>

    <div class="signature-area" style="margin-top: 45px; display: flex; justify-content: space-around; text-align: center;">
        <div style="width: 220px;">
            <p style="font-weight: bold; margin-bottom: 5px;">ລາຍເຊັນລູກຄ້າ</p>
            <div style="height: 40px; border-bottom: 1px solid #999; margin-bottom: 5px;"></div>
            <p style="font-size: 12px; color: #666;">ວັນທີ: ..../..../....</p>
        </div>
        <div style="width: 220px;">
            <p style="font-weight: bold; margin-bottom: 5px;">ຜູ້ຮັບເງິນ / ຊ່າງສ້ອມແປງ</p>
            <div style="height: 40px; border-bottom: 1px solid #999; margin-bottom: 5px;"></div>
            <p style="font-size: 12px; color: #666;">(ຮ້ານ ເກັ່ງດີ ການຊ່າງ)</p>
        </div>
    </div>
    
    <p style="text-align: center; margin-top: 20px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 12px;">
        * ຂອບໃຈທີ່ເລືອກໃຊ້ບໍລິການກັບພວກເຮົາ. ອາໄຫຼ່ທີ່ປ່ຽນທຸກຊິ້ນມີການຮັບປະກັນເປັນເວລາ 30 ວັນ *
    </p>
</div>

</body>
</html>