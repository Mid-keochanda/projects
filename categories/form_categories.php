<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>ເພີ່ມປະເພດຂີ້ເຫຍື້ອ</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap');

        body {
            font-family: 'Noto Sans Lao', sans-serif;
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(45deg, #198754, #20c997);
            color: white;
            text-align: center;
            padding: 1.5rem;
            font-weight: 700;
            font-size: 1.2rem;
            border: none;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
        }

        .btn-save {
            background-color: #198754;
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background-color: #157347;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
        }

        .btn-back {
            background-color: transparent;
            color: #6c757d;
            border: none;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .btn-back:hover {
            color: #212529;
        }

        .input-group-text {
            background-color: transparent;
            border-radius: 12px 0 0 12px;
            border-right: none;
            color: #198754;
        }

        .has-icon .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-plus-circle-dotted me-2"></i> ເພີ່ມປະເພດຂີ້ເຫຍື້ອໃໝ່
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small uppercase fw-bold">ຊື່ພາສາລາວ</label>
                        <div class="input-group has-icon">
                            <span class="input-group-text"><i class="bi bi-fonts"></i></span>
                            <input type="text" id="name_la" class="form-control" placeholder="ຕົວຢ່າງ: ຢາງພລາສຕິກ">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small uppercase fw-bold">ຊື່ພາສາອັງກິດ</label>
                        <div class="input-group has-icon">
                            <span class="input-group-text"><i class="bi bi-alphabet"></i></span>
                            <input type="text" id="name_en" class="form-control" placeholder="Example: Plastic">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small uppercase fw-bold">ສະຖານະການຣີໄຊເຄິນ</label>
                        <div class="input-group has-icon">
                            <span class="input-group-text"><i class="bi bi-recycle"></i></span>
                            <select id="is_recyclable" class="form-select text-dark">
                                <option value="1">ຣີໄຊເຄິນໄດ້ (Recyclable)</option>
                                <option value="0">ຣີໄຊເຄິນບໍ່ໄດ້ (Non-Recyclable)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="text-center">
                    <button type="button" id="btn_save" class="btn btn-primary btn-save w-50 mb-3 text-white">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> ບັນທຶກຂໍ້ມູນ
                    </button>
                     </div>
                    
                    <div class="text-center">
                        <a href="select_categories.php" class="btn btn-secondary w-50 mb-3 text-white">
                            <i class="bi bi-arrow-left me-2"></i> ກັບຄືນໜ້າລາຍການ
                        </a>
                    </div>
                </div>
                <div id="show"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $("#btn_save").click(function(){
            var name_la = $("#name_la").val().trim();
            var name_en = $("#name_en").val().trim();
            var is_recyclable = $("#is_recyclable").val();

            if(name_la == "" || name_en == ""){
                Swal.fire({
                    icon: 'warning',
                    title: 'ຂໍ້ມູນບໍ່ຄົບຖ້ວນ',
                    text: 'ກະລຸນາປ້ອນຊື່ປະເພດໃຫ້ຄົບທັງສອງພາສາ',
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'ຕົກລົງ'
                });
            } else {
                $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> ກຳລັງບັນທຶກ...');
                
                $.post("insert_categories.php", {
                    name_la: name_la,
                    name_en: name_en,
                    is_recyclable: is_recyclable
                }, function(output){
                    $("#show").html(output);
                    $("#btn_save").prop('disabled', false).html('<i class="bi bi-cloud-arrow-up-fill me-2"></i> ບັນທຶກຂໍ້ມູນ');
                });
            }
        });
    });
</script>
</body>
</html>