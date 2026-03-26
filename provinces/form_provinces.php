<?php



// 2. ດຶງໄຟລ໌ເຊື່ອມຕໍ່ (ກວດສອບຊື່ໄຟລ໌ໃຫ້ດີວ່າ cennect ຫຼື connect)
@include("../cennect_dbstock.php"); 

// ກວດສອບຕົວປ່ຽນການເຊື່ອມຕໍ່ ຖ້າບໍ່ມີໃຫ້ຢຸດເຮັດວຽກພ້ອມບອກສາເຫດ
if (!isset($connect)) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2 style='color:red;'>Error: ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນໄດ້!</h2>
            <p>ກະລຸນາກວດເຊັກໄຟລ໌ <b>../cennect_dbstock.php</b> ວ່າຊື່ຖືກຕ້ອງ ຫຼື ວາງຖືກ Folder ບໍ່?</p>
         </div>");
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນແຂວງ | Modern UI</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap');
        
        body { 
            font-family: 'Noto Sans Lao', sans-serif; 
            background-color: #f4f7fe; 
            color: #2d3436;
        }

        /* Modern Card */
        .card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.02);
        }

        .header-section {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: white;
            border-radius: 20px;
            padding: 20px 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(108, 92, 231, 0.2);
        }

        /* Table Styling */
        .table-container {
        background: white;
        border-radius: 15px; /* ຫຼຸດຄວາມມົນລົງໜ້ອຍໜຶ່ງໃຫ້ຮັບກັບຂະໜາດທີ່ນ້ອຍລົງ */
        padding: 15px;       /* ຫຼຸດຈາກ 20px ເຫຼືອ 15px */
        max-width: 900px;    /* ຈຳກັດຄວາມກວ້າງໃຫ້ເບິ່ງພໍດີ */
        margin: auto;        /* ໃຫ້ມັນຢູ່ເຄິ່ງກາງໜ້າຈໍ */
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
        .table thead th {
            background-color: transparent;
            border-bottom: 2px solid #f1f2f6;
            color: #b2bec3;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        .table tbody tr {
            border-bottom: 1px solid #f1f2f6;
            transition: 0.3s;
        }
        .table tbody tr:hover {
            background-color: #f9faff;
        }
        .table td {
            padding: 18px 12px;
            vertical-align: middle;
        }

        /* Form Custom */
        .form-control {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }
        .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1);
            border-color: #6c5ce7;
        }

        .btn-save {
            background-color: #6c5ce7;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            color: white;
        }
        .btn-save:hover { background-color: #5849c4; color: white; }

        .action-icon {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: 0.3s;
            text-decoration: none;
        }
        .icon-edit { background-color: #fff4e5; color: #ff9f43; }
        .icon-delete { background-color: #fff0f0; color: #ea5455; }
        .icon-edit:hover { background-color: #ff9f43; color: white; }
        .icon-delete:hover { background-color: #ea5455; color: white; }
    </style>

    <script>
        $(function(){
            $('#save').click(function(){
                var pro_name = $('#pro_name').val().trim();
                var remark = $('#remark').val().trim();

                if(pro_name == ""){
                    Swal.fire({ icon: 'warning', text: 'ກະລຸນາປ້ອນຊື່ແຂວງ', confirmButtonColor: '#6c5ce7' });
                } else {
                    $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                    $.get('insert_provinces.php', { pro_name: pro_name, remark: remark }, function(output){
                        $('#show_script').html(output);
                        $('#save').prop('disabled', false).html('<i class="fas fa-save me-2"></i>ບັນທຶກ');
                    });
                }
            });

            $(document).on('click', '.btn-delete', function(e){
                e.preventDefault();
                var url = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລຶບ?',
                    text: "ທ່ານຈະບໍ່ສາມາດກູ້ຄືນຂໍ້ມູນນີ້ໄດ້!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea5455',
                    cancelButtonColor: '#b2bec3',
                    confirmButtonText: 'ລຶບອອກ',
                    cancelButtonText: 'ຍົກເລີກ'
                }).then((result) => {
                    if (result.isConfirmed) { window.location.href = url; }
                });
            });
        });
    </script>
</head>
<body>

<div class="container py-5">
    <div class="header-section text-center">
        <h2 class="fw-bold mb-2">ລະບົບຈັດການຂໍ້ມູນແຂວງ</h2>
        <p class="mb-0 opacity-75"> ຂໍ້ມູນແຂວງພາຍໃນລະບົບ</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <h5 class="fw-bold mb-4 text-primary">ເພີ່ມຂໍ້ມູນ</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">ຊື່ແຂວງ</label>
                    <input type="text" id="pro_name" class="form-control" placeholder="ປ້ອນຊື່ແຂວງ...">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">ໝາຍເຫດ</label>
                    <textarea id="remark" class="form-control" rows="3" placeholder="ໝາຍເຫດ..."></textarea>
                </div>
                <button type="button" class="btn btn-save w-100 shadow-sm" id="save">
                    <i class="fas fa-save me-2"></i>ບັນທຶກຂໍ້ມູນ
                </button>
            </div>
        </div>

        <div class="col-lg-8">
            <div id="show_script"></div>
            <div class="table-container shadow-sm">
                <?php
                $sql = mysqli_query($connect, "SELECT * FROM provinces ORDER BY pro_id DESC");
                $count_res = mysqli_query($connect, "SELECT COUNT(pro_id) as total FROM provinces");
                $row_count = mysqli_fetch_array($count_res);
                ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">ລາຍຊື່ແຂວງ</h5>
                    <span class="badge bg-soft-primary text-primary px-3 py-2" style="background: #f0f0ff; border-radius: 10px;">
                        ທັງໝົດ <?= number_format($row_count['total']); ?> ແຂວງ
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th class="text-start">ຊື່ແຂວງ</th>
                                <th>ໝາຍເຫດ</th>
                                <th>ຈັດການ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; while($mid = mysqli_fetch_array($sql)){ ?>
                            <tr class="text-center">
                                <td class="fw-bold text-muted"><?= $i++; ?></td>
                                <td class="text-start fw-bold"><?= $mid["pro_name"]; ?></td>
                                <td class="text-muted small"><?= $mid["remark"] ?: '-'; ?></td>
                                <td>
                                    <a href="update.php?pro_id=<?= $mid['pro_id']; ?>" class="action-icon icon-edit" title="ແກ້ໄຂ">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?pro_id=<?= $mid['pro_id']; ?>" class="action-icon icon-delete btn-delete" title="ລຶບ">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>