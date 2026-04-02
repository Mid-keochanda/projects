<?php include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນບ້ານ | Village Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;700&display=swap');
        body { background-color: #f0f4f8; font-family: 'Noto Sans Lao', sans-serif; padding: 10px 0; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); background: white; }
        .main-header { background: white; padding: 15px; border-radius: 20px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .btn-custom { border-radius: 12px; padding: 10px 20px; font-weight: 600; transition: 0.3s; border: none; }
        .btn-add { background: linear-gradient(120deg, #4facfe 0%, #00f2fe 100%); color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="main-header shadow-sm">
        <div>
            <h4 class="fw-bold mb-0 text-primary"><i class="bi bi-house-door-fill me-2"></i>ຈັດການຂໍ້ມູນບ້ານ</h4>
            <p class="text-muted small mb-0">ບໍລິຫານຈັດການຂໍ້ມູນ ບ້ານ, ເມືອງ ແລະ ແຂວງ</p>
        </div>
        <button class="btn btn-add btn-custom" data-bs-toggle="modal" data-bs-target="#villageModal" id="btn_open_add">
            <i class="fas fa-plus-circle me-2"></i> ເພີ່ມບ້ານໃໝ່
        </button>
    </div>

    <div class="card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-center table-light">
                    <tr>
                        <th width="80">ລຳດັບ</th>
                        <th>ແຂວງ</th>
                        <th>ເມືອງ</th>
                        <th>ຊື່ບ້ານ</th>
                        <th>ໝາຍເຫດ</th>
                        <th width="150">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $query = "SELECT a.pro_name, a.pro_id, b.dis_name, b.dis_id, c.* FROM provinces AS a 
                                  INNER JOIN districts AS b ON a.pro_id = b.pro_id 
                                  INNER JOIN villages AS c ON b.dis_id = c.dis_id 
                                  ORDER BY c.vill_id DESC";
                        $sql = mysqli_query($connect, $query);
                        $i = 1;
                        while($form = mysqli_fetch_array($sql)){
                    ?>
                    <tr class="text-center">
                        <td><?= $i++ ?></td>
                        <td><span class="badge bg-info text-dark"><?= $form['pro_name']; ?></span></td>
                        <td><span class="badge bg-success text-white"><?= $form['dis_name']; ?></span></td>
                        <td class="fw-bold"><?= $form['vill_name']; ?></td>
                        <td><?= $form['remark'] ?: '-'; ?></td>
                        <td>
                            <button class="btn btn-light btn-sm edit-btn" 
                                    data-id="<?= $form['vill_id'] ?>"
                                    data-pro="<?= $form['pro_id'] ?>"
                                    data-dis="<?= $form['dis_id'] ?>"
                                    data-name="<?= $form['vill_name'] ?>"
                                    data-remark="<?= $form['remark'] ?>">
                                <i class="bi bi-pencil text-success"></i>
                            </button>
                            <a href="delete_villages.php?vill_id=<?= $form['vill_id'];?>" class="btn btn-light btn-sm delete">
                                <i class="bi bi-trash text-danger"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="villageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" id="modalTitle">ເພີ່ມຂໍ້ມູນບ້ານ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form id="villageForm">
                    <input type="hidden" id="vill_id">
                    <div class="mb-3">
                        <label class="form-label">ເລືອກແຂວງ</label>
                        <select class="form-select" id="pro_id">
                            <option value="">-- ເເລືອກແຂວງ --</option>
                            <?php
                                $select_pro = mysqli_query($connect, "SELECT * FROM provinces");
                                while($p = mysqli_fetch_array($select_pro)){
                                    echo "<option value='".$p['pro_id']."'>".$p['pro_name']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ເລືອກເມືອງ</label>
                        <select class="form-select" id="dis_id">
                            <option value="">-- ເລືອກເມືອງ --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ຊື່ບ້ານ</label>
                        <input type="text" id="vill_name" class="form-control" placeholder="ປ້ອນຊື່ບ້ານ...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ໝາຍເຫດ</label>
                        <textarea id="remark" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light btn-custom flex-grow-1" data-bs-dismiss="modal">ຍົກເລີກ</button>
                <button type="button" class="btn btn-add btn-custom flex-grow-1" id="save_btn">ບັນທຶກຂໍ້ມູນ</button>
            </div>
        </div>
    </div>
</div>

<div id="show"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function(){
        // ດຶງຂໍ້ມູນເມືອງ
        $("#pro_id").change(function(){
            var pro_id = $(this).val();
            $.post("get_idstrict.php", { pro_id: pro_id }, function(output){
                $("#dis_id").html(output);
                // ຖ້າເປັນການແກ້ໄຂ ໃຫ້ເລືອກເມືອງທີ່ຝາກຄ່າໄວ້
                var selected_dis = $("#dis_id").data('selected');
                if(selected_dis) {
                    $("#dis_id").val(selected_dis);
                    $("#dis_id").data('selected', ''); 
                }
            });
        });

        // ເປີດ Modal ເພີ່ມໃໝ່
        $('#btn_open_add').click(function(){
            $('#modalTitle').text('ເພີ່ມຂໍ້ມູນບ້ານ');
            $('#vill_id').val('');
            $('#villageForm')[0].reset();
            $('#dis_id').html('<option value="">-- ເລືອກເມືອງ --</option>');
        });

        // ປຸ່ມແກ້ໄຂ
        $('.edit-btn').click(function(){
            $('#modalTitle').text('ແກ້ໄຂຂໍ້ມູນບ້ານ');
            var id = $(this).data('id');
            var pro = $(this).data('pro');
            var dis = $(this).data('dis');
            var name = $(this).data('name');
            var remark = $(this).data('remark');

            $('#vill_id').val(id);
            $('#pro_id').val(pro).trigger('change');
            $("#dis_id").data('selected', dis); 
            $('#vill_name').val(name);
            $('#remark').val(remark);
            $('#villageModal').modal('show');
        });

        // ປຸ່ມບັນທຶກ (ທັງ Insert ແລະ Update)
$('#save_btn').click(function(){
    var btn = $(this);
    var vill_id = $('#vill_id').val(); // ຖ້າມີ ID ແປວ່າແກ້ໄຂ
    var data = {
        vill_id: $('#vill_id').val(),
        pro_id: $('#pro_id').val(), 
        dis_id: $('#dis_id').val(),
        vill_name: $('#vill_name').val(),
        remark: $('#remark').val()
    };

    if(data.dis_id == "" || data.vill_name == ""){
        Swal.fire('ແຈ້ງເຕືອນ', 'ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບ', 'warning');
        return;
    }

    // ສະແດງ Loading ຢູ່ປຸ່ມ
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    // ເລືອກ URL ຕາມເງື່ອນໄຂ
    var url = (vill_id == "") ? 'insert_villages.php' : 'save_villages.php';

    $.get(url, data, function(res){
        $('#show').html(res);
        btn.prop('disabled', false).html('ບັນທຶກຂໍ້ມູນ');
    }).fail(function() {
        Swal.fire('Error', 'ເຊື່ອມຕໍ່ກັບ Server ບໍ່ໄດ້', 'error');
        btn.prop('disabled', false).html('ບັນທຶກຂໍ້ມູນ');
    });
});

        // ລົບຂໍ້ມູນ
        $(document).on('click', '.delete', function(e){
            e.preventDefault();
            const href = $(this).attr('href');
            Swal.fire({
                title: 'ຢືນຢັນການລົບ?',
                text: "ຂໍ້ມູນຈະຖືກລົບອອກຈາກລະບົບ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ລົບເລີຍ',
                cancelButtonText: 'ຍົກເລີກ'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = href; }
            });
        });
    });
</script>
</body>
</html>