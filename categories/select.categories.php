<?php
// 1. ປ່ຽນຊື່ໄຟລ໌ໃຫ້ຕົງກັບທີ່ເຈົ້າມີ (../cennect_dbstock.php)
include("../cennect.php"); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script>
    <script src="../jquery.js"></script>
    <title>ຈັດການປະເພດຂີ້ເຫຍື້ອ</title>
    <script>
        $(function(){
            $('.delete').on('click', function(e){
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລຶບ?',
                    text: "ຂໍ້ມູນປະເພດຂີ້ເຫຍື້ອຈະຖືກລຶບອອກຈາກລະບົບ!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ລຶບເລີຍ',
                    cancelButtonText: 'ຍົກເລີກ'
                }).then((result) => {
                    if (result.isConfirmed) { document.location.href = href; }
                })
            })
        })
    </script>
</head>
<body style="font-family:'Phetsarath OT';">
<div class="container mt-5">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0"><i class="bi bi-recycle"></i> ລາຍການປະເພດຂີ້ເຫຍື້ອ</h3>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <a href="form_categories.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> ເພີ່ມໃໝ່</a>
                </div>
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="10%">ລຳດັບ</th>
                        <th>ຊື່ (ລາວ)</th>
                        <th>ຊື່ (ENG)</th>
                        <th width="15%">ຣີໄຊເຄິນ</th>
                        <th width="10%">ແກ້ໄຂ</th>
                        <th width="10%">ລຶບ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 2. ປ່ຽນ $conn ເປັນ $connect ໃຫ້ຕົງກັບໄຟລ໌ Config ຂອງເຈົ້າ
                    $sql = mysqli_query($connect, "SELECT * FROM categories ORDER BY id DESC");
                    $i = 1;
                    while($row = mysqli_fetch_array($sql)){
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td class="text-start ps-4"><?= $row['name_la'] ?></td>
                        <td class="text-start ps-4"><?= $row['name_en'] ?></td>
                        <td>
                            <?php if($row['is_recyclable'] == 1): ?>
                                <span class="badge bg-success">ໄດ້</span>
                            <?php else: ?>
                                <span class="badge bg-danger">ບໍ່ໄດ້</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="update_categories.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm shadow-sm">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                        <td>
                            <a href="delete_categories.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm delete shadow-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>