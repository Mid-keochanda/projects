<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນບ້ານ | Village Management</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.9);
            --primary-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        body { 
            background-color: #f0f4f8; 
            font-family: 'Phetsarath OT', sans-serif; 
            padding: 30px 0;
            color: #2d3748;
        }

        .card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            background: var(--glass-bg);
            margin-bottom: 30px;
        }

        .card-header { 
            background: var(--primary-gradient); 
            border-radius: 20px 20px 0 0 !important;
            padding: 20px;
            border-bottom: none;
        }

        .card-header h4 { font-weight: 700; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.1); }

        /* Form Styling */
        .form-label { font-weight: 600; color: #4a5568; font-size: 0.9rem; margin-bottom: 8px; }
        .form-control, .form-select { 
            border-radius: 12px; 
            padding: 11px 15px; 
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus { 
            box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.1);
            border-color: #4facfe;
        }

        /* Button Styling */
        .btn-custom { 
            border-radius: 12px; 
            padding: 12px 25px; 
            font-weight: 600; 
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-save { background: var(--primary-gradient); color: white; border: none; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(79, 172, 254, 0.3); color: white; }

        /* Table Styling */
        .table thead th { 
            background-color: #f8fafc; 
            color: #64748b; 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 0.8rem;
            padding: 15px;
            border-bottom: 2px solid #edf2f7;
        }
        .table tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #edf2f7; }
        
        .badge-pro { background: #e0f2fe; color: #0369a1; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
        .badge-dis { background: #f0fdf4; color: #166534; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; }

        .btn-action {
            width: 38px; height: 38px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; transition: 0.2s;
        }
    </style>

    <script>
        $(function(){
            // Dependent Dropdown: ເລືອກແຂວງແລ້ວດຶງເມືອງ
            $("#pro_id").change(function(){
                var pro_id = $(this).val();
                $.post("get_idstrict.php", { pro_id: pro_id }, function(output){
                    $("#dis_id").html(output);  
                });
            });

            // ບັນທຶກຂໍ້ມູນ
            $('#save').click(function(){
                var pro_id = $('#pro_id').val();
                var dis_id = $('#dis_id').val();
                var vil_name = $('#vil_name').val();
                var remark = $('#remark').val();

                if(dis_id == "" || vil_name == ""){
                    Swal.fire({ icon: 'warning', title: 'ຄຳເຕືອນ', text: 'ກະລຸນາເລືອກເມືອງ ແລະ ປ້ອນຊື່ບ້ານ', confirmButtonColor: '#4facfe' });
                } else {
                    $.get('insert_villages.php', {           
                        pro_id: pro_id,
                        dis_id: dis_id,
                        vil_name: vil_name,
                        remark: remark
                    }, function(output){
                        $('#show').html(output);
                    });
                }
            });

            // SweetAlert Confirm Delete
            $(document).on('click', '.delete', function(e){
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລົບ?',
                    text: "ທ່ານຕ້ອງການລົບຂໍ້ມູນບ້ານນີ້ແທ້ຫຼືບໍ່?",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'ລົບຂໍ້ມູນ',
                    cancelButtonText: 'ຍົກເລີກ'
                }).then((result) => {
                    if (result.isConfirmed) { window.location.href = href; }
                });
            });
        });
    </script>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <h4><i class="bi bi-house-add-fill me-2"></i> ຈັດການຂໍ້ມູນບ້ານ</h4>
        </div>
        <div class="card-body p-4">
            <form id="villageForm">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">ຊື່ແຂວງ</label>
                        <select class="form-select" id="pro_id">
                            <option value="">-- ເລືອກແຂວງ --</option>
                            <?php
                                include("../cennect_dbstock.php");
                                $select_pro = mysqli_query($connect, "SELECT * FROM provinces");
                                while($data = mysqli_fetch_array($select_pro)){
                                    echo "<option value='".$data['pro_id']."'>".$data['pro_name']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">ຊື່ເມືອງ</label>
                        <select class="form-select" id="dis_id">
                            <option value="">-- ເລືອກເມືອງ --</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">ຊື່ບ້ານ</label>
                        <input type="text" id="vil_name" class="form-control" placeholder="ປ້ອນຊື່ບ້ານ...">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">ໝາຍເຫດ</label>
                        <input type="text" id="remark" class="form-control" placeholder="ໝາຍເຫດ...">
                    </div>
                    <div class="col-12 text-center mt-4">
                        <button type="button" class="btn btn-custom btn-save" id="save">
                            <i class="fas fa-save"></i> ບັນທຶກຂໍ້ມູນ
                        </button>
                        <button type="reset" class="btn btn-custom btn-light text-muted ms-2">
                            <i class="fas fa-redo"></i> ລ້າງຟອມ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="text-center">
                        <tr>
                            <th width="80">ລຳດັບ</th>
                            <th>ແຂວງ</th>
                            <th>ເມືອງ</th>
                            <th>ຊື່ບ້ານ</th>
                            <th>ໝາຍເຫດ</th>
                            <th width="180">ຈັດການ</th>
                        </tr>
                    </thead>
                   <tbody>
    <?php
        include("../cennect_dbstock.php");

        // ໃຊ້ INNER JOIN ເພື່ອຄວາມຊັດເຈນ ແລະ ປ້ອງກັນ Error
        $query = "SELECT a.pro_name, b.dis_name, c.vil_id, c.dis_id, c.vil_name, c.remark 
                  FROM provinces AS a 
                  INNER JOIN districts AS b ON a.pro_id = b.pro_id 
                  INNER JOIN villages AS c ON b.dis_id = c.dis_id 
                  ORDER BY c.vil_id DESC";

        $sql = mysqli_query($connect, $query);

        // ກວດສອບວ່າ Query ເຮັດວຽກໄດ້ຫຼືບໍ່
        if (!$sql) {
            // ຖ້າ Query ຜິດ ມັນຈະບອກ Error ອອກມາເລີຍ
            echo "<tr><td colspan='6' class='text-danger text-center'>Query Error: " . mysqli_error($connect) . "</td></tr>";
        } else {
            $i = 1;
            while($form = mysqli_fetch_array($sql)){
    ?>
    <tr class="text-center">
        <td><span class="text-muted fw-bold"><?= $i ?></span></td>
        <td><span class="badge-pro"><?= $form['pro_name']; ?></span></td>
        <td><span class="badge-dis"><?= $form['dis_name']; ?></span></td>
        <td class="fw-bold"><?= $form['vil_name']; ?></td>
        <td class="text-muted small"><?= $form['remark'] ?: '-'; ?></td>
        <td>
            <div class="d-flex justify-content-center gap-2">
                <a href="update_villages.php?vil_id=<?= $form['vil_id'];?>" class="btn btn-outline-success btn-action" title="ແກ້ໄຂ">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <a href="delete_villages.php?vil_id=<?= $form['vil_id'];?>" class="btn btn-outline-danger btn-action delete" title="ລົບ">
                    <i class="bi bi-trash"></i>
                </a>
            </div>
        </td>
    </tr>
    <?php 
            $i++; 
            } // ປິດ while
        } // ປິດ else 
    ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div id="show"></div>
</div>

</body>
</html>