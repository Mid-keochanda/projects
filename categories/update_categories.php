<?php
include("../cennect_dbstock.php");
$id = $_GET['id'];
$sql = mysqli_query($connect, "SELECT * FROM categories WHERE id='$id'");
$row = mysqli_fetch_array($sql);
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>ແກ້ໄຂປະເພດຂີ້ເຫຍື້ອ</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;700&display=swap');

        body {
            font-family: 'Noto Sans Lao', sans-serif;
            background-color: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(45deg, #f6c23e, #f4b619);
            color: #333;
            text-align: center;
            padding: 1.5rem;
            font-weight: 700;
            font-size: 1.2rem;
            border: none;
        }

        .form-label {
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #f6c23e;
            box-shadow: 0 0 0 0.25rem rgba(246, 194, 62, 0.15);
        }

        .input-group-text {
            background-color: #fcfcfc;
            border-radius: 12px 0 0 12px;
            border-right: none;
            color: #f6c23e;
        }

        .has-icon .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .btn-update {
            background-color: #f6c23e;
            color: #333;
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-update:hover {
            background-color: #f4b619;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(246, 194, 62, 0.3);
            color: #000;
        }

        .btn-cancel {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: block;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background-color: #e9ecef;
            color: #333;
        }
    </style>
</head>
<body style="font-family:'Phetsarath OT', sans-serif;">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">ແກ້ໄຂປະເພດຂີ້ເຫຍື້ອ</div>
                <div class="card-body">
                    <input type="hidden" id="id" value="<?= $row['id'] ?>">
                    
                    <label>ຊື່ພາສາລາວ:</label>
                    <input type="text" id="name_la" class="form-control mb-2" value="<?= $row['name_la'] ?>">
                    
                    <label>ຊື່ພາສາອັງກິດ:</label>
                    <input type="text" id="name_en" class="form-control mb-2" value="<?= $row['name_en'] ?>">
                    
                    <label>ຣີໄຊເຄິນໄດ້ບໍ່:</label>
                    <select id="is_recyclable" class="form-control mb-2">
                        <option value="1" <?= $row['is_recyclable'] == 1 ? 'selected' : '' ?>>ໄດ້</option>
                        <option value="0" <?= $row['is_recyclable'] == 0 ? 'selected' : '' ?>>ບໍ່ໄດ້</option>
                    </select>
                     <div class="text-center">
                    <button type="button" id="btn_update" class="btn btn-success w-50 mt-3">ບັນທຶກການແກ້ໄຂ</button>
                    </div> 

                     <div class="text-center">
                    <a href="select_categories.php" class="btn btn-secondary w-50 mt-2">ຍົກເລີກ</a>
                </div>
                </div>
                <div id="show"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $("#btn_update").click(function(){
            var id = $("#id").val();
            var name_la = $("#name_la").val();
            var name_en = $("#name_en").val();
            var is_recyclable = $("#is_recyclable").val();

            if(name_la == "" || name_en == ""){
                Swal.fire('ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບ', '', 'warning');
            } else {
                $.post("save_update_categories.php", {
                    id: id,
                    name_la: name_la,
                    name_en: name_en,
                    is_recyclable: is_recyclable
                }, function(output){
                    $("#show").html(output);
                });
            }
        });
    });
</script>
</body>
</html>