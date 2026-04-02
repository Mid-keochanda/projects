<?php include("../cennect_dbstock.php"); ?>
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
        .badge-gender { padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; display: inline-block; }
        .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; }
        .search-box { background: #f1f2f6; border-radius: 0 12px 12px 0 !important; border: none; }
        .input-group-text { background: #f1f2f6; border-radius: 12px 0 0 12px !important; border: none; }
        td div.small {
        font-size: 0.8rem;
        white-space: nowrap; /* ບໍ່ໃຫ້ວັນທີຂຶ້ນແຖວໃໝ່ */
    }
    .bi-calendar-check { color: #2ecc71; } /* ສີຂຽວອ່ອນ */
    .bi-clock-history { color: #95a5a6; } /* ສີເທົາ */
    </style>
</head>
<body>

<div class="container-fluid py-5 px-md-5">
    <div class="card">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <h4 class="fw-bold mb-1">ຈັດການຂໍ້ມູນລູກຄ້າ</h4>
                <p class="text-muted small mb-0">ລະບົບຈັດການ ແລະ ຕິດຕາມຂໍ້ມູນລູກຄ້າທັງໝົດ</p>
            </div>

            <div class="d-flex gap-2">
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="search" class="form-control search-box" placeholder="ຄົ້ນຫາ...">
                </div>
                <button class="btn btn-primary px-4 shadow-sm" style="border-radius: 12px; white-space: nowrap;" onclick="openCustomerModal()">
                    <i class="bi bi-person-plus-fill me-2"></i> ເພີ່ມລູກຄ້າ
                </button>
            </div>
        </div>

        <div class="card-body px-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="customerTable">
                    <thead class="table-light">
                        <thead class="table-light">
    <tr>
        <th class="text-center">ລຳດັບ</th>
        <th>ຊື່ ແລະ ນາມສະກຸນ</th>
        <th class="text-center">ເພດ</th>
        <th>ເບີໂທລະສັບ</th>
        <th>ທີ່ຢູ່</th>
        <th>ວັນທີບັນທຶກ</th> 
        <th>ອັບເດດຫຼ້າສຸດ</th> 
        <th>ໝາຍເຫດ</th>
        <th class="text-center">ຈັດການ</th>
    </tr>
</thead>
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
        <td class="fw-bold"><?= $row['cust_name']." ".$row['cust_surname']; ?></td>
        <td class="text-center">
            <span class="badge-gender bg-<?= $color ?>-subtle text-<?= $color ?>">
                <?= $row['gender']; ?>
            </span>
        </td>
        <td class="text-primary fw-medium"><?= $row['tel']; ?></td>
        <td>
            <div class="text-muted small text-truncate" style="max-width: 150px;" title="<?= $row['address']; ?>">
                <?= $row['address']; ?>
            </div>
        </td>
        
        <td>
            <div class="text-success small">
                <i class="bi bi-calendar-check me-1"></i><?= $row['date_create']; ?>
            </div>
        </td>

        <td>
            <div class="text-muted small">
                <i class="bi bi-clock-history me-1"></i><?= $row['date_update']; ?>
            </div>
        </td>
        <td class="fw-bold"><?= $row['remark']; ?></td>

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
                <h5 class="fw-bold" id="modalTitle">ເພີ່ມລູກຄ້າໃໝ່</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="cust_id">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">ຊື່</label>
                        <input type="text" id="cust_name" class="form-control bg-light border-0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">ນາມສະກຸນ</label>
                        <input type="text" id="cust_surname" class="form-control bg-light border-0">
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-bold">ເພດ</label>
                        <select id="gender" class="form-select bg-light border-0">
                            <option value="ຊາຍ">ຊາຍ</option>
                            <option value="ຍິງ">ຍິງ</option>
                        </select>
                    </div>
                    <div class="col-8">
                        <label class="form-label small fw-bold">ເບີໂທ</label>
                        <input type="text" id="tel" class="form-control bg-light border-0">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">ທີ່ຢູ່</label>
                        <textarea id="address" class="form-control bg-light border-0" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">ໝາຍເຫດ</label>
                        <textarea id="remark" class="form-control bg-light border-0" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 12px;">ຍົກເລີກ</button>
                <button type="button" id="btnSubmit" class="btn btn-primary px-4 shadow" style="border-radius: 12px;">ບັນທຶກ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function openCustomerModal(id='', n='', s='', g='ຊາຍ', t='', a='', r='') {
    $("#modalTitle").text(id ? "ແກ້ໄຂຂໍ້ມູນລູກຄ້າ" : "ເພີ່ມລູກຄ້າໃໝ່");
    $("#cust_id").val(id); $("#cust_name").val(n); $("#cust_surname").val(s);
    $("#gender").val(g); $("#tel").val(t); $("#address").val(a); $("#remark").val(r);
    $("#customerModal").modal('show');
}

$(document).ready(function() {
    // ຄົ້ນຫາ
    $("#search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#customerTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // ບັນທຶກ
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

        if(!data.cust_name || !data.tel) return Swal.fire('ຄຳເຕືອນ', 'ປ້ອນຊື່ ແລະ ເບີໂທ!', 'warning');

        $.post(data.cust_id ? "save_customers.php" : "insert_customers.php", data, function(res) {
            if(res.trim() === "success") {
                Swal.fire({ icon:'success', title:'ສຳເລັດ', timer:1000, showConfirmButton:false }).then(() => location.reload());
            } else {
                Swal.fire('ຜິດພາດ', res, 'error');
            }
        });
    });

    // ລຶບ
    $(document).on("click", ".btn-delete", function() {
        const id = $(this).data("cust_id");
        Swal.fire({
            title: 'ຢືນຢັນການລຶບ?', icon: 'warning', showCancelButton: true,
            confirmButtonText: 'ລຶບ', cancelButtonText: 'ຍົກເລີກ', confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = "delete_customers.php?cust_id=" + id;
        });
    });
});
</script>
</body>
</html>