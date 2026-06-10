<?php 
session_start();
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<script>alert('ກະລຸນາລ໋ອກອິນກ່ອນ');location='index.php';</script>";
    exit();
} else {
include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນລູກຄ້າ</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
    :root { --primary-color: #4361ee; --bg-body: #f8f9fc; }
    body { font-family: 'Noto Sans Lao', sans-serif; background-color: var(--bg-body); color: #2d3436; }
    .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
    .table { font-size: 0.9rem; }
    .table thead th { 
        background-color: #4361ee !important; 
        color: white !important; 
        font-weight: 600; 
        padding: 12px 10px !important;
        white-space: nowrap;
    }
    .table tbody td { padding: 10px !important; }
    .badge-gender { padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; }
    .btn-action { width: 32px; height: 32px; border-radius: 8px; }
    .table-responsive { overflow-x: auto; }
    .search-box { background: #f1f2f6; border-radius: 0 12px 12px 0 !important; }
    .input-group-text { background: #f1f2f6; border-radius: 12px 0 0 12px !important; }
</style>
</head>
<body>

<div class="container-fluid py-3 px-md-3">
    <div class="card">
        <div class="card-header bg-white border-0 py-2 px-2 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary me-2"></i>ຈັດການຂໍ້ມູນລູກຄ້າ</h4>
                <p class="text-muted small mb-0">ລະບົບຈັດການ ແລະ ຕິດຕາມຂໍ້ມູນລູກຄ້າທັງໝົດ</p>
            </div>

            <div class="d-flex gap-2">
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="search" class="form-control search-box" placeholder="ຄົ້ນຫາ...">
                </div>
                <button class="btn btn-primary px-4 shadow-sm" style="border-radius: 12px; white-space: nowrap;" onclick="openCustomerModal()">
                    <i class="bi bi-person-plus-fill me-2"></i> ເພີ່ມລູກຄ້າໃໝ່
                </button>
            </div>
        </div>

        <div class="card-body px-2">
           <div class="table-responsive">
    <table class="table table-hover align-middle" id="customerTable">
        <thead>
            <tr class="text-center">
                <th width="70">#</th>
                <th width="100">ລະຫັດ</th> <th class="text-start">ຊື່ ແລະ ນາມສະກຸນ</th>
                <th width="100">ເພດ</th>
                <th>ເບີໂທລະສັບ</th>
                <th class="text-start">ທີ່ຢູ່</th>
                <th>ວັນທີບັນທຶກ</th> 
                <th>ອັບເດດຫຼ້າສຸດ</th> 
                <th class="text-start">ໝາຍເຫດ</th>
                <th width="110">ຈັດການ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $sql = mysqli_query($connect, "SELECT *, 
                    DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as date_create,
                    DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as date_update 
                    FROM customers ORDER BY cust_id DESC");
            
            while($row = mysqli_fetch_array($sql)) {
                $color = ($row['gender'] == 'ຊາຍ') ? 'primary' : 'danger';
            ?>
            <tr>
                <td class="text-center fw-bold text-muted"><?= $i++; ?></td>
                <td class="text-center fw-bold text-primary"><?= $row['cust_id']; ?></td> <td class="fw-bold">
                    <i class="bi bi-person-badge text-secondary me-2"></i><?= $row['cust_name']." ".$row['cust_surname']; ?>
                </td>
                <td class="text-center">
                    <span class="badge-gender bg-<?= $color ?>-subtle text-<?= $color ?>">
                        <?= $row['gender']; ?>
                    </span>
                </td>
                <td class="text-primary fw-medium">
                    <i class="bi bi-telephone text-muted me-1"></i><?= $row['tel']; ?>
                </td>
                <td>
                    <div class="text-muted small text-truncate" style="max-width: 250px;" title="<?= $row['address']; ?>">
                        <i class="bi bi-geo-alt text-danger me-1"></i><?= $row['address']; ?>
                    </div>
                </td>
                
                <td class="text-center">
                    <div class="text-success small">
                        <i class="bi bi-calendar-check me-1"></i><?= $row['date_create']; ?>
                    </div>
                </td>

                <td class="text-center">
                    <div class="text-muted small">
                        <i class="bi bi-clock-history me-1"></i><?= $row['date_update']; ?>
                    </div>
                </td>
                <td>
                    <span class="small text-dark">
                        <?php if(!empty($row['remark'])) { ?>
                            <i class="bi bi-sticky text-warning me-1"></i><?= $row['remark']; ?>
                        <?php } else { echo "-"; } ?>
                    </span>
                </td>

                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-action btn-outline-warning border-0" onclick="openCustomerModal('<?= $row['cust_id']; ?>','<?= $row['cust_name']; ?>','<?= $row['cust_surname']; ?>','<?= $row['gender']; ?>','<?= $row['tel']; ?>','<?= $row['address']; ?>','<?= $row['remark']; ?>')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-action btn-outline-danger border-0 btn-delete" data-cust_id="<?= $row['cust_id']; ?>">
                            <i class="bi bi-trash"></i>
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

<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold" id="modalTitle"><i class="bi bi-person-lines-fill text-primary me-2"></i>ເພີ່ມລູກຄ້າໃໝ່</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <input type="hidden" id="cust_id">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold"><i class="bi bi-person me-1 text-muted"></i>ຊື່</label>
                        <input type="text" id="cust_name" class="form-control bg-light border-0" style="border-radius: 10px;" placeholder="ຊື່ລູກຄ້າ">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold"><i class="bi bi-person me-1 text-muted"></i>ນາມສະກຸນ</label>
                        <input type="text" id="cust_surname" class="form-control bg-light border-0" style="border-radius: 10px;" placeholder="ນາມສະກຸນ">
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-bold"><i class="bi bi-gender-ambiguous me-1 text-muted"></i>ເພດ</label>
                        <select id="gender" class="form-select bg-light border-0" style="border-radius: 10px;">
                            <option value="ຊາຍ">ຊາຍ</option>
                            <option value="ຍິງ">ຍິງ</option>
                        </select>
                    </div>
                    <div class="col-8">
                        <label class="form-label small fw-bold"><i class="bi bi-telephone me-1 text-muted"></i>ເບີໂທ</label>
                        <input type="text" id="tel" class="form-control bg-light border-0" style="border-radius: 10px;" placeholder="ເບີໂທລະສັບ">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold"><i class="bi bi-geo-alt me-1 text-muted"></i>ທີ່ຢູ່</label>
                        <textarea id="address" class="form-control bg-light border-0" style="border-radius: 10px;" placeholder="ທີ່ຢູ່ປະຈຸບັນ..." rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold"><i class="bi bi-sticky me-1 text-muted"></i>ໝາຍເຫດ</label>
                        <textarea id="remark" class="form-control bg-light border-0" style="border-radius: 10px;" placeholder="ຂໍ້ມູນເພີ່ມເຕີມ..." rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 12px;">ຍົກເລີກ</button>
                <button type="button" id="btnSubmit" class="btn btn-primary px-4 shadow" style="border-radius: 12px;"><i class="bi bi-check-circle me-1"></i> ບັນທຶກ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function openCustomerModal(id='', n='', s='', g='ຊາຍ', t='', a='', r='') {
    $("#modalTitle").html(id ? "<i class='bi bi-pencil-square text-warning me-2'></i>ແກ້ໄຂຂໍ້ມູນລູກຄ້າ" : "<i class='bi bi-person-plus-fill text-primary me-2'></i>ເພີ່ມລູກຄ້າໃໝ່");
    $("#cust_id").val(id); $("#cust_name").val(n); $("#cust_surname").val(s);
    $("#gender").val(g); $("#tel").val(t); $("#address").val(a); $("#remark").val(r);
    $("#customerModal").modal('show');
}

$(document).ready(function() {
    // ຄົ້ນຫາ Real-time
    $("#search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#customerTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // ບັນທຶກຂໍ້ມູນ
    $("#btnSubmit").click(function() {
        const data = {
            cust_id: $("#cust_id").val(),
            cust_name: $("#cust_name").val(),
            cust_surname: $("#cust_surname").val(),
            gender: $("#gender").val(),
            tel: $("#tel").val(),
            address: $("#address").val(),
            remark: $("#remark").val()
        };

        if(!data.cust_name || !data.tel) {
            Swal.fire({
                icon: 'warning',
                title: 'ຄຳເຕືອນ',
                text: 'ກະລຸນາປ້ອນ ຊື່ ແລະ ເບີໂທລະສັບ ໃຫ້ຄົບຖ້ວນ!',
                confirmButtonColor: '#4361ee',
                confirmButtonText: 'ຕົກລົງ'
            });
            return;
        }

        // ກວດສອບວ່າເປັນການແກ້ໄຂ ຫຼື ເພີ່ມໃໝ່ ເພື່ອເລືອກ URL ແລະ ຂໍ້ຄວາມ Alert
        const isEdit = data.cust_id ? true : false;
        const targetUrl = isEdit ? "save_customers.php" : "insert_customers.php";

        $.post(targetUrl, data, function(res) {
            if(res.trim() === "success") {
                Swal.fire({ 
                    icon: 'success', 
                    title: isEdit ? 'ອັບເດດສຳເລັດ!' : 'ບັນທຶກສຳເລັດ!', 
                    text: isEdit ? 'ແກ້ໄຂຂໍ້ມູນລູກຄ້າຮຽບຮ້ອຍແລ້ວ.' : 'ເພີ່ມຂໍ້ມູນລູກຄ້າໃໝ່ຮຽບຮ້ອຍແລ້ວ.',
                    confirmButtonColor: '#4361ee',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ເກີດຂໍ້ຜິດພາດ!',
                    text: res,
                    confirmButtonColor: '#ef233c',
                    confirmButtonText: 'ປິດ'
                });
            }
        });
    });

    // ລຶບຂໍ້ມູນ
    $(document).on("click", ".btn-delete", function() {
        const id = $(this).data("cust_id");
        Swal.fire({
            title: 'ຢືນຢັນການລຶບ?', 
            text: "ຂໍ້ມູນລູກຄ້າລາຍການນີ້ຈະຖືກລຶບອອກຖາວອນ!",
            icon: 'warning', 
            showCancelButton: true,
            confirmButtonText: 'ລຶບເລີຍ', 
            cancelButtonText: 'ຍົກເລີກ', 
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = "delete_customers.php?cust_id=" + id;
        });
    });
});
</script>
</body>
</html>
<?php
}
?>