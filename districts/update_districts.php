<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ແກ້ໄຂຂໍ້ມູນເມືອງ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { 
            background-color: #f4f7fe; 
            font-family: 'Phetsarath OT', sans-serif; 
            padding-top: 50px;
        }
        .card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
        }
        .card-header { 
            background: linear-gradient(135deg, #198754 0%, #20c997 100%); 
            color: white; 
            border-radius: 20px 20px 0 0 !important;
            padding: 25px;
        }
        .form-label { font-weight: 600; color: #495057; margin-top: 15px; }
        .form-control, .form-select { 
            border-radius: 12px; 
            padding: 12px; 
            border: 1px solid #dee2e6;
        }
        .form-control:focus { 
            border-color: #198754; 
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1); 
        }
        .btn-update { 
            background-color: #198754; 
            color: white; 
            border-radius: 12px; 
            padding: 12px 30px; 
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-update:hover { background-color: #157347; transform: translateY(-2px); }
        .btn-back { 
            border-radius: 12px; 
            padding: 12px 30px; 
            font-weight: 600;
        }
    </style>

    <script>
        $(function(){
            $('#save').click(function(){
                var dis_id = $('#dis_id').val();
                var pro_id = $('#pro_id').val();
                var dis_name = $('#dis_name').val();
                var remark = $('#remark').val();

                if(pro_id == "" || dis_name == ""){
                    Swal.fire({
                        icon: 'warning',
                        title: 'ຂໍ້ມູນບໍ່ຄົບຖ້ວນ',
                        text: 'ກະລຸນາກວດສອບ ແຂວງ ແລະ ຊື່ເມືອງ',
                        confirmButtonText: 'ຕົກລົງ'
                    });
                } else {
                    $.get('save_update.php', {
                        dis_id: dis_id,
                        pro_id: pro_id,
                        dis_name: dis_name,
                        remark: remark
                    }, function(output){
                        $('#show').html(output);
                    });
                }
            });
        });
    </script>
</head>
<body>

<?php
    include("../cennect_dbstock.php");
    $dis_id = $_GET['dis_id'];
    // ດຶງຂໍ້ມູນເມືອງທີ່ຕ້ອງການແກ້ໄຂ
    $update = mysqli_query($connect, "SELECT a.pro_name, b.* FROM districts AS b, provinces AS a WHERE a.pro_id = b.pro_id AND dis_id = '$dis_id'");
    $show = mysqli_fetch_array($update);
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="mb-0"><i class="bi bi-pencil-square me-2"></i> ແກ້ໄຂຂໍ້ມູນເມືອງ</h3>
                    <p class="mb-0 opacity-75 small mt-1">ກະລຸນາປ່ຽນຂໍ້ມູນທີ່ທ່ານຕ້ອງການແລ້ວກົດບັນທຶກ</p>
                </div>
                <div class="card-body p-4">
                    <form>
                        <input type="hidden" id="dis_id" value="<?= $show['dis_id'];?>">

                        <div class="mb-3">
                            <label class="form-label">ຊື່ແຂວງ</label>
                            <select class="form-select" id="pro_id">
                                <option value="<?= $show['pro_id'];?>"><?= $show['pro_name'];?></option>
                                <?php
                                    $select = mysqli_query($connect, "SELECT * FROM provinces");
                                    while($data = mysqli_fetch_array($select)){
                                        // ບໍ່ໃຫ້ໂຊແຂວງທີ່ຊ້ຳກັບແຂວງປັດຈຸບັນໃນ List
                                        if($data['pro_id'] != $show['pro_id']) {
                                            echo "<option value='".$data['pro_id']."'>".$data['pro_name']."</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ຊື່ເມືອງ</label>
                            <input type="text" id="dis_name" class="form-control" 
                                   placeholder="ກະລຸນາປ້ອນຊື່ເມືອງ" value="<?= $show['dis_name'];?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">ໝາຍເຫດ</label>
                            <input type="text" id="remark" class="form-control" 
                                   placeholder="ກະລຸນາປ້ອນໝາຍເຫດ" value="<?= $show['remark'];?>">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="button" class="btn btn-update" id="save">
                                <i class="fas fa-save me-2"></i> ບັນທຶກການແກ້ໄຂ
                            </button>
                            <a href="form_districts.php" class="btn btn-outline-secondary btn-back">
                                <i class="fas fa-arrow-left me-2"></i> ກັບຄືນ
                            </a>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light border-0 text-center">
                    <div id="show"></div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>