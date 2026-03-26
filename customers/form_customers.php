<?php include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລະບົບຈັດການລູກຄ້າ - Car Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; }
        .navbar { background: linear-gradient(90deg, #4e73df 0%, #224abe 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .card-header { background-color: #fff; border-bottom: 1px solid #eee; border-radius: 15px 15px 0 0 !important; padding: 1.2rem; }
        .btn-primary { background-color: #4e73df; border: none; border-radius: 8px; padding: 10px 20px; transition: 0.3s; }
        .btn-primary:hover { background-color: #224abe; transform: translateY(-2px); }
        .form-control, .form-select { border-radius: 8px; padding: 10px; border: 1px solid #d1d3e2; }
        .table thead { background-color: #f8f9fc; color: #4e73df; }
        .badge-gender { padding: 5px 12px; border-radius: 20px; font-weight: 500; }
        .search-box { border-radius: 20px; padding-left: 40px; }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa; }
        
        /* ປັບແຕ່ງປຸ່ມຈັດການ */
        .btn-action { padding: 5px 12px; font-size: 0.85rem; border-radius: 6px; font-weight: 500; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="bi bi-car-front-fill me-2"></i> GARAGE SYSTEM</a>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-person-plus-fill me-2"></i> ເພີ່ມລູກຄ້າໃໝ່</h5>
                </div>
                <div class="card-body">
                    <form id="customerForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ຊື່</label>
                                <input type="text" id="cust_name" class="form-control" placeholder="ຊື່..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ນາມສະກຸນ</label>
                                <input type="text" id="cust_surname" class="form-control" placeholder="ນາມສະກຸນ...">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ເພດ / ເບີໂທ</label>
                            <div class="input-group">
                                <select id="gender" class="form-select" style="max-width: 100px;">
                                    <option value="ຊາຍ">ຊາຍ</option>
                                    <option value="ຍິງ">ຍິງ</option>
                                </select>
                                <input type="text" id="tel" class="form-control" placeholder="ເບີໂທ..." required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ທີ່ຢູ່ (Address)</label>
                            <textarea id="address" class="form-control" rows="2" placeholder="ບ້ານ, ເມືອງ, ແຂວງ..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">ໝາຍເຫດ (Remark)</label>
                            <textarea id="remark" class="form-control" rows="2" placeholder="ຂໍ້ມູນເພີ່ມເຕີມ..."></textarea>
                        </div>
                        <button type="button" id="saveBtn" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-save me-2"></i> ບັນທຶກຂໍ້ມູນ
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-people-fill me-2"></i> ລາຍຊື່ລູກຄ້າ</h5>
                    <div class="position-relative w-50">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="search" class="form-control search-box" placeholder="ຄົ້ນຫາລູກຄ້າ...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="customerTable">
                            <thead>
                                <tr>
                                    <th class="ps-4">ລຳດັບ</th>
                                    <th>ຊື່ ແລະ ນາມສະກຸນ</th>
                                    <th>ເພດ</th>
                                    <th>ເບີໂທ</th>
                                    <th>ທີ່ຢູ່</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $sql = mysqli_query($connect, "SELECT * FROM customers ORDER BY cust_id DESC");
                                while($row = mysqli_fetch_array($sql)) {
                                    $bg_gender = ($row['gender'] == 'ຊາຍ') ? 'bg-info-subtle text-info' : 'bg-danger-subtle text-danger';
                                ?>
                                <tr>
                                    <td class="ps-4"><?php echo $i++; ?></td>
                                    <td><div class="fw-bold"><?php echo $row['cust_name'] . " " . $row['cust_surname']; ?></div></td>
                                    <td><span class="badge <?php echo $bg_gender; ?> badge-gender"><?php echo $row['gender']; ?></span></td>
                                    <td><?php echo $row['tel']; ?></td>
                                    <td><small class="text-muted"><?php echo $row['address']; ?></small></td>
                                 
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header">
                <h5 class="modal-title text-primary fw-bold"><i class="bi bi-pencil-square me-2"></i> ແກ້ໄຂຂໍ້ມູນລູກຄ້າ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_cust_id">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label small fw-bold">ຊື່</label>
                            <input type="text" id="edit_cust_name" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold">ນາມສະກຸນ</label>
                            <input type="text" id="edit_cust_surname" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ເພດ / ເບີໂທ</label>
                        <div class="input-group">
                            <select id="edit_gender" class="form-select" style="max-width: 100px;">
                                <option value="ຊາຍ">ຊາຍ</option>
                                <option value="ຍິງ">ຍິງ</option>
                            </select>
                            <input type="text" id="edit_tel" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ທີ່ຢູ່ (Address)</label>
                        <textarea id="edit_address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ໝາຍເຫດ (Remark)</label>
                        <textarea id="edit_remark" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ຍົກເລີກ</button>
                <button type="button" id="updateBtn" class="btn btn-primary px-4">ບັນທຶກການແກ້ໄຂ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
     $(function(){
    $(document).ready(function() {
        // ເພີ່ມຂໍ້ມູນ
        $("#saveBtn").click(function() {
            const data = {
                cust_name: $("#cust_name").val(),
                cust_surname: $("#cust_surname").val(),
                gender: $("#gender").val(),
                tel: $("#tel").val(),
                address: $("#address").val(),
                remark: $("#remark").val()
            };

            if(!data.cust_name || !data.tel) {
                Swal.fire('ຄຳເຕືອນ', 'ກະລຸນາປ້ອນ ຊື່ ແລະ ເບີໂທ!', 'warning');
                return;
            }

            $.post("insert_customers.php", data, function(res) {
                if(res.trim() == "success") {
                    Swal.fire({ title: 'ສຳເລັດ!', icon: 'success', timer: 1000, showConfirmButton: false }).then(() => { location.reload(); });
                } else {
                    Swal.fire('ຜິດພາດ!', res, 'error');
                }
            });
        });

        // ອັບເດດຂໍ້ມູນ
        $("#updateBtn").click(function() {
            const data = {
                cust_id: $("#edit_cust_id").val(),
                cust_name: $("#edit_cust_name").val(),
                cust_surname: $("#edit_cust_surname").val(),
                gender: $("#edit_gender").val(),
                tel: $("#edit_tel").val(),
                address: $("#edit_address").val(),
                remark: $("#edit_remark").val()
            };

            $.post("update_customers.php", data, function(res) {
                if(res.trim() == "success") {
                    Swal.fire({ title: 'ອັບເດດສຳເລັດ!', icon: 'success', timer: 1000, showConfirmButton: false }).then(() => { location.reload(); });
                } else {
                    Swal.fire('ຜິດພາດ!', res, 'error');
                }
            });
        });

        // ຄົ້ນຫາ
        $("#search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#customerTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    // --- Functions ສາມາດເອີ້ນໃຊ້ໄດ້ທົ່ວໄປ ---

    function openEditModal(id, name, surname, gender, tel, address, remark) {
        $("#edit_cust_id").val(id);
        $("#edit_cust_name").val(name);
        $("#edit_cust_surname").val(surname);
        $("#edit_gender").val(gender);
        $("#edit_tel").val(tel);
        $("#edit_address").val(address);
        $("#edit_remark").val(remark);
        $("#editModal").modal('show');
    };

    $(document).on('click', '.btn-delete', function(e){
                e.preventDefault();
                var url = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລຶບ?',
                    text: "ທ່ານຈະບໍ່ສາມາດກູ້ຄືນຂໍ້ມູນນີ້ໄດ້!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea5455',
                    cancelButtonColor: '#b2bec3',
                    confirmButtonText: 'ລຶບອອກ',
                    cancelButtonText: 'ຍົກເລີກ'
                }).then((result) => {
                    if (result.isConfirmed) { window.location.href = url; }
                });
            });
        });
        
</script>
</body>
</html>