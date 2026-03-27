<?php include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລະບົບຈັດການຂໍ້ມູນລົດ - Full Version</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-body: #f1f5f9;
            --accent: #4361ee;
            --success: #10b981;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Noto Sans Lao', sans-serif; 
            color: #334155;
        }

        .custom-card { border: none; border-radius: 1.25rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); background: #fff; }
        
        .table thead th { 
            background-color: #f8fafc; 
            color: #64748b; 
            font-size: 1rem; 
            font-weight: 700;
            padding: 1rem;
            border-bottom: 2px solid #f1f5f9;
            text-transform: uppercase;
        }
        .table tbody td { vertical-align: middle; padding: 0.5rem; border-bottom: 1px solid #f1f5f9; }

        .plate-badge { 
            background: #fff; border: 2px solid #cbd5e1; color: #1e293b; 
            font-weight: 800; padding: 4px 12px; border-radius: 8px; 
            display: inline-block; min-width: 95px; font-size: 0.85rem;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
        }

        .avatar-circle {
            width: 32px; height: 32px; border-radius: 8px;
            background-color: #e0e7ff; color: var(--accent);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; margin-right: 10px; font-size: 0.8rem;
        }

        .time-group { line-height: 1.2; display: flex; flex-direction: column; align-items: center; }
        .date-text { font-weight: 700; font-size: 0.8rem; color: #1e293b; }
        .time-text { font-size: 0.7rem; color: #94a3b8; }

        .remark-pill {
            max-width: 150px; font-size: 0.75rem; color: #64748b;
            background: #f8fafc; padding: 4px 10px; border-radius: 6px;
            display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .btn-action { 
            width: 34px; height: 34px; border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center; 
            transition: all 0.2s; border: none;
        }
        .btn-edit { background-color: #ecfdf5; color: #059669; }
        .btn-edit:hover { background-color: #059669; color: #fff; transform: translateY(-2px); }
        .btn-delete { background-color: #fff1f2; color: #e11d48; }
        .btn-delete:hover { background-color: #e11d48; color: #fff; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="container-fluid px-4 py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-5">
            <h3 class="fw-bold m-0 text-dark"><i class="fas fa-car-side text-primary me-2"></i>ລາຍຊື່ລົດທັງໝົດ</h3>
            <p class="text-muted small mb-0">ຈັດການຂໍ້ມູນຍານພາຫະນະ ແລະ ປະຫວັດການອັບເດດ</p>
        </div>
        <div class="col-md-7 text-end d-flex gap-2 justify-content-end mt-3 mt-md-0">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-white border-0 shadow-sm" style="border-radius: 12px 0 0 12px;"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="searchCar" class="form-control border-0 shadow-sm" placeholder="ຄົ້ນຫາທະບຽນ ຫຼື ເຈົ້າຂອງ..." style="border-radius: 0 12px 12px 0;">
            </div>
            <a href="form_cars.php" class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius: 12px;">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມລົດໃໝ່
            </a>
        </div>
    </div>

    <div class="card custom-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="carTable">
                <thead class="text-center">
                    <tr>
                        <th width="40">#</th>
                        <th>ເລກທະບຽນ</th>
                        <th>ຍີ່ຫໍ້</th>
                        <th>ລຸ້ນ</th>
                        <th>ສີລົດ</th>   
                        <th class="text-start">ເຈົ້າຂອງລົດ</th>
                        <th>ເບີໂທລະສັບ</th>
                        <th class="text-start">ໝາຍເຫດ</th>
                        <th>ວັນທີບັນທຶກ</th>
                        <th>ອັບເດດລ່າສຸດ</th>
                        <th>ຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $sql = "SELECT cars.*, customers.cust_name, customers.cust_surname, customers.tel 
                            FROM cars 
                            LEFT JOIN customers ON cars.cust_id = customers.cust_id 
                            ORDER BY cars.car_id DESC";
                    $result = mysqli_query($connect, $sql);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_array($result)) {
                            $initial = mb_substr($row['cust_name'], 0, 1, 'utf-8');
                    ?>
                    <tr class="text-center">
                        <td class="text-muted small"><?= $i++ ?></td>
                        <td><div class="plate-badge shadow-sm"><?= htmlspecialchars($row['car_plate']) ?></div></td>
                        
                        <td class="fw-bold text-dark small"><?= htmlspecialchars($row['car_brand'] ?: '-') ?></td>
                        <td class="text-secondary small"><?= htmlspecialchars($row['car_model'] ?: '-') ?></td>
                        
                        <td><span class="badge bg-light text-dark border fw-medium"><?= htmlspecialchars($row['car_color'] ?: '-') ?></span></td>
                        
                        <td class="text-start">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle shadow-sm"><?= $initial ?></div>
                                <div class="fw-bold small"><?= htmlspecialchars($row['cust_name'] . ' ' . $row['cust_surname']) ?></div>
                            </div>
                        </td>
                        <td>
                            <a href="tel:<?= $row['tel'] ?>" class="text-primary text-decoration-none fw-bold small">
                                <?= htmlspecialchars($row['tel']) ?>
                            </a>
                        </td>

                        <td class="text-start">
                            <div class="remark-pill" title="<?= htmlspecialchars($row['remark']) ?>">
                                <?= htmlspecialchars($row['remark'] ?: '-') ?>
                            </div>
                        </td>

                        <td>
                            <div class="time-group">
                                <span class="date-text small"><?= date('d/m/Y', strtotime($row["created_at"])); ?></span>
                                <span class="time-text"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($row["created_at"])); ?></span>
                            </div>
                        </td>

                        <td>
                            <?php if(!empty($row["updated_at"]) && $row["updated_at"] != "0000-00-00 00:00:00"): ?>
                                <div class="time-group text-success">
                                    <span class="date-text small text-success"><?= date('d/m/Y', strtotime($row["updated_at"])); ?></span>
                                    <span class="time-text text-success opacity-75"><i class="fas fa-history me-1"></i><?= date('H:i', strtotime($row["updated_at"])); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="text-muted opacity-50 italic small">- ຍັງບໍ່ມີ -</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="update_cars.php?car_id=<?= $row['car_id']; ?>" class="btn-action btn-edit"><i class="fas fa-pen-nib small"></i></a>
                                <a href="delete_cars.php?car_id=<?= $row['car_id']; ?>" class="btn-action btn-delete delete"><i class="fas fa-trash-alt small"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='11' class='text-center py-5 text-muted'>ບໍ່ມີຂໍ້ມູນລົດໃນລະບົບ</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3 text-center border-top">
            <span class="text-muted small">ຂໍ້ມູນທັງໝົດ <strong class="text-primary"><?= ($i-1) ?></strong> ລາຍການ</span>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $("#searchCar").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#carTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $('.delete').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: 'ຢືນຢັນການລົບ?',
            text: "ທ່ານຈະບໍ່ສາມາດກູ້ຄືນຂໍ້ມູນນີ້ໄດ້!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'ລົບທັນທີ',
            cancelButtonText: 'ຍົກເລີກ'
        }).then((result) => {
            if (result.isConfirmed) { window.location.href = href; }
        });
    });
});
</script>
</body>
</html>