<?php
    include('../cennect_dbstock.php');
   
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການຂໍ້ມູນບ້ານ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../jquery.js"></script>
    
    <style>
        body { font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif; background-color: #f4f7f9; }
        .main-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-top: 20px; }
        .card-header { background: linear-gradient(45deg, #0dcaf0, #0aa2c0); color: white; border-radius: 15px 15px 0 0; padding: 20px; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; padding: 5px 10px; border-radius: 6px; font-weight: 600; }
    </style>
</head>
<body>

<div class="container py-4">
    <?php if (isset($success_save)): ?>
        <script>
            Swal.fire({ icon: 'success', title: 'ບັນທຶກສຳເລັດ', showConfirmButton: false, timer: 1500 })
            .then(() => { window.location.href = 'select_villages.php'; });
        </script>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
        <script>
            Swal.fire({ icon: 'error', title: 'ຜິດພາດ!', text: '<?= $error_msg ?>', confirmButtonColor: '#dc3545' });
        </script>
    <?php endif; ?>

    <div class="card main-card">
        <div class="card-header text-center">
            <h3 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i> ລາຍຊື່ບ້ານໃນລະບົບ</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="80">ລຳດັບ</th>
                            <th>ແຂວງ</th>
                            <th>ເມືອງ</th>
                            <th>ຊື່ບ້ານ</th>
                            <th>ໝາຍເຫດ</th>
                            <th width="180">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query ຂໍ້ມູນໂດຍໃຊ້ JOIN ໃຫ້ຖືກກັບ Structure: vil_id, vil_name
                        $sql = mysqli_query($connect, "SELECT a.pro_name, b.dis_name, c.vill_id, c.vill_name, c.remark 
                                                      FROM villages AS c
                                                      JOIN districts AS b ON c.dis_id = b.dis_id
                                                      JOIN provinces AS a ON c.pro_id = a.pro_id
                                                      ORDER BY c.vill_id DESC");
                        $i = 1;
                        while($row = mysqli_fetch_array($sql)):
                        ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>
                            <td class="text-center"><span class="badge-info"><?= $row['pro_name'] ?></span></td>
                            <td class="text-center"><?= $row['dis_name'] ?></td>
                            <td class="text-center fw-bold"><?= $row['vill_name'] ?></td>
                            <td class="text-center text-muted small"><?= $row['remark'] ?: '-' ?></td>
                            <td class="text-center">
                                <div class="btn-group gap-2">
                                    <a href="update_villages.php?vill_id=<?= $row['vill_id'] ?>" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="delete_villages.php?vill_id=<?= $row['vill_id'] ?>" class="btn btn-sm btn-outline-danger btn-delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
    // ຕົວຢ່າງ: ຖ້າ Redirect ມາຈາກ insert_villages.php?msg=success
    if (isset($_GET['msg']) && $_GET['msg'] == 'success'): 
?>
    <script>
        Swal.fire({ icon: 'success', title: 'ບັນທຶກສຳເລັດ', showConfirmButton: false, timer: 1500 });
    </script>
<?php endif; ?>

<?php
    $sql = mysqli_query($connect, "SELECT a.pro_name, b.dis_name, c.vill_id, c.vill_name, c.remark 
                                  FROM villages AS c
                                  LEFT JOIN districts AS b ON c.dis_id = b.dis_id
                                  LEFT JOIN provinces AS a ON c.pro_id = a.pro_id
                                  ORDER BY c.vill_id DESC");
?>

<script>
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        Swal.fire({
            title: 'ຢືນຢັນການລົບ?',
            text: "ຂໍ້ມູນນີ້ຈະຖືກລົບອອກຈາກລະບົບຖາວອນ!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ຢືນຢັນ, ລົບເລີຍ!', // ປ່ຽນໃຫ້ຊັດເຈນ
            cancelButtonText: 'ຍົກເລີກ'
        }).then((result) => {
            if (result.isConfirmed) { 
                window.location.href = url; 
            }
        });
    });
</script>

</body>
</html>