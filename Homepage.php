<?php
session_start();
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<script>alert('ກະລຸນາລ໋ອກອິນກ່ອນ');location='index.php';</script>";
    exit();
} else {
    include("cennect_dbstock.php");

    // ✨ ບັນທັດສຳຄັນ: ເປີດການຮອງຮັບພາສາລາວໃນການ Query ຖານຂໍ້ມູນ
    mysqli_set_charset($connect, "utf8mb4");

    // 1. ງົບປະມານຊື້ອາໄຫຼ່ເຂົ້າສະຕັອກທັງໝົດ
    $bamount       = mysqli_query($connect, "SELECT SUM(qty_bought * buyer_price) FROM part_purchases") or die("<b>Error ບ໋ອກຕົ້ນທຶນ:</b> " . mysqli_error($connect));
    $show_bamount  = mysqli_fetch_row($bamount);
    $total_cost    = $show_bamount[0] ?? 0;

    // 2. ລາຍຮັບອູ່ທັງໝົດ
    $sql_labor_total = mysqli_query($connect, "SELECT SUM(labor_cost) FROM service_logs WHERE status = 'success'");
    $row_labor_total = mysqli_fetch_row($sql_labor_total);
    $total_labor     = $row_labor_total[0] ?? 0;

    $sql_parts_total = mysqli_query($connect, "SELECT SUM(d.total) FROM service_details d INNER JOIN service_logs l ON d.log_id = l.log_id WHERE l.status = 'success'");
    $row_parts_total = mysqli_fetch_row($sql_parts_total);
    $total_parts     = $row_parts_total[0] ?? 0;

    $total_sales     = $total_labor + $total_parts;

    // 3. ລາຍຮັບຂອງມື້ນີ້
    $sql_labor_today = mysqli_query($connect, "SELECT SUM(labor_cost) FROM service_logs WHERE status = 'success' AND completed_at LIKE CONCAT(CURDATE(), '%')");
    $row_labor_today = mysqli_fetch_row($sql_labor_today);
    $today_labor     = $row_labor_today[0] ?? 0;

    $sql_parts_today = mysqli_query($connect, "SELECT SUM(d.total) FROM service_details d INNER JOIN service_logs l ON d.log_id = l.log_id WHERE l.status = 'success' AND l.completed_at LIKE CONCAT(CURDATE(), '%')");
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

    // 8. ລົດທີ່ກຳລັງແປງທັງໝົດ
    $sql_pending_total = mysqli_query($connect, "SELECT COUNT(log_id) FROM service_logs WHERE status = 'pending'");
    $row_pending_total = mysqli_fetch_row($sql_pending_total);
    $pending_total     = $row_pending_total[0] ?? 0;

    // 9. ລົດທີ່ກຳລັງແປງມື້ນີ້
    $sql_pending_today = mysqli_query($connect, "SELECT COUNT(log_id) FROM service_logs WHERE status = 'pending' AND DATE(created_at) = CURDATE()");
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

    // 12. ຈຳນວນພະນັກງານທັງໝົດ
    $sql_user_total = mysqli_query($connect, "SELECT COUNT(user_id) FROM users") or die("<b>Error ພະນັກງານທັງໝົດ:</b> " . mysqli_error($connect));
    $row_user_total = mysqli_fetch_row($sql_user_total);
    $total_users    = $row_user_total[0] ?? 0;

    // 13. ຈຳນວນຜູ້ບໍລິຫານ
    $sql_admin_total = mysqli_query($connect, "SELECT COUNT(user_id) FROM users WHERE status = 'ຜູ້ບໍລິຫານ'") or die("<b>Error ຜູ້ບໍລິຫານ:</b> " . mysqli_error($connect));
    $row_admin_total = mysqli_fetch_row($sql_admin_total);
    $total_admins    = $row_admin_total[0] ?? 0;

    // 14. ຈຳນວນພະນັກງານທົ່ວໄປ
    $sql_staff_total = mysqli_query($connect, "SELECT COUNT(user_id) FROM users WHERE status = 'ພະນັກງານ'") or die("<b>Error ພະນັກງານທົ່ວໄປ:</b> " . mysqli_error($connect));
    $row_staff_total = mysqli_fetch_row($sql_staff_total);
    $total_staff     = $row_staff_total[0] ?? 0;

    // 15. ຈຳນວນຊ່າງແປງລົດ
    $sql_mechanic_total = mysqli_query($connect, "SELECT COUNT(user_id) FROM users WHERE status = 'ຊ່າງແປງລົດ'") or die("<b>Error ຊ່າງແປງລົດ:</b> " . mysqli_error($connect));
    $row_mechanic_total = mysqli_fetch_row($sql_mechanic_total);
    $total_mechanics    = $row_mechanic_total[0] ?? 0;

    // ==========================================
    // 📊 ດຶງຂໍ້ມູນສຳລັບກຣາຟ (Charts Data)
    // ==========================================

    // ກຣາຟເສັ້ນ: ຍອດຂາຍ 7 ມື້ຫຼ້າສຸດ
    $chart_dates = [];
    $chart_sales = [];
    for($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        
        // ຍອດອາໄຫຼ່
        $q_p = mysqli_query($connect, "SELECT SUM(d.total) FROM service_details d INNER JOIN service_logs l ON d.log_id = l.log_id WHERE l.status = 'success' AND l.completed_at LIKE '$d%'");
        $sum_p = mysqli_fetch_row($q_p)[0] ?? 0;
        
        // ຍອດຄ່າແຮງ
        $q_l = mysqli_query($connect, "SELECT SUM(labor_cost) FROM service_logs WHERE status = 'success' AND completed_at LIKE '$d%'");
        $sum_l = mysqli_fetch_row($q_l)[0] ?? 0;
        
        $chart_dates[] = date('d/m', strtotime($d));
        $chart_sales[] = $sum_p + $sum_l;
    }

    // ກຣາຟວົງມົນ: ອາໄຫຼ່ຂາຍດີ 5 ອັນດັບທຳອິດ
    $top_parts_names = [];
    $top_parts_qty = [];
    $q_top = mysqli_query($connect, "
        SELECT p.part_name, SUM(d.qty) as total_qty 
        FROM service_details d 
        INNER JOIN service_logs l ON d.log_id = l.log_id 
        LEFT JOIN parts_profile p ON d.part_id = p.part_id 
        WHERE l.status = 'success' 
        GROUP BY d.part_id 
        ORDER BY total_qty DESC 
        LIMIT 5
    ");
    while($r = mysqli_fetch_assoc($q_top)) {
        // ຕັດຄຳຖ້າຊື່ອາໄຫຼ່ຍາວເກີນໄປ ເພື່ອໃຫ້ກຣາຟງາມ
        $name = mb_strimwidth($r['part_name'] ?? 'ບໍ່ລະບຸຊື່', 0, 20, "...");
        $top_parts_names[] = $name;
        $top_parts_qty[] = $r['total_qty'];
    }
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
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body, * { 
            font-family: 'Noto Sans Lao', sans-serif; 
        }
        body { 
            background-color: #f4f7fe; 
            padding: 20px;
        }

        .premium-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 24px 32px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .premium-header h4 { font-size: 24px; color: #1e293b; font-weight: 700; margin: 0; }
        .premium-header p { font-size: 14px; color: #64748b; margin: 5px 0 0 0; }

        /* Card ພາບລວມຕ່າງໆ */
        .dash-card {
            background: #ffffff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .dash-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .dash-card .card-body { padding: 22px; display: flex; align-items: center; justify-content: space-between; }
        .dash-card .card-title { font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
        .dash-card .card-value { font-size: 24px; font-weight: 700; color: #0f172a; line-height: 1.2; }
        .dash-card .card-value span.currency { font-size: 13px; font-weight: 500; color: #94a3b8; margin-left: 4px; }
        .dash-card .card-icon-box {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 22px;
        }
        .dash-card .card-link {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 22px; background: #f8fafc; font-size: 13px; font-weight: 500; text-decoration: none;
        }

        /* ສີສັນສະເພາະຂອງແຕ່ລະ Card */
        .card-pending-tot .card-icon-box { background: #fff7ed; color: #ea580c; }
        .card-pending-tot .card-link { color: #ea580c; }
        .card-pending-td .card-icon-box { background: #fef2f2; color: #ef4444; }
        .card-pending-td .card-link { color: #ef4444; }
        .card-success-tot .card-icon-box { background: #f0fdf4; color: #16a34a; }
        .card-success-tot .card-link { color: #16a34a; }
        .card-success-td .card-icon-box { background: #eff6ff; color: #2563eb; }
        .card-success-td .card-link { color: #2563eb; }

        .card-cost .card-icon-box { background: #fef3c7; color: #d97706; }
        .card-cost .card-link { color: #d97706; }
        .card-sales .card-icon-box { background: #dcfce7; color: #15803d; }
        .card-sales .card-link { color: #15803d; }
        .card-today .card-icon-box { background: #ccfbf1; color: #0f766e; }
        .card-today .card-link { color: #0f766e; }
        .card-profit .card-icon-box { background: #ede9fe; color: #6d28d9; }
        .card-profit .card-link { color: #6d28d9; }
        .card-profit-today .card-icon-box { background: #fae8ff; color: #a21caf; }
        .card-profit-today .card-link { color: #a21caf; }

        .card-alert .card-icon-box { background: #fee2e2; color: #b91c1c; }
        .card-alert .card-link { color: #b91c1c; }
        .card-zero .card-icon-box { background: #f1f5f9; color: #475569; }
        .card-zero .card-link { color: #475569; }

        .card-users-tot .card-icon-box { background: #e0f2fe; color: #0284c7; }
        .card-users-tot .card-link { color: #0284c7; }
        .card-admin .card-icon-box { background: #f3e8ff; color: #7e22ce; }
        .card-admin .card-link { color: #7e22ce; }
        .card-staff .card-icon-box { background: #e2e8f0; color: #475569; }
        .card-staff .card-link { color: #475569; }
        .card-mechanic .card-icon-box { background: #ffedd5; color: #ea580c; }
        .card-mechanic .card-link { color: #ea580c; }

        /* Animation ສຳລັບຈຸດສີຂຽວ */
        .pulse-dot { width: 8px; height: 8px; background-color: #22c55e; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite; }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        /* Section Title */
        .section-title { font-size: 18px; font-weight: 700; color: #334155; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .section-title i { color: #3b82f6; }
        
        /* Chart Container */
        .chart-card { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); height: 100%; border: 1px solid rgba(226, 232, 240, 0.8);}
    </style>
</head>
<body>

    <div class="container-fluid">
        
        <div class="premium-header flex-wrap gap-3">
            <div>
                <h4><i class="fas fa-cubes text-primary me-2"></i> ແຜງຄວບຄຸມ</h4>
                <p>ສະຫຼຸບພາບລວມດ້ານການເງິນ, ການຂາຍ ແລະ ສະຖານະຄັງສິນຄ້າອາໄຫຼ່</p>
            </div>
            <div>
                <span class="badge bg-white text-dark py-2 px-3 border rounded-pill d-flex align-items-center gap-2" style="font-size: 13px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                    <span class="pulse-dot"></span> ລະບົບອັບເດດລ່າສຸດອັດຕະໂນມັດ
                </span>
            </div>
        </div>

        <h5 class="section-title"><i class="fas fa-chart-area"></i> ພາບລວມຍອດຂາຍ ແລະ ສິນຄ້າຂາຍດີ</h5>
        <div class="row mb-4">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <div class="chart-card">
                    <h6 class="fw-bold text-secondary mb-3">ສະຖິຕິຍອດຂາຍ 7 ມື້ລ່າສຸດ</h6>
                    <div style="height: 300px;">
                        <canvas id="salesLineChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="chart-card">
                    <h6 class="fw-bold text-secondary mb-3">ອາໄຫຼ່ທີ່ຂາຍດີທີ່ສຸດ 5 ອັນດັບ</h6>
                    <div style="height: 300px; display: flex; justify-content: center;">
                        <?php if(empty($top_parts_names)): ?>
                            <div class="d-flex align-items-center justify-content-center w-100 text-muted">
                                <p><i class="fas fa-box-open me-2"></i>ຍັງບໍ່ມີຂໍ້ມູນການຂາຍ</p>
                            </div>
                        <?php else: ?>
                            <canvas id="topPartsChart"></canvas>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="section-title"><i class="fas fa-wallet"></i> ສະຫຼຸບການເງິນ ແລະ ຄັງສິນຄ້າອາໄຫຼ່</h5>
        <div class="row mb-2">
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-sales">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ລາຍຮັບລວມທັງໝົດ</div>
                            <div class="card-value"><?= number_format($total_sales); ?><span class="currency">ກີບ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php" class="card-link"><span>ປະຫວັດການປິດບິນ</span> <i class="fas fa-arrow-right"></i></a>
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
                    <a href="service_logs/form_service_logs.php?filter=today" class="card-link"><span>ກວດເຊັກບິນມື້ນີ້</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-profit">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ປະມານກຳໄລລວມ (30%)</div>
                            <div class="card-value"><?= number_format($income_kamlai); ?><span class="currency">ກີບ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-chart-line"></i></div>
                    </div>
                    <a href="#" class="card-link"><span>ລາຍງານກຳໄລ</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-profit-today">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ກຳໄລມື້ນີ້</div>
                            <div class="card-value"><?= number_format($profit_today); ?><span class="currency">ກີບ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-chart-pie"></i></div>
                    </div>
                    <a href="#" class="card-link"><span>ລາຍງານກຳໄລມື້ນີ້</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-cost">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ຕົ້ນທຶນສະຕັອກທັງໝົດ</div>
                            <div class="card-value"><?= number_format($total_cost); ?><span class="currency">ກີບ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-boxes"></i></div>
                    </div>
                    <a href="part_purchases/form_part_purchases.php" class="card-link"><span>ລາຍງານການຈັດຊື້</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-alert">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ອາໄຫຼ່ໃກ້ຈະໝົດ (< 5)</div>
                            <div class="card-value" style="color: #b91c1c;"><?= number_format($qty_alert); ?><span class="currency">ລາຍການ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-exclamation-triangle"></i></div>
                    </div>
                    <a href="parts_profile/form_parts_profile.php" class="card-link"><span>ລາຍການຄວນເຕີມ</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-zero">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ອາໄຫຼ່ທີ່ໝົດແລ້ວ (0)</div>
                            <div class="card-value" style="color: #475569;"><?= number_format($qty_zero); ?><span class="currency">ລາຍການ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-box-open"></i></div>
                    </div>
                    <a href="parts_profile/form_parts_profile.php" class="card-link"><span>ຈັດການສິນຄ້າອາໄຫຼ່ໝົດ</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div> 

        <h5 class="section-title"><i class="fas fa-tools"></i> ສະຖານະການສ້ອມແປງລົດ / ການຂາຍ</h5>
        <div class="row mb-2">
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-success-tot">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ສຳເລັດແລ້ວທັງໝົດ</div>
                            <div class="card-value"><?= number_format($success_total); ?><span class="currency">ບິນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-check-circle"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php" class="card-link"><span>ປະຫວັດທັງໝົດ</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-success-td">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ສຳເລັດມື້ນີ້</div>
                            <div class="card-value"><?= number_format($success_today); ?><span class="currency">ບິນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-calendar-check"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php?filter=today" class="card-link"><span>ບິນສຳເລັດມື້ນີ້</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-pending-tot">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ກຳລັງດຳເນີນການລວມ</div>
                            <div class="card-value"><?= number_format($pending_total); ?><span class="currency">ບິນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-spinner fa-spin"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php" class="card-link"><span>ເບິ່ງລາຍການຄ້າງ</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-pending-td">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ເປີດບິນໃໝ່ມື້ນີ້</div>
                            <div class="card-value"><?= number_format($pending_today); ?><span class="currency">ບິນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-file-invoice"></i></div>
                    </div>
                    <a href="service_logs/form_service_logs.php" class="card-link"><span>ກວດບິນໃໝ່</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <h5 class="section-title"><i class="fas fa-users"></i> ຂໍ້ມູນບຸກຄະລາກອນ</h5>
        <div class="row mb-5">
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-users-tot">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ພະນັກງານລວມທັງໝົດ</div>
                            <div class="card-value"><?= number_format($total_users); ?><span class="currency">ຄົນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-users"></i></div>
                    </div>
                    <a href="users/from_users.php" class="card-link"><span>ຈັດການພະນັກງານ</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-admin">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ຜູ້ບໍລິຫານ (Admin)</div>
                            <div class="card-value"><?= number_format($total_admins); ?><span class="currency">ຄົນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-user-shield"></i></div>
                    </div>
                    <a href="users/from_users.php?status=ແອັດມິນ" class="card-link"><span>ເບິ່ງຜູ້ບໍລິຫານ</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-staff">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ພະນັກງານທົ່ວໄປ</div>
                            <div class="card-value"><?= number_format($total_staff); ?><span class="currency">ຄົນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-user-tie"></i></div>
                    </div>
                    <a href="users/from_users.php?status=ພະນັກງານ" class="card-link"><span>ເບິ່ງພະນັກງານທົ່ວໄປ</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="dash-card card-mechanic">
                    <div class="card-body">
                        <div class="card-info-side">
                            <div class="card-title">ຊ່າງແປງລົດ</div>
                            <div class="card-value"><?= number_format($total_mechanics); ?><span class="currency">ຄົນ</span></div>
                        </div>
                        <div class="card-icon-box"><i class="fas fa-tools"></i></div>
                    </div>
                    <a href="users/from_users.php?status=ຊ່າງ" class="card-link"><span>ເບິ່ງຊ່າງແປງລົດ</span> <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

    </div> 

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // 1. ກຣາຟຍອດຂາຍ 7 ມື້ຫຼ້າສຸດ
        const salesCtx = document.getElementById('salesLineChart').getContext('2d');
        
        // ສ້າງ Gradient ໃຫ້ເສັ້ນກຣາຟເບິ່ງງາມຂຶ້ນ
        let gradient = salesCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // ສີຟ້າອ່ອນໆ
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_dates); ?>,
                datasets: [{
                    label: 'ຍອດຂາຍ (ກີບ)',
                    data: <?= json_encode($chart_sales); ?>,
                    borderColor: '#3b82f6', // ສີຟ້າ
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // ເຮັດໃຫ້ເສັ້ນໂຄ້ງມົນ
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw || 0;
                                return ' ຍອດຂາຍ: ' + value.toLocaleString() + ' ກີບ';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: {
                            callback: function(value) {
                                if(value >= 1000000) return (value / 1000000) + 'M';
                                if(value >= 1000) return (value / 1000) + 'k';
                                return value;
                            }
                        }
                    }
                }
            }
        });

        // 2. ກຣາຟວົງມົນ ອາໄຫຼ່ທີ່ຂາຍດີ
        <?php if(!empty($top_parts_names)): ?>
        const partsCtx = document.getElementById('topPartsChart').getContext('2d');
        new Chart(partsCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($top_parts_names); ?>,
                datasets: [{
                    data: <?= json_encode($top_parts_qty); ?>,
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
                    ],
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '70%',
    plugins: {
        legend: {
            position: 'bottom',
            labels: { 
                padding: 15, 
                usePointStyle: true, 
                pointStyle: 'circle',
                // ເພີ່ມການຕັ້ງຄ່າຟອນຢູ່ບ່ອນນີ້:
                font: {
                    family: "'Noto Sans Lao', sans-serif", // ໃສ່ຊື່ຟອນທີ່ທ່ານຕ້ອງການ
                    size: 14,                               // ຂະໜາດຟອນ
                    weight: 'bold'                          // ຄວາມໜາຂອງຟອນ
                }
            }
        },
        tooltip: {
            // ປັບຟອນຂອງ Tooltip
            bodyFont: {
                family: "'Noto Sans Lao', sans-serif", // ປ່ຽນຟອນທີ່ນີ້
                size: 14,
                weight: 'normal'
            },
            titleFont: {
                family: "'Noto Sans Lao', sans-serif",
                size: 16,
                weight: 'bold'
            },
            callbacks: {
                label: function(context) {
                    return ' ຂາຍໄດ້: ' + context.raw + ' ຊິ້ນ';
                }
            }
        }
    }
}
        });
        <?php endif; ?>
    </script>
</body>
</html>
<?php
}
?>