<?php 
include("../cennect_dbstock.php"); 
mysqli_set_charset($connect, "utf8");
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການປະເພດອາໄຫຼ່</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f1f5f9; }
        .custom-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table th { background-color: #4361ee !important; color: white !important; font-weight: 600; }
        .btn-action { padding: 0.35rem 0.5rem; border-radius: 8px; font-size: 0.85rem; }
        /* ປັບ Font ໃຫ້ SweetAlert2 ເຂົ້າກັບພາສາລາວ */
        .swal2-popup { font-family: 'Noto Sans Lao', sans-serif !important; border-radius: 15px !important; }
    </style>
</head>
<body>

<div class="container py-5" style="max-width: 900px;">
    <div class="row mb-4 align-items-center">
        <div class="col-6">
            <h3 class="fw-bold text-dark"><i class="fas fa-tags text-primary me-2"></i>ປະເພດອາໄຫຼ່</h3>
            <p class="text-muted small mb-0">ໝວດໝູ່ສິນຄ້າ ແລະ ອາໄຫຼ່ທັງໝົດໃນຮ້ານ</p>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-primary fw-bold shadow-sm" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus-circle me-1"></i> ເພີ່ມປະເພດໃໝ່
            </button>
        </div>
    </div>

  <div class="card custom-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="text-center">
                <tr>
                    <th width="80">#</th>
                    <th class="text-start">ຊື່ປະເພດອາໄຫຼ່</th>
                    <th width="180">ວັນທີບັນທຶກ</th>
                    <th width="180">ອັບເດດລ່າສຸດ</th> <th width="150">ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                $sql = "SELECT * FROM part_categories ORDER BY category_id DESC";
                $result = mysqli_query($connect, $sql);
                
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_array($result)) {
                        
                        // 🌟 ເຊັກເບິ່ງວ່າເຄີຍມີການອັບເດດຫຼືບໍ່ (ຖ້າ updated_at ບໍ່ທັນປ່ຽນ ຫຼື ເປັນ NULL ໃຫ້ສະແດງ -)
                        $updated_display = "-";
                        if (!empty($row['updated_at']) && $row['updated_at'] != $row['created_at']) {
                            $updated_display = date('d/m/Y H:i', strtotime($row['updated_at']));
                        }
                ?>
                <tr class="text-center">
                    <td><?= $i++ ?></td>
                    <td class="text-start fw-bold text-dark px-4"><?= $row['category_name'] ?></td>
                    <td class="text-center small text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                    <td class="text-center small text-primary fw-medium"><?= $updated_display ?></td> <td>
                        <button class="btn btn-warning text-dark btn-action fw-bold me-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editCategoryModal"
                                data-id="<?= $row['category_id'] ?>"
                                data-name="<?= $row['category_name'] ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-action fw-bold" 
                                onclick="confirmDelete(<?= $row['category_id'] ?>)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center text-muted py-4'>--- ບໍ່ມີຂໍ້ມູນປະເພດອາໄຫຼ່ໃນລະບົບ ---</td></tr>"; // 🌟 ປ່ຽນ colspan ເປັນ 5
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark"><i class="fas fa-tag text-primary me-2"></i>ເພີ່ມປະເພດອາໄຫຼ່ໃໝ່</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="insert_categories.php" method="POST">
                <div class="modal-body px-4 pb-2">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">ຊື່ປະເພດອາໄຫຼ່ / ໝວດໝູ່</label>
                        <input type="text" name="category_name" class="form-control" style="border-radius: 10px; padding: 0.6rem 1rem;" placeholder="ເຊັ່ນ: ລະບົບໄຟ, ນ້ຳມັນເຄື່ອງ" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" style="border-radius: 10px;">ບັນທຶກຂໍ້ມູນ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark"><i class="fas fa-edit text-warning me-2"></i>ແກ້ໄຂປະເພດອາໄຫຼ່</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="update_categories.php" method="POST">
                <div class="modal-body px-4 pb-2">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">ຊື່ປະເພດອາໄຫຼ່ / ໝວດໝູ່</label>
                        <input type="text" name="category_name" id="edit_category_name" class="form-control" style="border-radius: 10px; padding: 0.6rem 1rem;" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm" style="border-radius: 10px;">ບັນທຶກການແກ້ໄຂ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// 1. ສ່ວນດຶງຂໍ້ມູນເກົ່າມາວາງໃສ່ຟອມແກ້ໄຂ Modal 
const editModal = document.getElementById('editCategoryModal');
if (editModal) {
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        document.getElementById('edit_category_id').value = id;
        document.getElementById('edit_category_name').value = name;
    });
}

// 2. 🌟 ຟັງຊັນການລຶບແບບ SweetAlert2 ຖາມກ່ອນລຶບແທ້
function confirmDelete(id) {
    Swal.fire({
        title: 'ເຈົ້າແນ່ໃຈບໍ່?',
        text: "ຕ້ອງການລຶບປະເພດອາໄຫຼ່ນີ້ອອກຈາກລະບົບແທ້ຫຼືບໍ່?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef233c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ແມ່ນແລ້ວ, ລຶບເລີຍ!',
        cancelButtonText: 'ຍົກເລີກ'
    }).then((result) => {
        if (result.isConfirmed) {
            // ຖ້າກົດຢືນຢັນ ໃຫ້ລິ້ງໄປຫາໄຟລ໌ delete_category.php ພ້ອມສົ່ງ ID ໄປ
            window.location.href = 'delete_categories.php?id=' + id;
        }
    });
}
</script>
</body>
</html>