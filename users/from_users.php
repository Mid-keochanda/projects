<?php
session_start();
include("../cennect_dbstock.php");
if(@$_SESSION['checked'] <> 1){
    echo "<script>alert('ກະລຸນາລົງຊື່ເຂົ້າໃຊ້ກ່ອນ'); location='../index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນຜູ້ນຳໃຊ້ | Garage Management</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script>
    <script src="../jquery.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap');
        :root { --primary-bg: #f4f7fe; --brand-color: #4318ff; --brand-gradient: linear-gradient(135deg, #4318ff 0%, #868cff 100%); --text-main: #2b3674; }
        body { background-color: var(--primary-bg); font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif; color: var(--text-main); }
        .main-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .search-box { background: white; border-radius: 50px; padding: 5px 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); display: flex; align-items: center; max-width: 400px; }
        .search-input { border: none; background: transparent; padding: 8px 15px; width: 100%; outline: none; }
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .table-custom tbody tr { background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.02); transition: 0.3s; cursor: pointer; }
        .table-custom tbody tr:hover { transform: scale(1.01); box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
        .table-custom td { padding: 15px; vertical-align: middle; border: none; }
        .avatar { width: 40px; height: 40px; border-radius: 12px; background: #eef2ff; color: var(--brand-color); display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .badge-status { padding: 6px 12px; border-radius: 50px; font-size: 11px; font-weight: 700; }
        .status-admin { background: rgba(67, 24, 255, 0.1); color: var(--brand-color); }
        .status-user { background: #f4f7fe; color: #707eae; }
        .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: none; transition: 0.2s; text-decoration: none; }
        .btn-edit { background: #eef2ff; color: var(--brand-color); }
        .btn-del { background: #ffeeee; color: #ee5d50; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold mb-1 text-primary">ຈັດການຂໍ້ມູນຜູ້ນຳໃຊ້</h2>
            <p class="text-muted">ລະບົບຈັດການຂໍ້ມູນພະນັກງານ ແລະ ຜູ້ດູແລ</p>
        </div>
        <div class="col-md-6 d-flex justify-content-md-end gap-2">
            <div class="search-box">
                <i class="bi bi-search text-muted"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="ຄົ້ນຫາຊື່ ຫຼື Username...">
            </div>
            <button class="btn btn-primary rounded-pill px-4" style="background: var(--brand-gradient); border: none;" data-bs-toggle="modal" data-bs-target="#userModal">
                <i class="bi bi-plus-lg me-2"></i> ເພີ່ມໃໝ່
            </button>
        </div>
    </div>

    <div class="card main-card shadow-sm">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table-custom text-center" id="userTable">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>#</th>
                            <th class="text-start">ຊື່ ແລະ ນາມສະກຸນ</th>
                            <th>ເພດ</th>
                            <th>ເບີໂທ</th>
                            <th>ບ້ານ</th>
                            <th>ເມືອງ</th>
                            <th>ແຂວງ</th>
                            <th>ສະຖານະ</th>
                            <th>ຜູ້ນຳໃຊ້</th>
                            <th>ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$i = 1; 
$sql = mysqli_query($connect, "SELECT u.*, v.vill_name, d.dis_name, p.pro_name 
                               FROM users AS u 
                               LEFT JOIN villages AS v ON u.vill_id = v.vill_id 
                               LEFT JOIN districts AS d ON v.dis_id = d.dis_id 
                               LEFT JOIN provinces AS p ON d.pro_id = p.pro_id 
                               ORDER BY u.user_id DESC"); 
while($show = mysqli_fetch_array($sql)){
    $statusClass = ($show['status'] == 'admin' || $show['status'] == 'ຜູ້ບໍລິຫານ') ? 'status-admin' : 'status-user';
?>
<tr class="user-row">
    <td><?= $i++; ?></td>
    <td class="text-start">
        <div class="d-flex align-items-center">
            <div class="avatar me-3"><?= mb_substr($show['fname'], 0, 1, 'UTF-8'); ?></div>
            <div>
                <div class="fw-bold"><?= $show['fname'];?> <?= $show['lname'];?></div>
                <div class="small text-muted"><?= $show['dob'];?></div>
            </div>
        </div>
    </td>
    <td><?= $show['gender'];?></td>
    <td><?= $show['tel'];?></td>
    <td><?= $show['vill_name'];?></td>
    <td><?= $show['dis_name'];?></td>
    <td><?= $show['pro_name'];?></td>
    <td><span class="badge-status <?= $statusClass; ?>"><?= $show['status'];?></span></td>
    <td><code><?= $show['username'];?></code></td>
    <td>
        <div class="d-flex justify-content-center gap-2">
          <a href="update_users.php?user_id=<?= $show['user_id'];?>" class="btn btn-action btn-edit">
    <i class="fas fa-edit"></i>
</a>
            <a href="delete_users.php?user_id=<?= $show['user_id'];?>" class="btn btn-action btn-del delete-btn">
                <i class="fas fa-trash-alt"></i>
            </a>
        </div>
    </td>
</tr>
<?php } ?>

<script>
    $(function(){
        // ສ່ວນການລົບຂໍ້ມູນ
        $('.delete-btn').on('click', function(e){
            e.preventDefault();
            const href = $(this).attr('href');
            
            Swal.fire({
                title: 'ຢືນຢັນການລົບ?',
                text: "ຂໍ້ມູນນີ້ຈະຖືກລົບອອກຈາກລະບົບຖາວອນ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4318ff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ຢືນຢັນ',
                cancelButtonText: 'ຍົກເລີກ',
                border: 'none',
                borderRadius: '20px'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        // ກວດເຊັກ Query String ວ່າລົບສຳເລັດຫຼືບໍ່ (ຖ້າສົ່ງກັບມາຈາກ delete_users.php)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('msg') && urlParams.get('msg') === 'deleted') {
            Swal.fire({
                icon: 'success',
                title: 'ລົບຂໍ້ມູນສຳເລັດ!',
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
</script>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 text-white" style="background: var(--brand-gradient); border-radius: 20px 20px 0 0; padding: 1.5rem;">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i> ເພີ່ມຜູ້ໃຊ້ໃໝ່</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="userForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-white rounded-4 shadow-sm h-100">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">ຂໍ້ມູນສ່ວນຕົວ</h6>
                                <label class="small fw-bold">ຊື່:</label>
                                <input type="text" id="fname" class="form-control mb-2" placeholder="ປ້ອນຊື່">
                                <label class="small fw-bold">ນາມສະກຸນ:</label>
                                <input type="text" id="lname" class="form-control mb-2" placeholder="ປ້ອນນາມສະກຸນ">
                                <label class="small fw-bold d-block">ເພດ:</label>
                                <div class="btn-group w-100 mb-2">
                                    <input type="radio" class="btn-check" name="gender" id="genderM" value="ຊາຍ" checked>
                                    <label class="btn btn-outline-primary" for="genderM">ຊາຍ</label>
                                    <input type="radio" class="btn-check" name="gender" id="genderF" value="ຍິງ">
                                    <label class="btn btn-outline-danger" for="genderF">ຍິງ</label>
                                </div>
                                <label class="small fw-bold">ວັນເກີດ:</label>
                                <input type="date" id="dob" class="form-control mb-2">
                                <label class="small fw-bold">ເບີໂທ:</label>
                                <input type="text" id="tel" class="form-control" placeholder="020...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-white rounded-4 shadow-sm h-100">
                                <h6 class="text-info fw-bold mb-3 border-bottom pb-2">ທີ່ຢູ່ & ສະຖານະ</h6>
                                <label class="small fw-bold">ແຂວງ:</label>
                                <select class="form-select mb-2" id="pro_id">
                                    <option value="">-- ເລືອກແຂວງ --</option>
                                    <?php
                                    $res = mysqli_query($connect, "SELECT * FROM provinces");
                                    while($row = mysqli_fetch_array($res)){ echo "<option value='".$row['pro_id']."'>".$row['pro_name']."</option>"; }
                                    ?>
                                </select>
                                <label class="small fw-bold">ເມືອງ:</label>
                                <select class="form-select mb-2" id="dis_id"><option value="">-- ເລືອກເມືອງ --</option></select>
                                <label class="small fw-bold">ບ້ານ:</label>
                                <select class="form-select mb-2" id="vill_id"><option value="">-- ເລືອກບ້ານ --</option></select>
                                <label class="small fw-bold">ສະຖານະ:</label>
                                <select class="form-select" id="status">
                                    <option value="ພະນັກງານ">ພະນັກງານ</option>
                                    <option value="ຜູ້ບໍລິຫານ">ຜູ້ບໍລິຫານ</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-white rounded-4 shadow-sm h-100">
                                <h6 class="text-danger fw-bold mb-3 border-bottom pb-2">ຂໍ້ມູນເຂົ້າລະບົບ</h6>
                                <label class="small fw-bold">ຜູ້ນຳໃຊ້:</label>
                                <input type="text" id="username" class="form-control mb-2" placeholder="User123">
                                <label class="small fw-bold">ລະຫັດຜ່ານ:</label>
                                <div class="input-group mb-2">
                                    <input type="password" id="password" class="form-control" placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password')"><i class="bi bi-eye"></i></button>
                                </div>
                                <label class="small fw-bold">ຢືນຢັນລະຫັດ:</label>
                                <div class="input-group mb-2">
                                    <input type="password" id="confirm_password" class="form-control" placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('confirm_password')"><i class="bi bi-eye"></i></button>
                                </div>
                                <label class="small fw-bold">ໝາຍເຫດ:</label>
                                <textarea id="remark" class="form-control" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 bg-light p-3" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ຍົກເລີກ</button>
                <button type="button" id="btnSave" class="btn btn-primary rounded-pill px-4" style="background: var(--brand-gradient); border: none;">ບັນທຶກຂໍ້ມູນ</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePass(id) {
        const x = document.getElementById(id);
        x.type = (x.type === "password") ? "text" : "password";
    }

    $(document).ready(function() {
        // Search Function
        $('#searchInput').on('keyup', function() {
            let val = $(this).val().toLowerCase();
            $('.user-row').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
            });
        });

        // Dynamic Address Selects
        $('#pro_id').change(function() {
            $.post('insert_user_name.php', { pro_id: $(this).val() }, function(data) {
                $('#dis_id').html(data);
                $('#vill_id').html('<option value="">-- ເລືອກບ້ານ --</option>');
            });
        });

        $('#dis_id').change(function() {
            $.post('insert_vill_name.php', { dis_id: $(this).val() }, function(data) {
                $('#vill_id').html(data);
            });
        });

        // Save Function
        $('#btnSave').click(function() {
            const data = {
                fname: $('#fname').val(),
                lname: $('#lname').val(),
                gender: $("input[name='gender']:checked").val(),
                dob: $('#dob').val(),
                tel: $('#tel').val(),
                pro_id: $('#pro_id').val(),
                dis_id: $('#dis_id').val(),
                vill_id: $('#vill_id').val(),
                status: $('#status').val(),
                username: $('#username').val(),
                password: $('#password').val(),
                confirm_password: $('#confirm_password').val(),
                remark: $('#remark').val()
            };

            if(!data.fname || !data.username || !data.password) {
                Swal.fire('ແຈ້ງເຕືອນ', 'ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບ', 'warning');
                return;
            }
            if(data.password !== data.confirm_password) {
                Swal.fire('ຜິດພາດ', 'ລະຫັດຜ່ານບໍ່ກົງກັນ', 'error');
                return;
            }

            $.post('insert_users.php', data, function(res) {
                if(res.trim() === 'success') {
                    Swal.fire({ icon: 'success', title: 'ບັນທຶກສຳເລັດ', showConfirmButton: false, timer: 1500 })
                    .then(() => location.reload());
                } else {
                    Swal.fire('ຜິດພາດ', res, 'error');
                }
            });
        });
    });
</script>
</body>
</html>