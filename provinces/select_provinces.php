
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <title>ລາຍງານຂໍ້ມູນແຂວງ</title>
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Phetsarath OT', sans-serif; padding-top: 30px; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .card-header { background: linear-gradient(45deg, #0d6efd, #00c6ff); color: white; padding: 20px; border: none; }
        .table thead th { background-color: #f8f9fa; color: #495057; text-align: center; border-bottom: 2px solid #dee2e6; }
        .table tbody td { vertical-align: middle; text-align: center; padding: 12px; }
        .btn-action { border-radius: 10px; padding: 5px 15px; font-size: 0.9rem; transition: all 0.3s; }
        .btn-edit { background-color: #ffc107; color: #000; border: none; }
        .btn-delete { background-color: #dc3545; color: #fff; border: none; }
        .btn-edit:hover, .btn-delete:hover { transform: translateY(-2px); opacity: 0.9; }
    </style>

    <script>
        $(function(){
            $('.delete').on('click', function(e){
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລົບ?',
                    text: "ຂໍ້ມູນນີ້ຈະຖືກລົບອອກຈາກລະບົບຖາວອນ",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ຢືນຢັນ',
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
        // ກວດສອບຊື່ໄຟລ໌ connect ໃຫ້ຖືກຕ້ອງ
        include("../cennect_dbstock.php"); 
        // ດຶງຂໍ້ມູນລຽງຕາມ ID ຈາກນ້ອຍໄປຫາຫຼາຍ
        $sql = mysqli_query($connect, "SELECT * FROM provinces ORDER BY pro_id ASC");
    ?>

    <div class="container mb-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold"><i class="fas fa-database me-2"></i>ຈັດການຂໍ້ມູນແຂວງ</h4>
                <a href="form_provinces.php" class="btn btn-light rounded-pill px-4 fw-bold">
                    <i class="fas fa-plus-circle me-1"></i> ເພີ່ມແຂວງ
                </a>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="10%">ລຳດັບ</th>
                                <th width="15%">ລະຫັດແຂວງ</th>
                                <th width="35%">ຊື່ແຂວງ</th>
                                <th width="20%">ໝາຍເຫດ</th>
                                <th width="20%">ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while($mid = mysqli_fetch_array($sql)){
                            ?>
                            <tr>
                                <td class="text-muted fw-bold"><?= $i ?></td>
                                <td><span class="badge bg-info text-dark"><?= $mid["pro_id"]; ?></span></td>
                                <td class="text-start ps-5">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <strong><?= $mid["pro_name"]; ?></strong>
                                </td>
                                <td class="text-muted"><?= ($mid["remark"] != "") ? $mid["remark"] : "-"; ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="update.php?pro_id=<?=$mid['pro_id'];?>" 
                                           class="btn btn-action btn-edit" title="ແກ້ໄຂ">
                                            <i class="fas fa-cog fa-spin"></i>
                                        </a>
                                        <a href="delete.php?pro_id=<?=$mid['pro_id'];?>" 
                                           class="btn btn-action btn-delete delete" title="ລົບ">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php $i++; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 text-center py-3">
                <span class="badge rounded-pill bg-primary px-3">ທັງໝົດ: <?= ($i-1) ?> ແຂວງ</span>
            </div>
        </div>
    </div>

</body>
</html>
