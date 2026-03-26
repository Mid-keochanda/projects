<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຕາຕະລາງຂໍ້ມູນບ້ານ</title>
    
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script>
    <script src="../jquery.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');

        body {
            background-color: #f4f7f9;
            font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif;
        }

        .main-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-top: 50px;
        }

        .card-header {
            background: linear-gradient(45deg, #0dcaf0, #0aa2c0) !important;
            color: white !important;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        /* Table Styling */
        .table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-top: none;
            padding: 15px;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 5px 15px;
            color: #334155;
        }

        /* Buttons Styling */
        .btn-action {
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .btn-edit { background-color: #ecfdf5; color: #059669; border: 1px solid #10b981; }
        .btn-edit:hover { background-color: #10b981; color: white; }

        .btn-delete { background-color: #fef2f2; color: #dc2626; border: 1px solid #ef4444; }
        .btn-delete:hover { background-color: #ef4444; color: white; }

        .badge-info {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>

    <script>
        $(function(){
            $('.delete').on('click', function(e){
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລົບຂໍ້ມູນ?',
                    text: 'ທ່ານຈະບໍ່ສາມາດກູ້ຄືນຂໍ້ມູນນີ້ໄດ້!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'ລົບທັນທີ',
                    cancelButtonText: 'ຍົກເລີກ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                })
            })
        })
    </script>
</head>
<body>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card main-card">
                <div class="card-header text-center">
                    <h3 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i> ຕາຕະລາງການຈັດການຂໍ້ມູນບ້ານ</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="text-center">
                                <tr>
                                    <th width="80">ລຳດັບ</th>
                                    <th>ຊື່ແຂວງ</th>
                                    <th>ຊື່ເມືອງ</th>
                                    <th>ຊື່ບ້ານ</th>
                                    <th>ໝາຍເຫດ</th>
                                    <th width="200">ຈັດການຂໍ້ມູນ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    include("../cennect_dbstock.php");
                                    // ໃຊ້ JOIN ເພື່ອໃຫ້ Query ສັ້ນ ແລະ ເຮັດວຽກໄວຂຶ້ນ
                                    $sql = mysqli_query($connect, "SELECT a.pro_name, b.dis_name, c.vil_id, c.vil_name, c.remark 
                                                                  FROM villages AS c
                                                                  JOIN districts AS b ON c.dis_id = b.dis_id
                                                                  JOIN provinces AS a ON b.pro_id = a.pro_id
                                                                  ORDER BY c.vil_id DESC");
                                    $i = 1;
                                    while($form = mysqli_fetch_array($sql)){
                                ?>
                                <tr>
                                    <td class="text-center fw-bold text-muted"><?= $i ?></td>
                                    <td class="text-center">
                                        <span class="badge-info"><?= $form['pro_name']; ?></span>
                                    </td>
                                    <td class="text-center"><?= $form['dis_name']; ?></td>
                                    <td class="text-center fw-bold"><?= $form['vil_name']; ?></td>
                                    <td class="text-center text-secondary small"><?= $form['remark'] ?: '-'; ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="update_villages.php?vil_id=<?= $form['vil_id'];?>" class="btn btn-action btn-edit">
                                                <i class="bi bi-pencil-square me-1"></i> ແກ້ໄຂ
                                            </a>
                                            <a href="delete_villages.php?vil_id=<?= $form['vil_id'];?>" class="btn btn-action btn-delete delete">
                                                <i class="bi bi-trash me-1"></i> ລົບ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    $i++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <p class="text-muted small mb-0">ລະບົບຈັດການຂໍ້ມູນບ້ານ v1.0</p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>