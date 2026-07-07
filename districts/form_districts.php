<?php
    include("../cennect_dbstock.php");
    
    // SQL Join ເພື່ອເອົາຂໍ້ມູນເມືອງ ແລະ ແຂວງ
    $sql = mysqli_query($connect, "SELECT a.pro_name, b.pro_id, b.dis_name, b.remark, b.dis_id 
                                   FROM districts AS b 
                                   INNER JOIN provinces AS a ON a.pro_id = b.pro_id 
                                   ORDER BY b.dis_id DESC");
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນເມືອງ</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --bg-body: #f8f9fc;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Noto Sans Lao', sans-serif; 
            color: #2b2d42;
            padding-top: 20px;
        }

        /* Card Styling */
        .card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.04); 
            overflow: hidden;
        }

        .main-header {
            background: white;
            padding: 15px 20px;
            border-radius: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 4px 15px rgba(0,0,0,0.02);
        }

        /* Table Styling */
        .table { margin-bottom: 0; }
        .table thead th { 
            background-color: #fff;
            color: #8d99ae;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            border-bottom: 1px solid #edf2f4;
        }
        .table tbody td { 
            padding: 12px 10px; 
            border-bottom: 1px solid #edf2f4;
            font-size: 15px;
        }
        .table tbody tr:hover { background-color: #fcfdff; }

        /* Button Styling */
        .btn-custom { 
            border-radius: 12px; 
            padding: 10px 20px;
            font-weight: 500; 
            transition: all 0.3s ease;
            border: none;
            white-space: nowrap;
        }
        .btn-primary-custom {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-primary-custom:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
            color: white;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin: 0 3px;
            transition: 0.2s;
            border: none;
        }
        .btn-edit { background: #fff3cd; color: #ffc107; }
        .btn-edit:hover { background: #ffc107; color: #fff; }
        .btn-del { background: #f8d7da; color: #dc3545; }
        .btn-del:hover { background: #dc3545; color: #fff; }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.1);
        }
        .modal-header {
            border-bottom: 1px solid #f1f3f5;
            padding: 25px;
        }
        .form-label { font-weight: 600; color: #4a4e69; margin-bottom: 8px; font-size: 14px; }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #dee2e6;
            background-color: #fbfbfb;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            border-color: var(--primary-color);
            background-color: #fff;
        }

        .badge-id {
            background: #f1f3f5;
            color: #6c757d;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        /* Search Box */
        .search-container .input-group-text {
            border-radius: 12px 0 0 12px;
            background-color: #fff;
            border-right: none;
            color: #94a3b8;
        }
        .search-container .form-control {
            border-radius: 0 12px 12px 0;
            border-left: none;
            padding: 10px 15px;
            background-color: #fff;
        }
        .search-container .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
    </style>
</head>
<body>

<div class="container pb-4">
    <div class="main-header flex-column flex-md-row gap-3">
        <div class="text-center text-md-start">
            <h4 class="fw-bold mb-1"><i class="fas fa-layer-group text-primary me-2"></i>ຈັດການຂໍ້ມູນເມືອງ</h4>
            <p class="text-muted small mb-0">ທັງໝົດແຂວງ ແລະ ເມືອງທີ່ມີໃນລະບົບ</p>
        </div>
        
        <div class="search-container flex-grow-1 mx-md-4" style="max-width: 450px; width: 100%;">
            <div class="input-group shadow-sm">
                <span class="input-group-text border-end-0"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="ຄົ້ນຫາ: ຊື່ແຂວງ, ຊື່ເມືອງ, ໝາຍເຫດ...">
            </div>
        </div>

        <button class="btn btn-primary-custom btn-custom shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-2"></i> ເພີ່ມເມືອງໃໝ່
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th width="80" class="text-center">ລຳດັບ</th>
                        <th>ຊື່ແຂວງ</th>
                        <th>ຊື່ເມືອງ</th>
                        <th>ໝາຍເຫດ</th>
                        <th width="120" class="text-center">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php 
                    $i = 1; 
                    if(mysqli_num_rows($sql) > 0) {
                        while($form = mysqli_fetch_array($sql)){ 
                    ?>
                    <tr>
                        <td class="text-center"><span class="badge-id"><?= str_pad($i++, 2, "0", STR_PAD_LEFT) ?></span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="fas fa-map-marker-alt fa-sm"></i>
                                </div>
                                <span class="fw-bold"><?= $form['pro_name']; ?></span>
                            </div>
                        </td>
                        <td class="fw-medium text-dark"><?= $form['dis_name']; ?></td>
                        <td><span class="text-muted small"><?= $form['remark'] ?: '-'; ?></span></td>
                        <td class="text-center">
                            <button class="action-btn btn-edit edit-btn" 
                                    data-id="<?= $form['dis_id'];?>" 
                                    data-name="<?= $form['dis_name'];?>" 
                                    data-pro="<?= $form['pro_id'];?>" 
                                    data-remark="<?= $form['remark'];?>"
                                    title="ແກ້ໄຂ">
                                <i class="fas fa-pen fa-sm"></i>
                            </button>
                            <a href="delete_districts.php?dis_id=<?= $form['dis_id'];?>" class="action-btn btn-del delete" title="ລົບ">
                                <i class="fas fa-trash fa-sm"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } 
                    } else { ?>
                    <tr id="noDataRow">
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-folder-open fs-3 d-block mb-2 text-black-50"></i>
                            ຍັງບໍ່ມີຂໍ້ມູນເມືອງໃນລະບົບ
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="show"></div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle text-primary me-2"></i> ເພີ່ມຂໍ້ມູນເມືອງ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label">ເລືອກແຂວງ <span class="text-danger">*</span></label>
                    <select class="form-select" id="pro_id">
                        <option value="">-- ເລືອກແຂວງ --</option>
                        <?php
                            $select_pro = mysqli_query($connect, "SELECT * FROM provinces");
                            while($data = mysqli_fetch_array($select_pro)){
                                echo "<option value='".$data['pro_id']."'>".$data['pro_name']."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ຊື່ເມືອງ <span class="text-danger">*</span></label>
                    <input type="text" id="dis_name" class="form-control" placeholder="ຕົວຢ່າງ: ເມືອງໄຊທານີ">
                </div>
                <div class="mb-0">
                    <label class="form-label">ໝາຍເຫດ</label>
                    <textarea id="remark" class="form-control" rows="2" placeholder="ເພີ່ມລາຍລະອຽດເພີ່ມເຕີມ..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light btn-custom flex-grow-1" data-bs-dismiss="modal">ຍົກເລີກ</button>
                <button type="button" class="btn btn-primary-custom btn-custom flex-grow-1" id="save">ບັນທຶກ</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit text-warning me-2"></i> ແກ້ໄຂຂໍ້ມູນເມືອງ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_dis_id">
                <div class="mb-3">
                    <label class="form-label">ເລືອກແຂວງ <span class="text-danger">*</span></label>
                    <select class="form-select" id="edit_pro_id">
                        <?php
                            $select_pro2 = mysqli_query($connect, "SELECT * FROM provinces");
                            while($p = mysqli_fetch_array($select_pro2)){
                                echo "<option value='".$p['pro_id']."'>".$p['pro_name']."</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ຊື່ເມືອງ <span class="text-danger">*</span></label>
                    <input type="text" id="edit_dis_name" class="form-control">
                </div>
                <div class="mb-0">
                    <label class="form-label">ໝາຍເຫດ</label>
                    <textarea id="edit_remark" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light btn-custom flex-grow-1" data-bs-dismiss="modal">ປິດ</button>
                <button type="button" class="btn btn-warning text-white btn-custom flex-grow-1" id="btn_update">ອັບເດດ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function(){
        // 🌟 ລະບົບຄົ້ນຫາຂໍ້ມູນ (Real-time Search Filter) 🌟
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase().trim();
            var visibleRows = 0;

            $("#tableBody tr").each(function() {
                // ຂ້າມແຖວແຈ້ງເຕືອນຖ້າບໍ່ມີຂໍ້ມູນ
                if ($(this).attr('id') === 'noDataRow' || $(this).attr('id') === 'searchNoData') return;

                var text = $(this).text().toLowerCase();
                if (text.indexOf(value) > -1) {
                    $(this).show();
                    visibleRows++;
                } else {
                    $(this).hide();
                }
            });

            // ຖ້າຄົ້ນຫາແລ້ວບໍ່ພົບຂໍ້ມູນເລີຍ ໃຫ້ສະແດງຂໍ້ຄວາມ
            if (visibleRows === 0) {
                if ($("#searchNoData").length === 0) {
                    $("#tableBody").append('<tr id="searchNoData"><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-search-minus fs-4 d-block mb-2"></i>ບໍ່ພົບຂໍ້ມູນທີ່ທ່ານຄົ້ນຫາ</td></tr>');
                }
            } else {
                $("#searchNoData").remove();
            }
        });

        // 1. Insert
        $('#save').click(function(){
            var pro_id = $('#pro_id').val();
            var dis_name = $('#dis_name').val().trim();
            var remark = $('#remark').val().trim();

            if(pro_id == "" || dis_name == ""){
                Swal.fire({ icon: 'error', title: 'ຜິດພາດ!', text: 'ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ', confirmButtonColor: '#4361ee' });
                return;
            }

            $.get('insert_districts.php', { pro_id: pro_id, dis_name: dis_name, remark: remark }, function(out){
                $('#show').html(out);
                $('#addModal').modal('hide');
            });
        });

        // 2. Open Edit Modal
        $(document).on('click', '.edit-btn', function(){
            $('#edit_dis_id').val($(this).data('id'));
            $('#edit_pro_id').val($(this).data('pro'));
            $('#edit_dis_name').val($(this).data('name'));
            $('#edit_remark').val($(this).data('remark'));
            $('#editModal').modal('show');
        });

        // 3. Update
        $('#btn_update').click(function(){
            var dis_id = $('#edit_dis_id').val();
            var pro_id = $('#edit_pro_id').val();
            var dis_name = $('#edit_dis_name').val().trim();
            var remark = $('#edit_remark').val().trim();

            if(pro_id == "" || dis_name == ""){
                Swal.fire({ icon: 'error', title: 'ຜິດພາດ!', text: 'ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ', confirmButtonColor: '#ffc107' });
                return;
            }

            $.get('save_update.php', { dis_id: dis_id, pro_id: pro_id, dis_name: dis_name, remark: remark }, function(out){
                $('#show').html(out);
                $('#editModal').modal('hide');
            });
        });

        // 4. Delete
        $(document).on('click', '.delete', function(e){
            e.preventDefault();
            const href = $(this).attr('href');
            Swal.fire({
                title: 'ຢືນຢັນການລົບ?',
                text: "ທ່ານຈະບໍ່ສາມາດກູ້ຂໍ້ມູນນີ້ຄືນໄດ້!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ລົບອອກ',
                cancelButtonText: 'ຍົກເລີກ',
                borderRadius: '15px'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = href; }
            });
        });
    });
</script>
</body>
</html>