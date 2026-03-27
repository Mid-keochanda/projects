<?php 
include("../cennect_dbstock.php"); 
date_default_timezone_set('Asia/Vientiane');
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8fafc; }
        .search-box { max-width: 450px; position: relative; }
        .search-box i { position: absolute; left: 15px; top: 13px; color: #94a3b8; }
        .search-box input { padding-left: 45px; border-radius: 12px; border: 1px solid #e2e8f0; height: 45px; }
        .main-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .table thead th { background: #f1f5f9; color: #64748b; font-size: 1rem; text-transform: uppercase; padding: 10px; border: none; }
        .stock-tag { padding: 10px 10px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; }
        .stock-low { background: #fee2e2; color: #ef4444; }
        .stock-ok { background: #dcfce7; color: #10b981; }
    </style>
</head>
<body>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-3 align-items-center mb-4">
            <div class="col-lg-4">
                <h3 class="fw-bold m-0"><i class="fas fa-boxes text-primary me-2"></i>ຄັງອາໄຫຼ່</h3>
            </div>
            <div class="col-lg-5">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="myInput" onkeyup="searchTable()" class="form-control" placeholder="ຄົ້ນຫາຊື່ອາໄຫຼ່ ຫຼື ໝວດໝູ່...">
                </div>
            </div>
            <div class="col-lg-3 text-lg-end">
                <a href="form_parts.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> ເພີ່ມອາໄຫຼ່ໃໝ່
                </a>
            </div>
        </div>

        <div class="card main-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="partsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ຊື່ອາໄຫຼ່</th>
                            <th>ໝວດໝູ່</th>
                            <th>ຈຳນວນ</th>
                            <th>ຫົວໜ່ວຍ</th>
                            <th>ລາຄາຊື້</th>
                            <th>ລາຄາຂາຍ</th>
                            <th>ໝາຍເຫດ</th> <th>ເວລາບັນທຶກ</th>
                            <th>ເວລາອັບເດດ</th>
                            <th>ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM parts ORDER BY created_at DESC";
                        $query = mysqli_query($connect, $sql);
                        while($row = mysqli_fetch_array($query)) {
                            $is_low = ($row['qty_stock'] <= $row['min_stock']);
                        ?>
                        <tr>
                            <td class="text-muted small">#<?= $row['part_id'] ?></td>
                            <td class="fw-bold"><?= $row['part_name'] ?></td>
                            <td><span class="badge bg-light text-primary border rounded-pill px-3"><?= $row['category'] ?: 'ທົ່ວໄປ' ?></span></td>
                            <td>
                                <span class="stock-tag <?= $is_low ? 'stock-low' : 'stock-ok' ?>">
                                    <?= number_format($row['qty_stock']) ?>
                                </span>
                            </td>
                            <td><?= $row['unit'] ?></td>
                            <td class="text-muted"><?= number_format($row['cost_price']) ?> ກີບ</td>
                            <td class="text-primary fw-bold"><?= number_format($row['sale_price']) ?> ກີບ</td>
                            
                            <td class="small text-muted">
                                <?= $row['remark'] ?: '-' ?>
                            </td>

                            <td class="small">
                                <span class="d-block fw-bold"><?= date('d/m/Y', strtotime($row['created_at'])) ?></span>
                                <span class="text-muted" style="font-size: 0.7rem;"><?= date('H:i', strtotime($row['created_at'])) ?></span>
                            </td>

                            <td class="small">
                                <?php if($row['updated_at'] && $row['updated_at'] != '0000-00-00 00:00:00'): ?>
                                    <span class="d-block text-warning fw-bold"><?= date('d/m/Y', strtotime($row['updated_at'])) ?></span>
                                    <span class="text-muted" style="font-size: 0.7rem;"><?= date('H:i', strtotime($row['updated_at'])) ?></span>
                                <?php else: ?>
                                    <span class="text-muted italic small">ຍັງບໍ່ມີການແກ້ໄຂ</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="btn-group shadow-sm rounded-3">
                                    <a href="update_parts.php?part_id=<?= $row['part_id'] ?>" class="btn btn-sm btn-white text-warning border-end"><i class="fas fa-edit"></i></a>
                                    <button onclick="delPart(<?= $row['part_id'] ?>)" class="btn btn-sm btn-white text-danger"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function searchTable() {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("partsTable");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            tr[i].style.display = "none";
            td = tr[i].getElementsByTagName("td");
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                        break;
                    }
                }
            }
        }
    }

    function delPart(id) {
        Swal.fire({
            title: 'ຢືນຢັນການລົບ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ລົບເລີຍ',
            cancelButtonText: 'ຍົກເລີກ'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = 'delete_parts.php?part_id=' + id;
        });
    }
</script>
</body>
</html>