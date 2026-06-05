<?php
session_start();
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<script>alert('ກະລຸນາລ໋ອກອິນກ່ອນ');location='index.php';</script>";
    exit();
} else {
    include("cennect_dbstock.php");

    // 1. ງົບປະມານຊື້ອາໄຫຼ່ເຂົ້າສະຕັອກທັງໝົດ
    $bamount       = mysqli_query($connect, "SELECT SUM(qty_bought * buyer_price) FROM part_purchases") or die("<b>Error ບ໋ອກຕົ້ນທຶນ:</b> " . mysqli_error($connect));
    $show_bamount  = mysqli_fetch_row($bamount);
    $total_cost    = $show_bamount[0] ?? 0;

    // 2. ລາຍຮັບອູ່ທັງໝົດ
    $sql_labor_total = mysqli_query($connect, "SELECT SUM(labor_cost) FROM service_logs WHERE status = 'success'");
    $row_labor_total = mysqli_fetch_row($sql_labor_total);
    $total_labor     = $row_labor_total[0] ?? 0;

    $sql_parts_total = mysqli_query($connect, "SELECT SUM(d.total) FROM service_details d INNER JOIN service_logs l ON d.service_id = l.log_id WHERE l.status = 'success'");
    $row_parts_total = mysqli_fetch_row($sql_parts_total);
    $total_parts     = $row_parts_total[0] ?? 0;

    $total_sales     = $total_labor + $total_parts;

    // 3. ລາຍຮັບຂອງມື້ນີ້
    $sql_labor_today = mysqli_query($connect, "SELECT SUM(labor_cost) FROM service_logs WHERE status = 'success' AND completed_at LIKE CONCAT(CURDATE(), '%')");
    $row_labor_today = mysqli_fetch_row($sql_labor_today);
    $today_labor     = $row_labor_today[0] ?? 0;

    $sql_parts_today = mysqli_query($connect, "SELECT SUM(d.total) FROM service_details d INNER JOIN service_logs l ON d.service_id = l.log_id WHERE l.status = 'success' AND l.completed_at LIKE CONCAT(CURDATE(), '%')");
    $row_parts_today = mysqli_fetch_row($sql_parts_today);
    $today_parts     = $row_parts_today[0] ?? 0;

    $today_sales     = $today_labor + $today_parts;

    // 4. ສິນຄ້າໃກ້ຈະໝົດ (< 5)
    $countqyt      = mysqli_query($connect, "SELECT COUNT(part_id) FROM parts_profile WHERE qty_stock >= 1 AND qty_stock < 5") or die("<b>Error ບ໋ອກອາໄຫຼ່ໃກ້ໝົດ:</b> " . mysqli_error($connect));
    $show_countqty = mysqli_fetch_row($countqyt);
    $qty_alert     = $show_countqty[0] ?? 0;

    // 5. ສິນຄ້າໝົດສະຕັອກ
    $countqytz         = mysqli_query($connect, "SELECT COUNT(part_id) FROM parts_profile WHERE qty_stock <= 0") or die("<b>Error ບ໋ອກອາໄຫຼ່ໝົດແລ້ວ:</b> " . mysqli_error($connect));
    $show_countqtyzero = mysqli_fetch_row($countqytz);
    $qty_zero          = $show_countqtyzero[0] ?? 0;

    // 6. ຄິດໄລ່ກຳໄລລວມ (30% ຂອງຍອດຂາຍ)
    $income_kamlai = $total_sales * 0.30; 

    // 7. ຄິດໄລ່ກຳໄລຂອງມື້ນີ້ (30% ຂອງຍອດຂາຍມື້ນີ້)
    $profit_today = $today_sales * 0.30; 


    // ເພີ່ມສ່ວນການດຶງຂໍ້ມູນຈຳນວນລົດສ້ອມແປງ

    // 8. ລົດທີ່ກຳລັງແປງທັງໝົດ
    $sql_pending_total = mysqli_query($connect, "SELECT COUNT(log_id) FROM service_logs WHERE status = 'pending'");
    $row_pending_total = mysqli_fetch_row($sql_pending_total);
    $pending_total     = $row_pending_total[0] ?? 0;

    // 9. ລົດທີ່ກຳລັງແປງມື້ນີ້ (ເປີດບິນມື້ນີ້ ແລະ ຍັງແປງບໍ່ແລ້ວ)
    $sql_pending_today = mysqli_query($connect, "SELECT COUNT(log_id) FROM service_logs WHERE status = 'pending' AND service_date LIKE CONCAT(CURDATE(), '%')");
    $row_pending_today = mysqli_fetch_row($sql_pending_today);
    $pending_today     = $row_pending_today[0] ?? 0;

    // 10. ແປງສຳເລັດທັງໝົດ
    $sql_success_total = mysqli_query($connect, "SELECT COUNT(log_id) FROM service_logs WHERE status = 'success'");
    $row_success_total = mysqli_fetch_row($sql_success_total);
    $success_total     = $row_success_total[0] ?? 0;

    // 11. ແປງສຳເລັດມື້ນີ້
    $sql_success_today = mysqli_query($connect, "SELECT COUNT(log_id) FROM service_logs WHERE status = 'success' AND completed_at LIKE CONCAT(CURDATE(), '%')");
    $row_success_today = mysqli_fetch_row($sql_success_today);
    $success_today     = $row_success_today[0] ?? 0;
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ລະບົບບໍລິຫານ ຮ້ານສ້ອມແປງລົດ</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        body, * { 
            font-family: 'Noto Sans Lao', sans-serif; 
        }
        body { 
            background-color: #f8fafc; 
            padding: 25px;
        }

        /* ຫົວຂໍ້ໜ້າເວັບ */
        .premium-header {
            background: #ffffff;
            padding: 24px 32px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .premium-header h4 {
            font-size: 22px;
            color: #1e293b;
            font-weight: 700;
            margin: 0;
        }
        .premium-header p {
            font-size: 13px;
            color: #64748b;
            margin: 4px 0 0 0;
        }

        /* ບ໋ອກກາດ (Dashboard Cards) */
        .dash-card {
            position: relative;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 24px;
        }
        
        .dash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        }
        
        .dash-card .card-body {
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .dash-card .card-info-side {
            flex-grow: 1;
        }

        .dash-card .card-title {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .dash-card .card-value {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        
        .dash-card .card-value span.currency {
            font-size: 14px;
            font-weight: 500;
            color: #94a3b8;
            margin-left: 4px;
        }
        
        /* ວົງມົນໃສ່ໄອຄອນ */
        .dash-card .card-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        /* ລິ້ງຄ໌ທາງລຸ່ມກາດ */
        .dash-card .card-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            background: #fafafa;
            border-top: 1px solid #f1f5f9;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .dash-card:hover .card-link {
            background: #f1f5f9;
        }

        /* ລະບົບສີແຍກແຕ່ລະກາດ */
        .card-cost .card-icon-box { background: #fef3c7; color: #d97706; }
        .card-cost .card-link { color: #d97706; }

        .card-sales .card-icon-box { background: #dcfce7; color: #15803d; }
        .card-sales .card-link { color: #15803d; }
        
        .card-today .card-icon-box { background: #ccfbf1; color: #0f766e; }
        .card-today .card-link { color: #0f766e; }
        
        .card-alert .card-icon-box { background: #fee2e2; color: #b91c1c; }
        .card-alert .card-link { color: #b91c1c; }
        
        .card-zero .card-icon-box { background: #f1f5f9; color: #475569; }
        .card-zero .card-link { color: #475569; }
        
        .card-profit .card-icon-box { background: #ede9fe; color: #6d28d9; }
        .card-profit .card-link { color: #6d28d9; }
        
        .card-profit-today .card-icon-box { background: #fae8ff; color: #a21caf; }
        .card-profit-today .card-link { color: #a21caf; }

        .card-pending-tot .card-icon-box { background: #fff7ed; color: #ea580c; }
        .card-pending-tot .card-link { color: #ea580c; }

        .card-pending-td .card-icon-box { background: #fef2f2; color: #ef4444; }
        .card-pending-td .card-link { color: #ef4444; }

        .card-success-tot .card-icon-box { background: #f0fdf4; color: #16a34a; }
        .card-success-tot .card-link { color: #16a34a; }

        .card-success-td .card-icon-box { background: #eff6ff; color: #2563eb; }
        .card-success-td .card-link { color: #2563eb; }

        /* ໄຟສັນຍານແຈ້ງເຕືອນ Online */
        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #22c55e;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse 1.6s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        
        <div class="premium-header flex-wrap gap-3">
            <div>
                <h4><i class="fas fa-cubes text-muted me-2"></i> ແຜງຄວບຄຸມ</h4>
                <p>ສະຫຼຸບພາບລວມດ້ານການເງິນ ແລະ ສະຖານະຄັງສິນຄ້າ</p>
            </div>
            <div>
                <span class="badge bg-white text-dark py-2 px-3 border rounded-pill d-flex align-items-center gap-2" style="font-size: 13px;">
                    <span class="pulse-dot"></span> ລະບົບເຮັດວຽກປົກກະຕິ
                </span>
            </div>
        </div>

        <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-tools me-2"></i> ສະຖານະການສ້ອມແປງລົດ</h5>
        <div class="row mb-2">
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-pending-tot">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ລົດກຳລັງແປງທັງໝົດ</div>
                            <div class="card-value"><?= number_format($pending_total); ?><span class="currency">ຄັນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-spinner fa-spin"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php" class="card-link">
                        <span>ເບິ່ງລາຍການກຳລັງແປງ</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-pending-td">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ກຳລັງແປງມື້ນີ້</div>
                            <div class="card-value"><?= number_format($pending_today); ?><span class="currency">ຄັນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-wrench"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php" class="card-link">
                        <span>ກວດບິນເປີດໃໝ່ມື້ນີ້</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-success-tot">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ແປງສຳເລັດທັງໝົດ</div>
                            <div class="card-value"><?= number_format($success_total); ?><span class="currency">ຄັນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-check-circle"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php" class="card-link">
                        <span>ປະຫວັດການແປງທັງໝົດ</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-success-td">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ແປງສຳເລັດມື້ນີ້</div>
                            <div class="card-value"><?= number_format($success_today); ?><span class="currency">ຄັນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-history"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php?filter=today" class="card-link">
                        <span>ເບິ່ງລາຍການສຳເລັດມື້ນີ້</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-chart-bar me-2"></i> ສະຫຼຸບການເງິນ ແລະ ຄັງສິນຄ້າ</h5>
        <div class="row">
            
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-cost">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ຕົ້ນທຶນສະຕັອກທັງໝົດ</div>
                            <div class="card-value"><?= number_format($total_cost); ?><span class="currency">ກີບ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-wallet"></i></div>
                    </div>
                    <a href="part_purchases/form_part_purchases.php" class="card-link">
                        <span>ລາຍງານການຈັດຊື້</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-sales">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ລາຍຮັບອູ່ທັງໝົດ</div>
                            <div class="card-value"><?= number_format($total_sales); ?><span class="currency">ກີບ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php" class="card-link">
                        <span>ປະຫວັດການປິດບິນ</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-today">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ລາຍຮັບສະເພາະມື້ນີ້</div>
                            <div class="card-value"><?= number_format($today_sales); ?><span class="currency">ກີບ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-calendar-day"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php?filter=today" class="card-link">
                        <span>ກວດເຊັກບິນມື້ນີ້</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-alert">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ອາໄຫຼ່ໃກ້ຈະໝົດ (ຕ່ຳກ່ວາ5)</div>
                            <div class="card-value" style="color: #b91c1c;"><?= number_format($qty_alert); ?><span class="currency">ລາຍການ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-exclamation-triangle"></i></div>
                    </div>
                    <a href="parts_profile/form_parts_profile.php" class="card-link">
                        <span>ລາຍການຄວນເຕີມ</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-zero">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ອາໄຫຼ່ທີ່ໝົດແລ້ວ</div>
                            <div class="card-value" style="color: #475569;"><?= number_format($qty_zero); ?><span class="currency">ລາຍການ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-box-open"></i></div>
                    </div>
                    <a href="parts_profile/form_parts_profile.php" class="card-link">
                        <span>ຈັດການສິນຄ້າໝົດ</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-profit">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ປະມານກຳໄລລວມ</div>
                            <div class="card-value"><?= number_format($income_kamlai); ?><span class="currency">ກີບ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-chart-line"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php" class="card-link">
                        <span>ວິເຄາະກຳໄລລວມ</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-profit-today">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ກຳໄລສະເພາະມື້ນີ້</div>
                            <div class="card-value"><?= number_format($profit_today); ?><span class="currency">ກີບ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-chart-pie"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php?filter=today" class="card-link">
                        <span>ວິເຄາະກຳໄລມື້ນີ້</span> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div> 
    </div> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
}
?>