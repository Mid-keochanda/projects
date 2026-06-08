<?php 
session_start();
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<script>alert('ກະລຸນາລ໋ອກອິນກ່ອນ');location='index.php';</script>";
    exit();
} else {
include("../cennect_dbstock.php"); 
mysqli_set_charset($connect, "utf8");
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການສະຕັອກອາໄຫຼ່</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f1f5f9; }
        .custom-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table th { background-color: #4361ee !important; color: white !important; font-weight: 600; padding: 0.75rem; }
        .btn-action { padding: 0.35rem 0.5rem; border-radius: 8px; font-size: 0.85rem; }
        .swal2-popup { font-family: 'Noto Sans Lao', sans-serif !important; border-radius: 15px !important; }
        .form-control-custom { border-radius: 10px; padding: 0.6rem 1rem; }
    html, body {
        height: 100%;
        margin: 0;
        background-color: #f8fafc;
    }
    .custom-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        background: #ffffff;
    }

    .table thead th {
       background-color: #4361ee !important; 
        color: #f3f5f8 !important;
        font-weight: 700;
        padding: 13px 10px !important;
        white-space: nowrap; 
    }

    .table tbody td {
        padding: 10px 10px !important;
        font-size: 13px;
        color: #475569;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #fcfcfc;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    </style>
</head>
<body>

<div class="container py-2" style="max-width: 7000px;">
    
    <div class="row mb-4 align-items-center g-3">
        <div class="col-md-5">
            <h3 class="fw-bold text-dark m-0"><i class="fas fa-boxes text-primary me-2"></i>ຄັງສະຕັອກອາໄຫຼ່</h3>
            <p class="text-muted small mb-0">ບໍລິຫານຈັດການຂໍ້ມູນອາໄຫຼ່ ແລະ ຈຳນວນສິນຄ້າຄົງເຫຼືອ</p>
        </div>
        <div class="col-md-7 text-end d-flex gap-2 justify-content-md-end justify-content-between">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-white border-0 shadow-sm" style="border-radius: 10px 0 0 10px;"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="searchPart" class="form-control border-0 shadow-sm" placeholder="ຄົ້ນຫາ: ລະຫັດ, ຊື່, ປະເພດອາໄຫຼ່..." style="border-radius: 0 10px 10px 0;">
            </div>
            
            <button class="btn btn-primary fw-bold shadow-sm" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#addPartModal">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມອາໄຫຼ່ໃໝ່
            </button>
        </div>
    </div>

    <div class="card custom-card p-2">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="partsTable">
                <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>ລະຫັດອາໄຫຼ່</th>
                        <th>ຮູບ</th>
                        <th>ຊື່ອາໄຫຼ່</th>
                        <th>ປະເພດ</th>
                        <th>ລາຄາທຶນ</th>
                        <th>ລາຄາຂາຍ</th>
                        <th>ຈຳນວນ</th>
                        <th>ສະຖານະ</th>
                        <th>ວັນທີບັນທຶກ</th> 
                        <th>ອັບເດດລ່າສຸດ</th> 
                        <th>ຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $sql = "SELECT p.*, c.category_name 
                            FROM parts_profile p
                            LEFT JOIN part_categories c ON p.category_id = c.category_id
                            ORDER BY p.part_id DESC";
                    $result = mysqli_query($connect, $sql);
                    
                    while($row = mysqli_fetch_array($result)) {
                        $stock_badge = "<span class='badge bg-success'>ມີສິນຄ້າ</span>";
                        if($row['qty_stock'] <= 0) {
                            $stock_badge = "<span class='badge bg-danger'>ໝົດ</span>";
                        } elseif($row['qty_stock'] <= 5) {
                            $stock_badge = "<span class='badge bg-warning text-dark'>ໃກ້ໝົດ</span>";
                        }

                        // ກວດສອບເວລາອັບເດດລ່າສຸດ
                        $updated_display = "-";
                        if (!empty($row['updated_at']) && $row['updated_at'] != $row['created_at'] && $row['updated_at'] != '0000-00-00 00:00:00') {
                            $updated_display = date('d/m/Y H:i', strtotime($row['updated_at']));
                        }

                        // ຟໍແມັດວັນທີບັນທຶກ
                        $created_display = !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-';
                    ?>
                    <tr class="text-center search-row">
                        <td><?= $i++ ?></td>
                        <td><span class="badge bg-secondary part-code"><?= $row['part_code'] ?></span></td>
                        <td>
    <?php if(!empty($row['part_image'])): ?>
        <img src="uploads/<?= $row['part_image'] ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
    <?php else: ?>
        <div class="bg-light text-muted d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 5px;">
            <i class="fas fa-image"></i>
        </div>
    <?php endif; ?>
</td>
                        <td class="text-start fw-bold text-dark part-name"><?= $row['part_name'] ?></td>
                        <td class="part-category"><?= $row['category_name'] ? $row['category_name'] : '<span class="text-muted small">ບໍ່ມີປະເພດ</span>' ?></td>
                        <td class="text-end text-muted"><?= number_format($row['cost_price']) ?> ກີບ</td>
                        <td class="text-end fw-bold text-primary"><?= number_format($row['sale_price']) ?> ກີບ</td>
                        <td class="fw-bold fs-5"><?= number_format($row['qty_stock']) ?></td>
                        <td><?= $stock_badge ?></td>
                        <td class="small text-muted"><?= $created_display ?></td> 
                        <td class="small text-primary fw-medium"><?= $updated_display ?></td> 
                        <td>
                            <button class="btn btn-warning text-dark btn-action fw-bold me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editPartModal"
                                    data-id="<?= $row['part_id'] ?>"
                                    data-code="<?= $row['part_code'] ?>"
                                    data-name="<?= $row['part_name'] ?>"
                                    data-cat="<?= $row['category_id'] ?>"
                                    data-cost="<?= $row['cost_price'] ?>"
                                    data-sale="<?= $row['sale_price'] ?>"
                                    data-stock="<?= $row['qty_stock'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-action fw-bold" 
                                    onclick="confirmDelete(<?= $row['part_id'] ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addPartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold"><i class="fas fa-box-open text-primary me-2"></i>ເພີ່ມຂໍ້ມູນອາໄຫຼ່ໃໝ່</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="insert_parts_profile.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4 pt-0">
                
    <div class="mb-3">
        <label class="form-label small fw-bold">ຮູບພາບອາໄຫຼ່</label>
        <input type="file" name="part_image" class="form-control form-control-custom" accept="image/*">
    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">ລະຫັດອາໄຫຼ່ / ບາໂຄດ</label>
                        <input type="text" name="part_code" class="form-control form-control-custom" placeholder="ເຊັ່ນ: P-001 ຫຼື ຍິງບາໂຄດ" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ຊື່ອາໄຫຼ່</label>
                        <input type="text" name="part_name" class="form-control form-control-custom" placeholder="ພິມຊື່ອາໄຫຼ່..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ປະເພດອາໄຫຼ່</label>
                        <select name="category_id" class="form-select form-control-custom" required>
                            <option value="">-- ເລືອກປະເພດ --</option>
                            <?php 
                            $cats = mysqli_query($connect, "SELECT * FROM part_categories ORDER BY category_name ASC");
                            while($c = mysqli_fetch_array($cats)) echo "<option value='{$c['category_id']}'>{$c['category_name']}</option>";
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">ລາຄາທຶນ (ກີບ)</label>
                            <input type="text" name="cost_price" class="form-control price-input" value="0" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">ລາຂາຍ (ກີບ)</label>
                            <input type="text" name="sale_price" class="form-control price-input" value="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">ບັນທືກ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editPartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark"><i class="fas fa-edit text-warning me-2"></i>ແກ້ໄຂຂໍ້ມູນອາໄຫຼ່</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
           <form action="update_parts_profile.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4 pt-0">
                    <input type="hidden" name="part_id" id="edit_part_id">
                    
                    <div class="mb-3">
    <label class="form-label small fw-bold">ປ່ຽນຮູບພາບ (ຖ້າມີ)</label>
    <input type="file" name="part_image" class="form-control form-control-custom" accept="image/*">
</div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ລະຫັດອາໄຫຼ່ / ບາໂຄດ</label>
                        <input type="text" name="part_code" id="edit_part_code" class="form-control form-control-custom" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ຊື່ອາໄຫຼ່</label>
                        <input type="text" name="part_name" id="edit_part_name" class="form-control form-control-custom" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ປະເພດອາໄຫຼ່</label>
                        <select name="category_id" id="edit_category_id" class="form-select form-control-custom" required>
                            <?php 
                            $cats2 = mysqli_query($connect, "SELECT * FROM part_categories ORDER BY category_name ASC");
                            while($c2 = mysqli_fetch_array($cats2)) echo "<option value='{$c2['category_id']}'>{$c2['category_name']}</option>";
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">ລາຄາທຶນ (ກີບ)</label>
                            <input type="text" name="sale_price" id="edit_sale_price" class="form-control form-control-custom price-input" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">ລາຂາຍ (ກີບ)</label>
                            <input type="text" name="cost_price" id="edit_cost_price" class="form-control form-control-custom price-input" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-success px-4" style="border-radius: 10px;">ບັນທືກ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // 🌟 ລະບົບຄົ້ນຫາອາໄຫຼ່ Real-time ຝັ່ງ Client (ຄົ້ນຫາໄດ້ທັງ ລະຫັດ, ຊື່, ແລະ ໝວດໝູ່)
    $("#searchPart").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#partsTable tbody tr.search-row").filter(function() {
            var code = $(this).find('.part-code').text().toLowerCase();
            var name = $(this).find('.part-name').text().toLowerCase();
            var cat = $(this).find('.part-category').text().toLowerCase();
            
            // ເຊັກວ່າຄຳຄົ້ນຫາໄປຕົງກັບຖັນໃດຖັນໜຶ່ງຫຼືບໍ່
            $(this).toggle(code.indexOf(value) > -1 || name.indexOf(value) > -1 || cat.indexOf(value) > -1);
        });
    });
});

