<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ແກ້ໄຂຂໍ້ມູນແຂວງ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Phetsarath OT', sans-serif;
        }
        .card {
            border: none;
            border-radius: 20px; /* ຄວາມມົນທີ່ເຈົ້າມັກ */
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); /* ເງົາແບບຟຸ້ງໆ */
            overflow: hidden;
            margin-top: 50px;
        }
        .card-header {
            background: linear-gradient(45deg, #0d6efd, #00c6ff); /* ໄລ່ສີໃຫ້ເບິ່ງທັນສະໄໝ */
            color: white;
            padding: 20px;
            border: none;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-top: 8px;
        }
        .form-control {
            border-radius: 12px;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
            border-color: #0d6efd;
        }
        .btn {
            border-radius: 12px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
        }
    </style>
</head>
<body>

    <?php
        include("../cennect_dbstock.php");
        $pro_id = $_GET['pro_id'];
        $update = mysqli_query($connect, "select*from provinces where pro_id='$pro_id'");
        $show = mysqli_fetch_array($update);
    ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>ແກ້ໄຂຟອມບັນທຶກຂໍ້ມູນ</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="save_update.php" method="GET">
                            <input type="hidden" name="pro_id" value="<?= $show['pro_id'];?>">

                            <div class="mb-3">
                                <label class="form-label">ຊື່ແຂວງ:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="pro_name" class="form-control border-start-0" 
                                           placeholder="ກະລຸນາປ້ອນຊື່ແຂວງ..." value="<?= $show['pro_name'];?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">ໝາຍເຫດ:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-chat-left-text"></i></span>
                                    <input type="text" name="remark" class="form-control border-start-0" 
                                           placeholder="ໝາຍເຫດເພີ່ມເຕີມ..." value="<?= $show['remark'];?>">
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                                <button type="submit" id="send" class="btn btn-success px-4">
                                    <i class="bi bi-floppy-fill me-1"></i> ບັນທຶກ
                                </button>
                                <a href="province_list.php" class="btn btn-danger px-4">
                                    <i class="bi bi-x-circle me-1"></i> ຍົກເລີກ
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>