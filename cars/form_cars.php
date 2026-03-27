<?php include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ເພີ່ມຂໍ້ມູນລົດ - Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        body { background-color: #f4f7f6; font-family: 'Noto Sans Lao', sans-serif; }
        .card-compact { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 550px; margin: auto; }
        .section-title { font-size: 0.95rem; font-weight: 700; color: #4e73df; border-bottom: 2px solid #4e73df; display: inline-block; margin-bottom: 15px; }
        .form-label { font-weight: 600; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card card-compact">
        <div class="card-header bg-white border-0 pt-4 text-center">
            <h5 class="fw-bold text-primary"><i class="fas fa-plus-circle me-2"></i>ລົງທະບຽນລົດໃໝ່</h5>
        </div>
        <div class="card-body px-4 pb-4">
            <form id="carForm">
                
                <div class="mb-4">
                    <label class="form-label text-primary">ເລືອກເຈົ້າຂອງລົດ (ຄົ້ນຫາດ້ວຍຊື່ ຫຼື ເບີໂທ)</label>
                    <select name="cust_id" id="cust_id" class="form-select select2-customer" required>
                        <option value="">-- ກະລຸນາເລືອກລູກຄ້າ --</option>
                        <?php
                        // ດຶງຂໍ້ມູນລູກຄ້າທັງໝົດມາສະແດງ
                        $sql_cust = "SELECT cust_id, cust_name, cust_surname, tel FROM customers ORDER BY cust_name ASC";
                        $res_cust = mysqli_query($connect, $sql_cust);
                        while($row_c = mysqli_fetch_array($res_cust)) {
                            // ສະແດງທັງ ຊື່ ແລະ ເບີໂທ ເພື່ອໃຫ້ເລືອກງ່າຍ
                            echo "<option value='".$row_c['cust_id']."'>".$row_c['cust_name']." ".$row_c['cust_surname']." (".$row_c['tel'].")</option>";
                        }
                        ?>
                    </select>
                    <div class="mt-2 text-end">
                       <a href="../customers/form_customers.php" class="btn btn-sm btn-outline-secondary border-0">
                       <i class="fas fa-user-plus me-1"></i> ເພີ່ມລູກຄ້າໃໝ່</a>
                    </div>
                </div>

                <div class="section-title"><i class="fas fa-car me-1"></i> ຂໍ້ມູນລົດ</div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">ເລກທະບຽນ</label>
                        <input type="text" name="car_plate" class="form-control" placeholder="ກນ 1234" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">ຍີ່ຫໍ້</label>
                        <input type="text" name="car_brand" class="form-control" placeholder="Toyota">
                    </div>
                    <div class="col-6">
                        <label class="form-label">ລຸ້ນ</label>
                        <input type="text" name="car_model" class="form-control" placeholder="Vigo">
                    </div>
                    <div class="col-6">
                        <label class="form-label">ສີ</label>
                        <input type="text" name="car_color" class="form-control" placeholder="ຂາວ">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">ໝາຍເຫດ</label>
                    <input type="text" name="remark" class="form-control" placeholder="...">
                </div>

                <div class="row g-2">
                    <div class="col-8">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm py-2 fw-bold">ບັນທຶກ</button>
                    </div>
                    <div class="col-4">
                        <a href="select_cars.php" class="btn btn-light w-100 border py-2">ຍົກເລີກ</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // ເປີດໃຊ້ງານ Select2 ໃຫ້ຄົ້ນຫາຊື່ລູກຄ້າໄດ້
        $('.select2-customer').select2({
            theme: 'bootstrap-5',
            placeholder: '-- ຄົ້ນຫາລູກຄ້າ --',
            allowClear: true
        });

        // ລະບົບ Ajax ສົ່ງຂໍ້ມູນ
        $('#carForm').on('submit', function(e) {
            e.preventDefault();
            $.post('insert_cars.php', $(this).serialize(), function(res) {
                if(res.trim() == "success") {
                    Swal.fire({ icon: 'success', title: 'ບັນທຶກສຳເລັດ', timer: 1500, showConfirmButton: false })
                    .then(() => { window.location.href = 'form_cars.php'; });
                } else {
                    Swal.fire('ຜິດພາດ!', res, 'error');
                }
            });
        });
    });
</script>
</body>
</html>