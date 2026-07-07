<?php 
@include("../cennect_dbstock.php"); 
if (!isset($connect)) {
    die("<h2 style='color:red; text-align:center; font-family: Phetsarath OT;'>Error: Connection Failed!</h2>");
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນແຂວງ</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --bg-body: #f8f9fc;
            --text-dark: #2d3436;
        }

        body { 
            font-family: 'Noto Sans Lao', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-dark); 
        }

        /* Custom Header */
        .page-header {
            background: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 15px;
            border: 1px solid #edf2f7;
        }

        .header-title h2 { color: var(--primary-color); font-weight: 500; }

        /* Card & Table */
        .card-table {
            background: #fff;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            overflow: hidden;
        }

        .table thead {
            background-color: #f1f4f9;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            color: #64748b;
            border: none;
        }

        .table tbody tr {
            transition: all 0.2s;
            border-bottom: 1px solid #f1f4f9;
        }

        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.02);
            transform: scale(1.002);
        }

        /* Buttons & Icons */
        .btn-add {
            background: var(--primary-color);
            color: white;
            border-radius: 12px;
            padding: 10px 24px;
            font-weight: 500;
            transition: 0.3s;
            white-space: nowrap;
        }
        .btn-add:hover { background: var(--secondary-color); color: white; transform: translateY(-2px); }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: 0.2s;
            margin: 0 2px;
        }

        .btn-edit-soft { background: #e0e7ff; color: #4361ee; }
        .btn-edit-soft:hover { background: #4361ee; color: #fff; }

        .btn-delete-soft { background: #fee2e2; color: #ef4444; }
        .btn-delete-soft:hover { background: #ef4444; color: #fff; }

        /* Modal Customization */
        .custom-modal { border-radius: 24px; border: none; }
        .form-control-custom {
            border-radius: 12px;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        .form-control-custom:focus {
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            border-color: var(--primary-color);
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
        }
        .search-container .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
    </style>
</head>
<body>

<div class="container py-3">
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="header-title text-center text-md-start">
            <h2 class="mb-0 fs-4"><i class="fas fa-map-marked-alt me-2"></i> ຂໍ້ມູນແຂວງ</h2>
            <p class="text-muted mb-0 small">ຈັດການ ແລະ ຕິດຕາມລາຍຊື່ແຂວງທັງໝົດໃນລະບົບ</p>
        </div>
        
        <div class="search-container flex-grow-1 mx-md-4" style="max-width: 400px; width: 100%;">
            <div class="input-group shadow-sm">
                <span class="input-group-text border-end-0"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="ຄົ້ນຫາ: ຊື່ແຂວງ, ໝາຍເຫດ...">
            </div>
        </div>

        <button class="btn btn-add shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-2"></i> ເພີ່ມແຂວງໃໝ່
        </button>
    </div>

    <div id="show_script"></div>

    <div class="card card-table mt-2">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="80" class="text-center">ລຳດັບ</th>
                        <th class="text-start">ຊື່ແຂວງ</th>
                        <th>ໝາຍເຫດ</th>
                        <th width="150" class="text-center">ຈັດການ</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php 
                    $sql = mysqli_query($connect, "SELECT * FROM provinces ORDER BY pro_id DESC");
                    $i = 1; 
                    while($row = mysqli_fetch_array($sql)){ 
                    ?>
                    <tr>
                        <td class="text-center fw-bold text-muted"><?= $i++; ?></td>
                        <td class="text-start">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                    <i class="fas fa-city"></i>
                                </div>
                                <span class="fw-semibold text-dark"><?= $row["pro_name"]; ?></span>
                            </div>
                        </td>
                        <td class="text-muted small"><?= $row["remark"] ?: '<span class="opacity-50">---</span>'; ?></td>
                        <td class="text-center">
                            <button class="btn-action btn-edit-soft btn-edit-modal" 
                                    data-id="<?= $row['pro_id']; ?>" 
                                    data-name="<?= $row['pro_name']; ?>" 
                                    data-remark="<?= $row['remark']; ?>"
                                    title="ແກ້ໄຂ">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <a href="delete.php?pro_id=<?= $row['pro_id']; ?>" class="btn-action btn-delete-soft btn-delete" title="ລຶບ">
                                <i class="fas fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } 
                    if(mysqli_num_rows($sql) == 0) { ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="fas fa-folder-open fs-3 d-block mb-2 text-black-50"></i>
                            ຍັງບໍ່ມີຂໍ້ມູນແຂວງໃນລະບົບ
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold">✨ ເພີ່ມຂໍ້ມູນແຂວງໃໝ່</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">ຊື່ແຂວງ <span class="text-danger">*</span></label>
                    <input type="text" id="pro_name" class="form-control form-control-custom" placeholder="ປ້ອນຊື່ແຂວງ...">
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold small">ໝາຍເຫດ</label>
                    <textarea id="remark" class="form-control form-control-custom" rows="3" placeholder="ເພີ່ມລາຍລະອຽດເພີ່ມເຕີມ..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius:12px;">ປິດ</button>
                <button type="button" class="btn btn-add shadow-sm" id="btn_save">ບັນທຶກຂໍ້ມູນ</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal border-top border-warning border-5">
            <div class="modal-header border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-warning"><i class="fas fa-edit me-2"></i> ແກ້ໄຂຂໍ້ມູນແຂວງ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_pro_id">
                <div class="mb-3">
                    <label class="form-label fw-bold small">ຊື່ແຂວງ</label>
                    <input type="text" id="edit_pro_name" class="form-control form-control-custom">
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold small">ໝາຍເຫດ</label>
                    <textarea id="edit_remark" class="form-control form-control-custom" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius:12px;">ຍົກເລີກ</button>
                <button type="button" class="btn btn-warning text-white px-4 shadow-sm" id="btn_update" style="border-radius: 12px;">ອັບເດດຂໍ້ມູນ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function(){
        // 🌟 ລະບົບຄົ້ນຫາຂໍ້ມູນ (Real-time Search Filter) 🌟
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase().trim();
            var visibleRows = 0;

            $("#tableBody tr").each(function() {
                if ($(this).attr('id') === 'noDataRow') return; // ຂ້າມແຖວແຈ້ງເຕືອນບໍ່ມີຂໍ້ມູນ

                var text = $(this).text().toLowerCase();
                if (text.indexOf(value) > -1) {
                    $(this).show();
                    visibleRows++;
                } else {
                    $(this).hide();
                }
            });

            // ຖ້າຄົ້ນຫາແລ້ວບໍ່ພົບຂໍ້ມູນ ໃຫ້ສະແດງຂໍ້ຄວາມ
            if (visibleRows === 0) {
                if ($("#searchNoData").length === 0) {
                    $("#tableBody").append('<tr id="searchNoData"><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-search-minus fs-4 d-block mb-2"></i>ບໍ່ພົບຂໍ້ມູນແຂວງທີ່ທ່ານຄົ້ນຫາ</td></tr>');
                }
            } else {
                $("#searchNoData").remove();
            }
        });

        // Insert
        $('#btn_save').click(function(){
            var pro_name = $('#pro_name').val().trim();
            var remark = $('#remark').val().trim();
            if(pro_name == ""){
                Swal.fire({ icon: 'warning', text: 'ກະລຸນາປ້ອນຊື່ແຂວງ', confirmButtonColor: '#4361ee' });
            } else {
                $.get('insert_provinces.php', { pro_name: pro_name, remark: remark }, function(output){
                    $('#show_script').html(output);
                    $('#addModal').modal('hide');
                });
            }
        });

        // Open Edit Modal
        $(document).on('click', '.btn-edit-modal', function(){
            $('#edit_pro_id').val($(this).data('id'));
            $('#edit_pro_name').val($(this).data('name'));
            $('#edit_remark').val($(this).data('remark'));
            $('#editModal').modal('show');
        });

        // Update
        $('#btn_update').click(function(){
            var pro_id = $('#edit_pro_id').val();
            var pro_name = $('#edit_pro_name').val();
            var remark = $('#edit_remark').val();
            $.get('save_update.php', { pro_id: pro_id, pro_name: pro_name, remark: remark }, function(output){
                $('#show_script').html(output);
                $('#editModal').modal('hide');
            });
        });

        // Delete
        $(document).on('click', '.btn-delete', function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: 'ຢືນຢັນການລຶບ?',
                text: "ທ່ານຈະບໍ່ສາມາດກູ້ຄືນຂໍ້ມູນນີ້ໄດ້!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ລຶບອອກທັນທີ',
                cancelButtonText: 'ຍົກເລີກ'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = url; }
            });
        });
    });
</script>
</body>
</html>