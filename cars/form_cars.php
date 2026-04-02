<?php include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລະບົບຈັດການຂໍ້ມູນລົດ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --bg-body: #f1f5f9; --primary: #4361ee; }
        body { background-color: var(--bg-body); font-family: 'Noto Sans Lao', sans-serif; color: #334155; }
        .custom-card { border: none; border-radius: 1.25rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); background: #fff; }
        .table thead th { background-color: #f8fafc; color: #64748b; font-weight: 700; padding: 1rem; border-bottom: 2px solid #f1f5f9; white-space: nowrap; }
        .plate-badge { background: #fff; border: 2px solid #cbd5e1; color: #1e293b; font-weight: 800; padding: 4px 12px; border-radius: 8px; display: inline-block; min-width: 95px; font-size: 0.85rem; }
        .btn-action { width: 34px; height: 34px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; border: none; }
        .btn-edit { background-color: #ecfdf5; color: #059669; }
        .btn-edit:hover { background-color: #059669; color: #fff; transform: translateY(-2px); }
        .btn-delete { background-color: #fff1f2; color: #e11d48; }
        .btn-delete:hover { background-color: #e11d48; color: #fff; transform: translateY(-2px); }
        .form-control-custom { border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.6rem 1rem; }
    </style>
</head>
<body>

<div class="container-fluid px-4 py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-5">
            <h3 class="fw-bold m-0 text-dark"><i class="fas fa-car-side text-primary me-2"></i>ລາຍຊື່ລົດທັງໝົດ</h3>
            <p class="text-muted small mb-0">ຈັດການຂໍ້ມູນຍານພາຫະນະດ້ວຍລະບົບ Modal</p>
        </div>
        <div class="col-md-7 text-end d-flex gap-2 justify-content-end">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-white border-0 shadow-sm" style="border-radius: 12px 0 0 12px;"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="searchCar" class="form-control border-0 shadow-sm" placeholder="ຄົ້ນຫາ..." style="border-radius: 0 12px 12px 0;">
            </div>
            <button class="btn btn-primary px-4 fw-bold shadow-sm" style="border-radius: 12px;" onclick="openAddModal()">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມລົດໃໝ່
            </button>
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
                        <th>ສີ</th>
                        <th class="text-start">ເຈົ້າຂອງລົດ</th>
                        <th>ເບີໂທ</th>
                        <th>ໝາຍເຫດ</th>
                        <th>ວັນທີບັນທຶກ</th>
                        <th>ວັນທີອັບເດດ</th>
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
                    while($row = mysqli_fetch_array($result)) {
                    ?>
                    <tr class="text-center align-middle">
                        <td class="text-muted small"><?= $i++ ?></td>
                        <td><div class="plate-badge shadow-sm"><?= $row['car_plate'] ?></div></td>
                        <td class="fw-bold text-dark small"><?= $row['car_brand'] ?></td>
                        <td class="text-muted fw-bold small"><?= $row['car_model'] ?></td>
                        <td><span class="badge fw-bold bg-light text-dark border small fw-normal"><?= $row['car_color'] ?></span></td>

                        <td class="text-start fw-bold small"><?= $row['cust_name'].' '.$row['cust_surname'] ?></td>
                        <td><span class="text-primary fw-bold small"><?= $row['tel'] ?></span></td>
                        <td class="fw-bold text-dark small"><?= $row['remark'] ?></td>
                        <td>
    <div class="small text-muted" style="font-size: 0.85rem;">
        <i class="far fa-calendar-plus me-1 text-success"></i>
        <?= date('d/m/Y', strtotime($row["created_at"])) ?>
        <span class="d-block text-dark fw-bold" style="font-size: 0.7rem;">
            <i class="far fa-clock me-1 text-muted"></i><?= date('H:i', strtotime($row["created_at"])) ?>
        </span>
    </div>
</td>

<td>
    <div class="small text-muted" style="font-size: 0.85rem;">
        <?php if(!empty($row['updated_at']) && $row['updated_at'] != '0000-00-00 00:00:00'): ?>
            <i class="fas fa-history me-1 text-warning"></i>
            <?= date('d/m/Y', strtotime($row["updated_at"])) ?>
            <span class="d-block text-dark fw-bold" style="font-size: 0.7rem;">
                <i class="far fa-clock me-1 text-muted"></i><?= date('H:i', strtotime($row["updated_at"])) ?>
            </span>
        <?php else: ?>
            <span class="text-muted small">-</span>
        <?php endif; ?>
    </div>
</td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn-action btn-edit" 
                                    onclick="openEditModal('<?= $row['car_id'] ?>','<?= $row['car_plate'] ?>','<?= $row['car_brand'] ?>','<?= $row['car_model'] ?>','<?= $row['car_color'] ?>','<?= $row['cust_id'] ?>','<?= $row['remark'] ?>')">
                                    <i class="fas fa-pen-nib small"></i>
                                </button>
                                <a href="delete_cars.php?car_id=<?= $row['car_id'] ?>" class="btn-action btn-delete delete-btn">
                                    <i class="fas fa-trash-alt small"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="carModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold" id="modalTitle">ຂໍ້ມູນລົດ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="carForm" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="car_id" id="car_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ເລກທະບຽນ</label>
                            <input type="text" name="car_plate" id="car_plate" class="form-control form-control-custom" required placeholder="ກກ 1234">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">ເຈົ້າຂອງລົດ</label>
                            <select name="cust_id" id="cust_id" class="form-select form-control-custom" required>
                                <option value="">-- ເລືອກເຈົ້າຂອງລົດ --</option>
                                <?php 
                                $c_sql = mysqli_query($connect, "SELECT * FROM customers ORDER BY cust_name ASC");
                                while($c = mysqli_fetch_array($c_sql)) echo "<option value='{$c['cust_id']}'>{$c['cust_name']} {$c['cust_surname']}</option>";
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label small fw-bold">ຍີ່ຫໍ້</label><input type="text" name="car_brand" id="car_brand" class="form-control form-control-custom"></div>
                        <div class="col-md-4"><label class="form-label small fw-bold">ລຸ້ນ</label><input type="text" name="car_model" id="car_model" class="form-control form-control-custom"></div>
                        <div class="col-md-4"><label class="form-label small fw-bold">ສີ</label><input type="text" name="car_color" id="car_color" class="form-control form-control-custom"></div>
                        <div class="col-12"><label class="form-label small fw-bold">ໝາຍເຫດ</label><textarea name="remark" id="remark" class="form-control form-control-custom" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius:12px;">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-primary px-4 shadow" style="border-radius:12px;">ບັນທຶກຂໍ້ມູນ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function openAddModal() {
    $('#modalTitle').html('<i class="fas fa-plus-circle text-primary me-2"></i>ເພີ່ມຂໍ້ມູນລົດໃໝ່');
    $('#carForm').attr('action', 'insert_cars.php');
    $('#car_id').val('');
    $('#carForm')[0].reset(); 
    $('#carModal').modal('show');
}

function openEditModal(id, plate, brand, model, color, cust, remark) {
    $('#modalTitle').html('<i class="fas fa-edit text-success me-2"></i>ແກ້ໄຂຂໍ້ມູນລົດ');
    $('#carForm').attr('action', 'save_cars.php');
    $('#car_id').val(id); 
    $('#car_plate').val(plate); 
    $('#car_brand').val(brand); 
    $('#car_model').val(model); 
    $('#car_color').val(color); 
    $('#cust_id').val(cust); 
    $('#remark').val(remark);
    $('#carModal').modal('show');
}

$(document).ready(function() {
    $("#searchCar").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#carTable tbody tr").filter(function() { 
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1) 
        });
    });

    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: 'ຢືນຢັນການລຶບ?',
            text: "ຂໍ້ມູນລົດຄັນນີ້ຈະຫາຍໄປຈາກລະບົບ!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'ລຶບທັນທີ',
            cancelButtonText: 'ຍົກເລີກ'
        }).then((result) => { 
            if (result.isConfirmed) window.location.href = href; 
        });
    });
});
</script>
</body>
</html>