<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນບ້ານ | Red Theme Edition</title>
    
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../icon/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script> 
    <script src="../jquery.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');

        :root {
            --red-primary: #dc3545;
            --red-gradient: linear-gradient(135deg, #870000 0%, #dc3545 100%);
            --red-light: #fff5f5;
            --gray-soft: #f8f9fa;
        }

        body { 
            background: #fdf2f2; /* ພື້ນຫຼັງສີແດງອ່ອນໆໆ */
            font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif;
            color: #444;
        }

        /* Card Styling */
        .main-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(220, 53, 69, 0.15);
            overflow: hidden;
            background: white;
        }

        .card-header-red {
            background: var(--red-gradient);
            padding: 30px;
            border: none;
        }

        .card-header-red h3 {
            color: white;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            letter-spacing: 1px;
        }

        /* Input Styling */
        .form-label {
            font-weight: 600;
            color: #b91c1c; /* ສີແດງເຂັ້ມ */
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control, .form-select {
            border-radius: 15px;
            padding: 12px 18px;
            border: 1px solid #fecaca;
            background-color: var(--gray-soft);
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--red-primary);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15);
            background-color: #fff;
        }

        /* Button Styling */
        .btn-red {
            border-radius: 15px;
            padding: 12px 35px;
            font-weight: 700;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--red-gradient);
            color: white;
        }

        .btn-red:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(135, 0, 0, 0.3);
            color: white;
        }

        .btn-light-custom {
            border-radius: 15px;
            padding: 12px 35px;
            font-weight: 600;
            background: #f3f4f6;
            color: #4b5563;
            border: none;
        }

        .btn-light-custom:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .icon-badge {
            background: rgba(255,255,255,0.2);
            width: 45px;
            height: 45px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin-right: 15px;
        }
    </style>

    <script>
        $(function(){
            // ເລືອກແຂວງແລ້ວດຶງເມືອງ
            $("#pro_id").change(function(){
                var pro_id = $(this).val();
                $.post("get_idstrict.php", { pro_id: pro_id }, function(output){
                    $("#dis_id").html(output);
                });
            });

            // ບັນທຶກຂໍ້ມູນ
            $('#save').click(function(){
                var pro_id = $('#pro_id').val();
                var vil_id = $('#vil_id').val();
                var dis_id = $('#dis_id').val();
                var vil_name = $('#vil_name').val();
                var remark = $('#remark').val();

                if(dis_id == "" || vil_name == ""){
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'ຂໍ້ມູນບໍ່ຄົບ!',
                        text: 'ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນກ່ອນບັນທຶກ',
                        confirmButtonColor: '#dc3545'
                    });
                } else {
                    $.get('save_villages.php', {           
                        pro_id: pro_id,
                        vil_id: vil_id,
                        dis_id: dis_id,
                        vil_name: vil_name,
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
    
    $vil_id_edit = isset($_GET['vil_id']) ? $_GET['vil_id'] : '';
    $data = ['pro_id'=>'','pro_name'=>'-- ເລືອກແຂວງ --','dis_id'=>'','dis_name'=>'-- ເລືອກເມືອງ --','vil_id'=>'','vil_name'=>'','remark'=>''];

    if($vil_id_edit != ""){
        $select_edit = mysqli_query($connect, "SELECT a.pro_name, a.pro_id, b.dis_name, b.dis_id, c.vil_id, c.vil_name, c.remark 
            FROM provinces AS a 
            INNER JOIN districts AS b ON a.pro_id = b.pro_id 
            INNER JOIN villages AS c ON b.dis_id = c.dis_id 
            WHERE c.vil_id='$vil_id_edit'");
        if(mysqli_num_rows($select_edit) > 0) {
            $data = mysqli_fetch_array($select_edit);
        }
    }
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="card main-card">
                <div class="card-header-red text-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="icon-badge"><i class="fas fa-map-marked-alt text-white fs-4"></i></span>
                        <h3>ຈັດການຂໍ້ມູນບ້ານ</h3>
                    </div>
                    <p class="text-white-50 mb-0 mt-2">ຈັດການຂໍ້ມູນ ບ້ານ, ເມືອງ ແລະ ແຂວງ ໃນລະບົບຂອງທ່ານ</p>
                </div>
                
                <div class="card-body p-5">
                    <form id="villageForm">
                        <input type="hidden" id="vil_id" value="<?= $data['vil_id'];?>">
                        
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label"><i class="fas fa-layer-group me-2"></i>ຊື່ແຂວງ</label>
                                <select class="form-select" id="pro_id">
                                    <option value="<?= $data['pro_id'];?>"><?= $data['pro_name'];?></option>
                                    <?php
                                        $select_pro = mysqli_query($connect, "SELECT * FROM provinces");
                                        while($a = mysqli_fetch_array($select_pro)){ 
                                            echo "<option value='".$a['pro_id']."'>".$a['pro_name']."</option>";
                                        }
                                    ?>
                                </select>
                            </div>

                          <div class="col-md-6 col-lg-3">
    <label class="form-label d-block"><i class="fas fa-directions me-2"></i>ຊື່ເມືອງ</label>
    <select class="form-select shadow-sm" id="dis_id" style="width: 100%;"> 
        <option value="<?= $data['dis_id'];?>"><?= $data['dis_name'];?></option>
    </select>
</div>

                            <div class="col-md-6 col-lg-3">
                                <label class="form-label"><i class="fas fa-home me-2"></i>ຊື່ບ້ານ</label>
                                <input type="text" id="vil_name" class="form-control" placeholder="ປ້ອນຊື່ບ້ານ..." value="<?= $data['vil_name'];?>">
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label class="form-label"><i class="fas fa-pen-nib me-2"></i>ໝາຍເຫດ</label>
                                <input type="text" id="remark" class="form-control" placeholder="..." value="<?= $data['remark'];?>">
                            </div>

                            <div class="col-12 text-center mt-5">
                                <button type="button" class="btn btn-red" id="save">
                                    <i class="fas fa-check-circle"></i> ບັນທຶກຂໍ້ມູນ
                                </button>
                                <button type="reset" class="btn btn-light-custom ms-2" onclick="window.location.href='form_villages.php'">
                                    <i class="fas fa-undo"></i> ຍົກເລີກ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="show" class="mt-4"></div>
        </div>
    </div>
</div>

</body>
</html>