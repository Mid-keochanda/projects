<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script>
    <script src="../jquery.js"></script>
</head>
<body style="font-family:'Phetsarath OT';">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">ເພີ່ມປະເພດຂີ້ເຫຍື້ອ</div>
                <div class="card-body">
                    <label>ຊື່ພາສາລາວ:</label>
                    <input type="text" id="name_la" class="form-control mb-2">
                    <label>ຊື່ພາສາອັງກິດ:</label>
                    <input type="text" id="name_en" class="form-control mb-2">
                    <label>ຣີໄຊເຄິນໄດ້ບໍ່:</label>
                    <select id="is_recyclable" class="form-control mb-2">
                        <option value="1">ໄດ້</option>
                        <option value="0">ບໍ່ໄດ້</option>
                    </select>
                    <button type="button" id="btn_save" class="btn btn-primary w-100 mt-3">ບັນທຶກ</button>
                    <a href="select_categories.php" class="btn btn-secondary w-100 mt-2">ກັບຄືນ</a>
                </div>
                <div id="show"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $("#btn_save").click(function(){
            var name_la = $("#name_la").val();
            var name_en = $("#name_en").val();
            var is_recyclable = $("#is_recyclable").val();

            if(name_la == "" || name_en == ""){
                Swal.fire('ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບ', '', 'warning');
            } else {
                $.post("insert_categories.php", {
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