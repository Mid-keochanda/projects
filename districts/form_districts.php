<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນເມືອງ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { background-color: #f0f2f5; font-family: 'Phetsarath OT', sans-serif; padding: 20px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .card-header { background: linear-gradient(45deg, #0d6efd, #00c6ff); border-radius: 15px 15px 0 0 !important; padding: 15px; }
        .form-label { font-weight: 600; color: #495057; }
        .form-control, .form-select { border-radius: 10px; padding: 10px; border: 1px solid #ced4da; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); }
        .btn-custom { border-radius: 10px; padding: 10px 20px; font-weight: 600; transition: all 0.3s; }
        .btn-custom:hover { transform: translateY(-2px); }
        .table thead th { background-color: #f8f9fa; color: #333; border-bottom: 2px solid #dee2e6; text-align: center; }
        .table tbody td { vertical-align: middle; text-align: center; }
        .badge-id { background-color: #e9ecef; color: #495057; padding: 5px 10px; border-radius: 8px; font-weight: bold; }
    </style>

    <script>
        $(function(){
            $('#save').click(function(){
                var pro_id = $('#pro_id').val();
                var dis_name = $('#dis_name').val();
                var remark = $('#remark').val();

                if(pro_id == ""){
                    Swal.fire({ icon: 'warning', title: 'ຄຳເຕືອນ', text: 'ກະລຸນາເລືອກແຂວງກ່ອນ', confirmButtonText: 'ຕົກລົງ' });
                } else if(dis_name == ""){
                    Swal.fire({ icon: 'warning', title: 'ຄຳເຕືອນ', text: 'ກະລຸນາປ້ອນຊື່ເມືອງ', confirmButtonText: 'ຕົກລົງ' });
                } else {
                    $.get('insert_districts.php', {
                        pro_id: pro_id,
                        dis_name: dis_name,
                        remark: remark
                    }, function(output){
                        $('#show').html(output);
                        // ສາມາດໃສ່ window.location.reload() ທີ່ນີ້ຖ້າຢາກໃຫ້ Refresh ໜ້າ
                    });
                }
            });

            // SweetAlert Confirm Delete
            $(document).on('click', '.delete', function(e){
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລົບ?',
                    text: "ທ່ານຕ້ອງການລົບຂໍ້ມູນເມືອງນີ້ແທ້ຫຼືບໍ່?",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
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

<?php
    include("../cennect_dbstock.php");
    // SQL Join ເພື່ອເອົາຊື່ແຂວງມາສະແດງ
    $sql = mysqli_query($connect, "SELECT a.pro_name, b.dis_name, b.remark, b.dis_id 
                                   FROM districts AS b 
                                   INNER JOIN provinces AS a ON a.pro_id = b.pro_id 
                                   ORDER BY b.dis_id DESC");
?>

<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header text-center">
            <h4 class="text-white mb-0"><i class="fas fa-edit me-2"></i>ຟອມບັນທຶກຂໍ້ມູນເມືອງ</h4>
        </div>
        <div class="card-body p-4">
            <form id="districtForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">ຊື່ແຂວງ</label>
                        <select class="form-select" id="pro_id">
                            <option value="">-- ເລືອກແຂວງ --</option>
                            <?php
                                $select_pro = mysqli_query($connect, "SELECT * FROM provinces");
                                while($data = mysqli_fetch_array($select_pro)){
                                    echo "<option value='".$data['pro_id']."'>".$data['pro_name']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ຊື່ເມືອງ</label>
                        <input type="text" id="dis_name" class="form-control" placeholder="ປ້ອນຊື່ເມືອງ...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ໝາຍເຫດ</label>
                        <input type="text" id="remark" class="form-control" placeholder="ໝາຍເຫດ...">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="button" class="btn btn-success btn-custom flex-grow-1" id="save">
                            <i class="fas fa-save me-1"></i> ບັນທຶກ
                        </button>
                        <button type="reset" class="btn btn-danger btn-custom flex-grow-1">
                            <i class="fas fa-redo me-1"></i> ລ້າງຂໍ້ມູນ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="80">ລຳດັບ</th>
                            <th>ຊື່ແຂວງ</th>
                            <th>ຊື່ເມືອງ</th>
                            <th>ໝາຍເຫດ</th>
                            <th width="120">ແກ້ໄຂ</th>
                            <th width="120">ລົບ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 1;
                            while($form = mysqli_fetch_array($sql)){
                        ?>
                        <tr>
                            <td><span class="badge-id"><?= $i ?></span></td>
                            <td class="text-primary fw-bold"><?= $form['pro_name']; ?></td>
                            <td><?= $form['dis_name']; ?></td>
                            <td class="text-muted small"><?= $form['remark'] ?: '-'; ?></td>
                            <td>
                                <a href="update_districts.php?dis_id=<?= $form['dis_id'];?>" class="btn btn-warning btn-sm rounded-pill px-3">
                                    <i class="fas fa-cog fa-spin me-1"></i> ແກ້ໄຂ
                                </a>
                            </td>
                            <td>
                                <a href="delete_districts.php?dis_id=<?= $form['dis_id'];?>" class="btn btn-danger btn-sm rounded-pill px-3 delete">
                                    <i class="fas fa-trash me-1"></i> ລົບ
                                </a>
                            </td>
                        </tr>
                        <?php $i++; } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div id="show" class="mt-3"></div>
</div>

</body>
</html>