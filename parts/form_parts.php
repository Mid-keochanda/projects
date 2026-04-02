<?php 
include("../cennect_dbstock.php"); 
date_default_timezone_set('Asia/Vientiane');
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management - ຄັງອາໄຫຼ່</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8fafc; }
        .full-width-container { width: 100%; margin: 0 auto; }
        .main-card { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: #fff; }
        .form-control-custom { border-radius: 10px; padding: 10px 15px; border: 1px solid #e2e8f0; }
        .stock-tag { padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; }
        .stock-low { background: #fee2e2; color: #ef4444; }
        .stock-ok { background: #dcfce7; color: #10b981; }
        .table thead th { 
            background-color: #f1f5f9; 
            white-space: nowrap; 
            padding: 1px 1px; 
            font-size: 0.9rem;
            color: #475569;
        }
    </style>
</head>
<body>

<div class="container-fluid py-2">
    <div class="full-width-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fas fa-boxes text-primary me-2"></i>ລາຍການອາໄຫຼ່ທັງໝົດ</h3>
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow" onclick="openAddModal()">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມອາໄຫຼ່ໃໝ່
            </button>
        </div>

        <div class="card main-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="partsTable">
                    <thead class="text-center">
                        <tr>
                            <th width="4%">ID</th>
                            <th >ຊື່ອາໄຫຼ່</th>
                            <th >ໝວດໝູ່</th>
                            <th >ຈຳນວນ</th>
                            <th >ຫົວໜ່ວຍ</th>
                            <th >ລາຄາຊື້</th>
                            <th >ລາຄາຂາຍ</th>
                            <th >ໝາຍເຫດ</th>
                            <th >ວັນທີບັນທຶກ</th>
                            <th >ວັນທີອັບເດດ</th>
                            <th >ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM parts ORDER BY part_id DESC";
                        $query = mysqli_query($connect, $sql);
                        while($row = mysqli_fetch_array($query)) {
                            $is_low = ($row['qty_stock'] <= $row['min_stock']);
                        ?>
                        <tr class="text-center">
                            <td class="text-muted small">#<?= $row['part_id'] ?></td>
                            <td class="text-start fw-bold text-dark"><?= $row['part_name'] ?></td>
                            <td>
                                <span class="badge bg-light text-primary border rounded-pill px-3">
                                    <?= $row['category'] ?: 'ທົ່ວໄປ' ?>
                                </span>
                            </td>
                            <td>
                                <span class="stock-tag <?= $is_low ? 'stock-low' : 'stock-ok' ?>">
                                    <?= number_format($row['qty_stock']) ?>
                                </span>
                            </td>
                            <td class="text-muted"><?= $row['unit'] ?: '-' ?></td>
                            <td class="text-secondary"><?= number_format($row['cost_price']) ?> ກີບ</td>
                            <td class="text-primary"><?= number_format($row['sale_price']) ?> ກີບ</td>
                            
                            <td class="text-start small text-muted">
                                <?= !empty($row['remark']) ? $row['remark'] : '<span class="opacity-50">-</span>' ?>
                            </td>
                            
                            <td class="small">
                                <span class="d-block text-dark"><?= date('d/m/Y', strtotime($row['created_at'])) ?></span>
                                <span class="text-muted" style="font-size: 0.7rem;"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($row['created_at'])) ?></span>
                            </td>

                            <td class="small">
                                <?php if(!empty($row['updated_at']) && $row['updated_at'] != '0000-00-00 00:00:00'): ?>
                                    <span class="d-block text-warning"><?= date('d/m/Y', strtotime($row['updated_at'])) ?></span>
                                    <span class="text-muted" style="font-size: 0.7rem;"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($row['updated_at'])) ?></span>
                                <?php else: ?>
                                    <span class="text-muted italic small opacity-50">ຍັງບໍ່ມີການແກ້ໄຂ</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                    <button onclick='openEditModal(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                                            class="btn btn-sm btn-white text-warning px-3 border-end">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="delPart(<?= $row['part_id'] ?>)" 
                                            class="btn btn-sm btn-white text-danger px-3">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
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

<div class="modal fade" id="partModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold" id="modalTitle">ຈັດການຂໍ້ມູນອາໄຫຼ່</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="partForm" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="part_id" id="part_id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">ຊື່ອາໄຫຼ່</label>
                            <input type="text" name="part_name" id="part_name" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">ໝວດໝູ່</label>
                            <input type="text" name="category" id="category" class="form-control form-control-custom">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">ຈຳນວນໃນສາງ</label>
                            <input type="number" name="qty_stock" id="qty_stock" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">ຫົວໜ່ວຍ</label>
                            <input type="text" name="unit" id="unit" class="form-control form-control-custom">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">ແຈ້ງເຕືອນເມື່ອຕໍ່າກວ່າ</label>
                            <input type="number" name="min_stock" id="min_stock" class="form-control form-control-custom">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ລາຄາຊື້ (ຕົ້ນທຶນ)</label>
                            <input type="number" name="cost_price" id="cost_price" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ລາຄາຂາຍ</label>
                            <input type="number" name="sale_price" id="sale_price" class="form-control form-control-custom" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">ໝາຍເຫດ</label>
                            <textarea name="remark" id="remark" class="form-control form-control-custom" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 text-end">
                    <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">ບັນທຶກຂໍ້ມູນ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const partModal = new bootstrap.Modal(document.getElementById('partModal'));

    function openAddModal() {
        document.getElementById('partForm').reset();
        document.getElementById('part_id').value = ''; 
        document.getElementById('modalTitle').innerText = 'ເພີ່ມອາໄຫຼ່ໃໝ່';
        document.getElementById('partForm').action = 'insert_parts.php';
        partModal.show();
    }

    function openEditModal(data) {
        document.getElementById('modalTitle').innerText = 'ແກ້ໄຂຂໍ້ມູນອາໄຫຼ່';
        document.getElementById('partForm').action = 'save_parts.php';
        
        document.getElementById('part_id').value = data.part_id;
        document.getElementById('part_name').value = data.part_name;
        document.getElementById('category').value = data.category;
        document.getElementById('qty_stock').value = data.qty_stock;
        document.getElementById('unit').value = data.unit;
        document.getElementById('min_stock').value = data.min_stock;
        document.getElementById('cost_price').value = data.cost_price;
        document.getElementById('sale_price').value = data.sale_price;
        
        // ສ່ວນສຳຄັນ: ດຶງຂໍ້ມູນ remark ມາໃສ່ textarea
        document.getElementById('remark').value = data.remark || '';
        
        partModal.show();
    }

    function delPart(id) {
        Swal.fire({
            title: 'ຢືນຢັນການລຶບ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'ລຶບທັນທີ',
            cancelButtonText: 'ຍົກເລີກ'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = 'delete_parts.php?part_id=' + id;
        });
    }

    $(document).ready(function() {
        const status = new URLSearchParams(window.location.search).get('status');
        if (status === 'success') {
            Swal.fire({ icon: 'success', title: 'ສຳເລັດ!', showConfirmButton: false, timer: 1500 });
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>
</body>
</html>