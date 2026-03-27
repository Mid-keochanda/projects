<?php include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລາຍຊື່ລົດທັງໝົດ - Garage Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-bg: #f4f7fa;
            --brand-color: #4361ee;
            --brand-hover: #3a53cc;
            --card-shadow: 0 8px 24px rgba(149, 157, 165, 0.1);
        }

        body { 
            background-color: var(--primary-bg); 
            font-family: 'Noto Sans Lao', sans-serif; 
            color: #333;
        }

        /* ປັບໄລຍະຫ່າງຂອງ Cell ໃຫ້ແຄບລົງ */
.table > :not(caption) > * > * { 
    padding: 0.5rem 0.4rem !important; /* ຫຼຸດ Padding ລົງ 50% */
    vertical-align: middle; 
}

/* ປັບຂະໜາດ Font ໂດຍລວມໃນຕາຕະລາງ */
.table { 
    font-size: 0.85rem; /* ຫຼຸດຂະໜາດຕົວໜັງສື */
}
        
        /* ປັບຄວາມສູງ ແລະ ໄລຍະຫ່າງຂອງ Cell */
        .table > :not(caption) > * > * { 
            padding: 0.75rem 0.75rem; 
            border-bottom-color: #f1f5f9; 
            vertical-align: middle; 
        }

        /* ປັບແຕ່ງ Header ຂອງຕາຕະລາງ */
        .thead-custom { background-color: #f8fafc; }
        .thead-custom th { 
            color: #475569; 
            font-weight: 600; 
            font-size: 1rem; 
            text-transform: uppercase; 
            border-bottom: 2px solid #edf2f7;
            white-space: nowrap;
        }

        /* ປັບຂະໜາດ Avatar ໃຫ້ພໍດີ */
        .avatar-circle {
            width: 30px; height: 30px; border-radius: 50%;
            background-color: #e0e7ff; color: var(--brand-color);
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem; margin-right: 10px;
            flex-shrink: 0;
        }

        .plate-badge { 
            background: #fff; border: 1px solid #cbd5e1; color: #1e293b; 
            font-weight: 700; padding: 4px 12px; border-radius: 6px; 
            font-size: 0.85rem; display: inline-block; min-width: 90px;
        }

        .date-info { font-size: 0.8rem; line-height: 1.4; }
        .updated-text { color: #10b981; font-size: 0.75rem; }

        .btn-action { 
            width: 34px; height: 34px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center; 
            transition: all 0.2s ease; border: none; font-size: 0.85rem;
        }
        .btn-edit { background-color: #f0fdf4; color: #16a34a; }
        .btn-delete { background-color: #fef2f2; color: #dc2626; }
        
        .empty-state { padding: 5rem 2rem; color: #94a3b8; }
    </style>

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
                text: "ຂໍ້ມູນຈະຖືກລົບຖາວອນ!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'ລົບຂໍ້ມູນ',
                cancelButtonText: 'ຍົກເລີກ'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = href; }
            });
        });
    });
    </script>
</head>
<body>

<div class="container-fluid px-4 py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-1"><i class="fas fa-car-side text-primary me-2"></i>ລາຍຊື່ລົດທັງໝົດ</h3>
            <p class="text-muted small mb-0">ລາຍການລົດທັງໝົດໃນລະບົບ</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-flex gap-2 justify-content-md-end">
                <input type="text" id="searchCar" class="form-control rounded-pill px-4" placeholder="ຄົ້ນຫາ..." style="max-width: 250px;">
                <a href="form_cars.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus me-1"></i> ເພີ່ມລົດ
                </a>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="carTable">
                <thead class="thead-custom text-center">
                     <th width="5%">ລຳດັບ</th>
                        <th width="10%">ເລກທະບຽນ</th>
                        <th width="10%">ຍີ່ຫໍ້</th>
                        <th width="10%">ລຸ້ນ</th>
                        <th width="10%">ສີ</th>
                        <th width="10%" class="text-start pl-4">ເຈົ້າຂອງລົດ</th>
                        <th width="10%">ເບີໂທລະສັບ</th>
                        <th width="10%">ວັນທີບັນທຶກ/ແກ້ໄຂ</th>
                        <th width="15%">ຈັດການ</th>
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
                        <td class="text-secondary"><?= $i++ ?></td>
                        <td><div class="plate-badge"><?= htmlspecialchars($row['car_plate']) ?></div></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['car_brand'] ?: '-') ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['car_model'] ?: '-') ?></td>
                        <td>
                            <span class="fw-bold"><?= htmlspecialchars($row['car_color'] ?: '-') ?></span>
                        </td>
                        <td class="text-start">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle"><?= $initial ?></div>
                                <div class="fw-bold small"><?= htmlspecialchars($row['cust_name'] . ' ' . $row['cust_surname']) ?></div>
                            </div>
                        </td>
                        <td>
                            <a href="tel:<?= $row['tel'] ?>" class="text-primary text-decoration-none small">
                                <i class="fas fa-phone-alt me-1" style="font-size: 0.7rem;"></i><?= htmlspecialchars($row['tel']) ?>
                            </a>
                        </td>
                        <td>
                            <div class="date-info text-muted fw-bold">
                                <div><?= date('d/m/Y', strtotime($row["created_at"])); ?></div>
                                <?php if(!empty($row["updated_at"]) && $row["updated_at"] != $row["created_at"]): ?>
                                    <div class="updated-text"><i class="fas fa-sync-alt me-1"></i><?= date('d/m/Y', strtotime($row["updated_at"])); ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="update_cars.php?car_id=<?= $row['car_id']; ?>" class="btn btn-action btn-edit"><i class="fas fa-pen"></i></a>
                                <a href="delete_cars.php?car_id=<?= $row['car_id']; ?>" class="btn btn-action btn-delete delete"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center empty-state'>ຍັງບໍ່ມີຂໍ້ມູນລົດ</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>