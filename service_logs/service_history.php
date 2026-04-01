<?php 
include("../cennect_dbstock.php"); 

// ຮັບຄ່າວັນທີຈາກ Form (ຖ້າບໍ່ມີໃຫ້ຕັ້ງເປັນວັນທີປັດຈຸບັນ)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); 
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');    
$search = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';

// ກຽມຂໍ້ມູນ Query ໄວ້ກ່ອນເພື່ອໄລ່ຍອດລວມໃສ່ Card ທາງເທິງ
$sql = "SELECT l.*, c.car_plate, cust.cust_name, 
        (SELECT SUM(total) FROM service_details WHERE service_id = l.log_id) as parts_sum
        FROM service_logs l
        JOIN cars c ON l.car_id = c.car_id
        JOIN customers cust ON c.cust_id = cust.cust_id
        WHERE l.status = 'completed' 
        AND l.service_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";

if ($search != '') {
    $sql .= " AND (c.car_plate LIKE '%$search%' OR cust.cust_name LIKE '%$search%')";
}
$sql .= " ORDER BY l.service_date ASC";
$res = mysqli_query($connect, $sql);

// ໄລ່ຍອດລວມກ່ອນສະແດງຜົນ
$grand_total = 0; $total_labor = 0; $total_parts = 0; $count_bill = 0;
$data = [];
while($row = mysqli_fetch_array($res)){
    $data[] = $row;
    $labor = $row['labor_cost'];
    $parts = $row['parts_sum'] ? $row['parts_sum'] : 0;
    $total_labor += $labor;
    $total_parts += $parts;
    $grand_total += ($labor + $parts);
    $count_bill++;
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ລາຍງານລາຍຮັບ</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;700&display=swap');
        :root { --primary-color: #4e73df; --success-color: #1cc88a; }
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fc; color: #333; }
        .card { border: none; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .summary-card { border-left: 4px solid; transition: transform 0.2s; }
        .summary-card:hover { transform: translateY(-5px); }
        .card-labor { border-left-color: var(--primary-color); }
        .card-parts { border-left-color: var(--success-color); }
        .card-total { border-left-color: #f6c23e; }
        .card-count { border-left-color: #36b9cc; }
        .table thead { background: #f8f9fc; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.05em; }
        .badge-plate { background: #2d3436; color: #fdcb6e; padding: 0.5em 0.8em; border-radius: 6px; font-weight: 600; }
        @media print { .btn, form, .no-print { display: none !important; } .card { box-shadow: none; border: 1px solid #ddd; } }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="fw-bold text-dark m-0">📊 ລາຍງານການເງິນ</h3>
        <div class="btn-group">
            <a href="select_service_logs.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-2">
                <i class="fas fa-home me-1"></i> ກັບໜ້າຫຼັກ
            </a>
        </div>
    </div>

    <div class="card p-4 mb-4 no-print">
    <form action="service_history.php" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">ຕັ້ງແຕ່ວັນທີ</label>
            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="<?= $start_date; ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">ເຖິງວັນທີ</label>
            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="<?= $end_date; ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">ຄົ້ນຫາ</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="ທະບຽນ, ຊື່ລູກຄ້າ..." value="<?= $search; ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" name="btn_filter" class="btn btn-primary btn-sm w-70">
                <i class="fas fa-filter me-1"></i> ຄົ້ນຫາ ຂໍ້ມູນ
            </button>
        </div>
    </form>
</div>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card summary-card card-count p-3">
                <div class="text-xs fw-bold text-info text-uppercase mb-1" style="font-size: 0.75rem;">ຈຳນວນບິນທັງໝົດ</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($count_bill); ?> ບິນ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card card-labor p-3">
                <div class="text-xs fw-bold text-primary text-uppercase mb-1" style="font-size: 0.75rem;">ລວມຄ່າແຮງງານ</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($total_labor); ?> ກີບ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card card-parts p-3">
                <div class="text-xs fw-bold text-success text-uppercase mb-1" style="font-size: 0.75rem;">ລວມຄ່າອາໄຫຼ່</div>
                <div class="h5 mb-0 fw-bold text-gray-800"><?= number_format($total_parts); ?> ກີບ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card card-total p-3">
                <div class="text-xs fw-bold text-warning text-uppercase mb-1" style="font-size: 0.75rem;">ລາຍຮັບລວມສິດທິ</div>
                <div class="h5 mb-0 fw-bold text-danger"><?= number_format($grand_total); ?> ກີບ</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">ລາຍລະອຽດການສ້ອມແປງ</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="text-muted">
                        <tr>
                            <th class="ps-4">ວັນທີ</th>
                            <th>ບິນ</th>
                            <th>ທະບຽນລົດ</th>
                            <th>ລູກຄ້າ</th>
                            <th class="text-end">ຄ່າແຮງ</th>
                            <th class="text-end">ຄ່າອາໄຫຼ່</th>
                            <th class="text-end pe-4">ລວມ (ກີບ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($data) > 0): foreach($data as $row): 
                            $b_sum = $row['labor_cost'] + ($row['parts_sum'] ? $row['parts_sum'] : 0);
                        ?>
                        <tr>
                            <td class="ps-4 small"><?= date('d/m/Y', strtotime($row['service_date'])); ?></td>
                            <td class="text-muted small">#<?= $row['log_id']; ?></td>
                            <td><span class="badge-plate small"><?= $row['car_plate']; ?></span></td>
                            <td class="fw-bold small"><?= $row['cust_name']; ?></td>
                            <td class="text-end small"><?= number_format($row['labor_cost']); ?></td>
                            <td class="text-end small"><?= number_format($row['parts_sum'] ? $row['parts_sum'] : 0); ?></td>
                            <td class="text-end pe-4 fw-bold text-primary"><?= number_format($b_sum); ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">ບໍ່ພົບຂໍ້ມູນທີ່ທ່ານຄົ້ນຫາ</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>