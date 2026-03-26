<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລາຍງານຂໍ້ມູນເມືອງ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Phetsarath OT', sans-serif;
            padding-top: 50px;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(45deg, #17a2b8, #007bff);
            color: white;
            padding: 20px;
            border-bottom: none;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background-color: #f1f4f9;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
        }
        .table tbody td {
            vertical-align: middle;
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .btn-action {
            border-radius: 10px;
            padding: 8px 16px;
            transition: all 0.3s;
            font-weight: 500;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .badge-id {
            background-color: #e7f1ff;
            color: #0d6efd;
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 8px;
        }
    </style>

    <script>
        $(function(){
            $('.delete').on('click', function(e){
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລົບ?',
                    text: "ທ່ານແນ່ໃຈຫຼືບໍ່ທີ່ຈະລົບຂໍ້ມູນເມືອງນີ້?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ຕົກລົງ, ລົບເລີຍ!',
                    cancelButtonText: 'ຍົກເລີກ',
                    borderRadius: '15px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        });
    </script>
</head>
<body>

<?php
    include("../cennect_dbstock.php");
    $sql = mysqli_query($connect, "SELECT a.pro_name, b.dis_name, b.remark, b.dis_id 
                                   FROM districts AS b, provinces AS a 
                                   WHERE a.pro_id = b.pro_id 
                                   ORDER BY b.dis_id DESC");
?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 fw-bold">
                        <i class="bi bi-geo-alt-fill me-2"></i> ຕາຕະລາງລາຍຊື່ເມືອງ
                    </h3>
                    <a href="form_districts.php" class="btn btn-light rounded-pill px-4 fw-bold text-info">
                        <i class="bi bi-plus-circle-fill me-1"></i> ເພີ່ມເມືອງໃໝ່
                    </a>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover text-center">
                            <thead>
                                <tr>
                                    <th width="10%">ລຳດັບ</th>
                                    <th width="25%">ຊື່ແຂວງ</th>
                                    <th width="25%">ຊື່ເມືອງ</th>
                                    <th width="20%">ໝາຍເຫດ</th>
                                    <th width="20%">ຈັດການ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i = 1;
                                    while($form = mysqli_fetch_array($sql)){
                                ?>
                                <tr>
                                    <td><span class="badge-id"><?= $i ?></span></td>
                                    <td class="fw-bold text-secondary"><?= $form['pro_name']; ?></td>
                                    <td class="fw-bold text-primary"><?= $form['dis_name']; ?></td>
                                    <td class="text-muted small"><?= $form['remark'] ?: '-'; ?></td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="update_districts.php?dis_id=<?= $form['dis_id'];?>" 
                                               class="btn btn-success btn-action btn-sm">
                                                <i class="bi bi-pencil-square me-1"></i> ແກ້ໄຂ
                                            </a>
                                            <a href="delete_districts.php?dis_id=<?= $form['dis_id'];?>" 
                                               class="btn btn-danger btn-action btn-sm delete">
                                                <i class="bi bi-trash3-fill me-1"></i> ລົບ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php $i++; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3 text-end px-4">
                    <span class="text-muted">ລວມທັງໝົດ: <strong><?= ($i-1) ?></strong> ລາຍການ</span>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>