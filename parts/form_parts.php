<?php include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../icon/css/all.min.css">
    <style>body { font-family: 'Phetsarath OT', sans-serif; }</style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8"> <div class="card border-0 shadow p-4">
                <h5 class="mb-4 text-primary fw-bold"><i class="fas fa-plus-circle me-2"></i>ເພີ່ມອາໄຫຼ່ເຂົ້າລະບົບ</h5>
                <form action="insert_parts.php" method="POST">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">ຊື່ອາໄຫຼ່</label>
                            <input type="text" name="part_name" class="form-control" placeholder="ປ້ອນຊື່ອາໄຫຼ່..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ໝວດໝູ່</label>
                            <input type="text" name="category" class="form-control" placeholder="ເຄື່ອງຍົນ, ຢາງ...">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">ຈຳນວນໃນສາງ</label>
                            <input type="number" name="qty_stock" class="form-control" value="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ຈຳນວນຕ່ຳສຸດທີ່ຕ້ອງເຕືອນ</label>
                            <input type="number" name="min_stock" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ຫົວໜ່ວຍ</label>
                            <input type="text" name="unit" class="form-control" placeholder="ໂຕ, ກ່ອງ, ລິດ...">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ລາຄາຕົ້ນທຶນ</label>
                            <input type="number" step="0.01" name="cost_price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ລາຄາຂາຍ</label>
                            <input type="number" step="0.01" name="sale_price" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">ໝາຍເຫດ</label>
                        <textarea name="remark" class="form-control" rows="2" placeholder="ລາຍລະອຽດເພີ່ມເຕີມ..."></textarea>
                    </div>

                    <div class="row g-2">
                        <div class="col-8">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm py-2 fw-bold">ບັນທຶກອາໄຫຼ່</button>
                        </div>
                        <div class="col-4">
                            <a href="select_parts.php" class="btn btn-light w-100 border py-2">ຍົກເລີກ</a>
                        </div>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if(isset($_GET['save']) && $_GET['save'] == 'success'): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'ບັນທຶກສຳເລັດ!',
        text: 'ຂໍ້ມູນອາໄຫຼ່ຖືກເພີ່ມເຂົ້າໃນລະບົບແລ້ວ',
        confirmButtonText: 'ຕົກລົງ',
        timer: 3000 // ໃຫ້ມັນຫາຍໄປເອງພາຍໃນ 3 ວິນາທີ
    });
</script>
<?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>