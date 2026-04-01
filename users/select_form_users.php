<?php
session_start();
// ກວດສອບການ Login
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
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-bg: #f4f7fe;
            --brand-color: #4318ff;
            --brand-gradient: linear-gradient(135deg, #4318ff 0%, #868cff 100%);
            --text-main: #2b3674;
            --text-muted: #a3aed1;
        }

        body {
            background-color: var(--primary-bg);
            font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif;
            color: var(--text-main);
        }

        .main-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: white;
        }

        .header-section {
            background: var(--brand-gradient);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
        }

        /* Search Box Custom */
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 5px 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            max-width: 400px;
        }

        .search-input {
            border: none;
            background: transparent;
            padding: 8px 15px;
            width: 100%;
            outline: none;
        }

        /* Table Design */
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .table-custom thead th {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            border: none;
            padding-bottom: 15px;
        }

        .table-custom tbody tr {
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            transition: 0.3s;
        }

        .table-custom tbody tr:hover {
            transform: scale(1.01);
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .table-custom td {
            padding: 15px;
            vertical-align: middle;
            border: none;
        }

        .table-custom td:first-child { border-radius: 12px 0 0 12px; }
        .table-custom td:last-child { border-radius: 0 12px 12px 0; }

        /* Avatar */
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #eef2ff;
            color: var(--brand-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Status Badge */
        .badge-status {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
        }
        .status-admin { background: rgba(67, 24, 255, 0.1); color: var(--brand-color); }
        .status-user { background: #f4f7fe; color: #707eae; }

        /* Action Buttons */
        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: 0.2s;
        }
        .btn-edit { background: #eef2ff; color: var(--brand-color); }
        .btn-edit:hover { background: var(--brand-color); color: white; }
        .btn-del { background: #ffeeee; color: #ee5d50; }
        .btn-del:hover { background: #ee5d50; color: white; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold mb-1">ລາຍຊື່ຜູ້ນຳໃຊ້</h2>
            <p class="text-muted">ລະບົບຈັດການຂໍ້ມູນພະນັກງານ ແລະ ຜູ້ດູແລ</p>
        </div>
        <div class="col-md-6 d-flex justify-content-md-end">
            <div class="search-box me-3">
                <i class="bi bi-search text-muted"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="ຄົ້ນຫາຊື່ ຫຼື Username...">
            </div>
            <a href="form_users.php" class="btn btn-primary rounded-pill px-4" style="background: var(--brand-gradient); border: none;">
                <i class="bi bi-plus-lg me-2"></i> ເພີ່ມໃໝ່
            </a>
        </div>
    </div>

    <div class="card main-card">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table-custom text-center" id="userTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-start">ຊື່ ແລະ ນາມສະກຸນ</th>
                            <th>ເພດ</th>
                            <th>ເບີໂທ</th>
                            <th class="text-start">ບ້ານ</th>
                            <th class="text-start">ເມືອງ</th>
                            <th class="text-start">ແຂວງ</th>
                            <th>ສະຖານະ</th>
                            <th>Username</th>
                            <th>ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
    include("../cennect_dbstock.php");
    
    $i = 1; 

    // Query ທີ່ດຶງຂໍ້ມູນຄົບທັງ ບ້ານ, ເມືອງ, ແຂວງ
    $sql_text = "SELECT u.*, v.vill_name, d.dis_name, p.pro_name 
                 FROM users AS u 
                 LEFT JOIN villages AS v ON u.vill_id = v.vill_id 
                 LEFT JOIN districts AS d ON v.dis_id = d.dis_id 
                 LEFT JOIN provinces AS p ON d.pro_id = p.pro_id 
                 ORDER BY u.user_id DESC";
                 
    $query = mysqli_query($connect, $sql_text) or die(mysqli_error($connect)); 

    while($show = mysqli_fetch_array($query)){
        $statusClass = ($show['status'] == 'admin') ? 'status-admin' : 'status-user';
?>
<tr class="user-row">
    <td class="text-muted"><?= $i++; ?></td>
    <td class="text-start">
        <div class="d-flex align-items-center">
            <div class="avatar me-3">
                <?= mb_substr($show['fname'], 0, 1, 'UTF-8'); ?>
            </div>
            <div>
                <div class="fw-bold name-tag"><?= $show['fname'];?> <?= $show['lname'];?></div>
                <div class="small text-muted"><i class="bi bi-calendar-event me-1"></i><?= $show['dob'];?></div>
            </div>
        </div>
    </td>
    <td><?= $show['gender'];?></td>
    <td><span class="fw-medium text-primary"><?= $show['tel'];?></span></td>
    
    <td class="text-start small"><?= $show['vill_name'] ?? '-';?></td>
    <td class="text-start small"><?= $show['dis_name'] ?? '-';?></td>
    <td class="text-start small"><?= $show['pro_name'] ?? '-';?></td>
    
    <td>
        <span class="badge-status <?= $statusClass; ?>">
            <?= strtoupper($show['status']);?>
        </span>
    </td>
    <td><code class="text-dark fw-bold"><?= $show['username'];?></code></td>
    <td>
        <div class="d-flex justify-content-center">
            <a href="update_users.php?user_id=<?= $show['user_id'];?>" class="btn-action btn-edit me-1" title="ແກ້ໄຂ">
                <i class="bi bi-pencil-square"></i>
            </a>
            <a href="delete_users.php?user_id=<?= $show['user_id'];?>" class="btn-action btn-del" 
               onclick="return confirm('ຢືນຢັນການລົບຜູ້ໃຊ້: <?= $show['fname'];?>?')" title="ລົບ">
                <i class="bi bi-trash3-fill"></i>
            </a>
        </div>
    </td>
</tr>
<?php } ?>
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('.user-row');

        rows.forEach(row => {
            let name = row.querySelector('.name-tag').textContent.toLowerCase();
            let username = row.querySelector('code').textContent.toLowerCase();
            if (name.includes(value) || username.includes(value)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>

</body>
</html>