// ສ່ວນດຶງຂໍ້ມູນຈາກຕາຕະລາງໄປຍັດໃສ່ຟອມແກ້ໄຂ Modal
const editPartModal = document.getElementById('editPartModal');
if (editPartModal) {
editPartModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('edit_part_id').value = button.getAttribute('data-id');
    document.getElementById('edit_part_code').value = button.getAttribute('data-code');
    document.getElementById('edit_part_name').value = button.getAttribute('data-name');
    document.getElementById('edit_category_id').value = button.getAttribute('data-cat');
    document.getElementById('edit_cost_price').value = button.getAttribute('data-cost');
    document.getElementById('edit_sale_price').value = button.getAttribute('data-sale');
});
}
$(document).on('keyup', '.price-input', function(e) {
    let value = $(this).val().replace(/,/g, '');
    if (!isNaN(value) && value.length > 0) {
        $(this).val(parseFloat(value).toLocaleString('en-US'));
    }
});

// ຟັງຊັນດຶງຂໍ້ມູນເຂົ້າ Modal ແລ້ວໃສ່ຈຸດໃຫ້ອັດຕະໂນມັດ
editPartModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('edit_part_id').value = button.getAttribute('data-id');
    document.getElementById('edit_part_code').value = button.getAttribute('data-code');
    document.getElementById('edit_part_name').value = button.getAttribute('data-name');
    document.getElementById('edit_category_id').value = button.getAttribute('data-cat');
    
    // ໃສ່ຈຸດໃຫ້ຂໍ້ມູນທີ່ດຶງມາຈາກ Database
    let cost = button.getAttribute('data-cost');
    let sale = button.getAttribute('data-sale');
    document.getElementById('edit_cost_price').value = parseInt(cost).toLocaleString('en-US');
    document.getElementById('edit_sale_price').value = parseInt(sale).toLocaleString('en-US');
});
// ຟັງຊັນການລຶບແບບ SweetAlert2 ຖາມເພື່ອຄວາມແນ່ໃຈກ່ອນດີດໄປໄຟລ໌ລຶບ
function confirmDelete(id) {
    Swal.fire({
        title: 'ເຈົ້າແນ່ໃຈບໍ່?',
        text: "ຕ້ອງການລຶບອາໄຫຼ່ລາຍການນີ້ອອກຈາກສະຕັອກແທ້ຫຼືບໍ່?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef233c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ແມ່ນແລ້ວ, ລຶບເລີຍ!',
        cancelButtonText: 'ຍົກເລີກ'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete_parts_profile.php?id=' + id;
        }
    });
}
</script>
</body>
</html>
<?php
}
?>