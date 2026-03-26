<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນຜູ້ນຳໃຊ້ | Ultra Premium</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-bg: #f4f7fe;
            --brand-color: #4318ff;
            --brand-gradient: linear-gradient(135deg, #4318ff 0%, #868cff 100%);
            --card-bg: rgba(255, 255, 255, 0.8);
            --text-main: #2b3674;
            --text-muted: #a3aed1;
        }

        body {
            background-color: var(--primary-bg);
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,0.03) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.03) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.03) 0, transparent 50%);
            font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Top Header Area */
        .page-title {
            color: var(--text-main);
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        /* Search Bar */
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 5px 15px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .search-box:focus-within {
            border-color: var(--brand-color);
            box-shadow: 0 10px 25px -5px rgba(67, 24, 255, 0.15);
        }

        .search-input {
            border: none;
            background: transparent;
            padding: 10px 15px;
            width: 100%;
            outline: none;
            color: var(--text-main);
        }

        /* Add Button */
        .btn-add-new {
            background: var(--brand-gradient);
            color: white;
            border-radius: 50px;
            padding: 12px 25px;
            font-weight: 600;
            border: none;
            box-shadow: 0 10px 20px -5px rgba(67, 24, 255, 0.4);
            transition: 0.3s;
        }
        .btn-add-new:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 15px 25px -5px rgba(67, 24, 255, 0.5);
        }

        /* Floating Table Style */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0 12px; /* ເຮັດໃຫ້ແຖວແຍກອອກຈາກກັນ */
            width: 100%;
        }

        .table-custom thead th {
            border: none;
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 20px 10px 20px;
            font-weight: 600;
        }

        .table-custom tbody tr {
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .table-custom tbody tr:hover {
            transform: translateY(-3px) scale(1.005);
            box-shadow: 0 15px 30px rgba(0,0,0,0.06);
        }

        .table-custom td {
            border: none;
            padding: 18px 20px;
            vertical-align: middle;
        }

        /* ໂຄ້ງມົນສະເພາະຫົວ ແລະ ທ້າຍຂອງແຖວ */
        .table-custom td:first-child { border-radius: 15px 0 0 15px; }
        .table-custom td:last-child { border-radius: 0 15px 15px 0; }

        /* User Identity */
        .avatar-gradient {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.3);
        }

        /* Status Badges */
        .status-badge {
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .bg-admin { background: rgba(67, 24, 255, 0.1); color: var(--brand-color); }
        .bg-user { background: #f4f7fe; color: #707eae; }

        /* Action Buttons */
        .action-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            text-decoration: none;
            margin: 0 4px;
        }
        
        .action-edit { background: #f4f7fe; color: var(--brand-color); }
        .action-edit:hover { background: var(--brand-color); color: white; }
        
        .action-delete { background: #ffeeee; color: #ee5d50; }
        .action-delete:hover { background: #ee5d50; color: white; }

        .text-truncate-custom {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>

<div class="container-fluid py-5 main-container">
    
    <div class="row align-items-center mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <h2 class="page-title mb-1">ຂໍ້ມູນຜູ້ນຳໃຊ້</h2>
            <p class="text-muted mb-0 small">ຈັດການ ແລະ ກວດສອບລາຍຊື່ຜູ້ນຳໃຊ້ລະບົບ</p>
        </div>
        
        <div class="col-md-4 mb-3 mb-md-0 d-flex justify-content-center">
            <div class="search-box">
                <i class="bi bi-search text-muted ms-2"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="ຄົ້ນຫາຊື່ ຫຼື ເບີໂທ...">
            </div>
        </div>

        <div class="col-md-4 text-md-end">
            <a href="from_users.php" class="btn btn-add-new">
                <i class="bi bi-plus-lg me-2"></i>ເພີ່ມຜູ້ໃຊ້ໃໝ່
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive" style="overflow-x: auto; padding-bottom: 20px;">
                <table class="table-custom text-center" id="userTable">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th class="text-start" width="25%">ຂໍ້ມູນທົ່ວໄປ</th>
                            <th width="15%">ເບີໂທຕິດຕໍ່</th>
                            <th class="text-start" width="25%">ທີ່ຢູ່ປະຈຸບັນ</th>
                            <th width="10%">ບັນຊີ</th>
                            <th width="10%">ສະຖານະ</th>
                            <th width="10%">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            include("../cennect_dbstock.php");
                            $sql = "SELECT d.*, a.pro_name, b.dis_name, c.vil_name 
                                    FROM users AS d
                                    LEFT JOIN provinces AS a ON d.pro_id = a.pro_id
                                    LEFT JOIN districts AS b ON d.dis_id = b.dis_id
                                    LEFT JOIN villages AS c ON d.vil_id = c.vil_id
                                    ORDER BY d.user_id DESC";
                            $select = mysqli_query($connect, $sql);
                            
                            $i = 1;
                            while($show = mysqli_fetch_array($select)){
                                $statusClass = ($show['status'] == 'admin') ? 'bg-admin' : 'bg-user';
                                // ສຸ່ມສີ Avatar ໃຫ້ບໍ່ຊ້ຳກັນ (Optionally)
                                $colors = ['linear-gradient(135deg, #f6d365 0%, #fda085 100%)', 'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)', 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)', 'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'];
                                $randomBg = $colors[$i % 4];
                        ?>
                        <tr class="search-item">
                            <td class="text-muted fw-bold"><?= $i++; ?></td>
                            <td class="text-start">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-gradient" style="background: <?= $randomBg; ?>">
                                        <?= mb_substr($show['fname'], 0, 1, 'UTF-8'); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold mb-0 text-main user-name"><?= $show['fname'];?> <?= $show['lname'];?></div>
                                        <div class="small text-muted"><?= $show['gender'];?> | ວ/ດ/ປ: <?= $show['dob'];?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark user-tel"><?= $show['tel'];?></div>
                            </td>
                            <td class="text-start">
                                <div class="fw-bold text-dark text-truncate-custom" title="<?= $show['pro_name'];?>"><?= $show['pro_name'];?></div>
                                <div class="small text-muted text-truncate-custom" title="<?= $show['dis_name'];?> - <?= $show['vil_name'];?>"><?= $show['dis_name'];?> - <?= $show['vil_name'];?></div>
                            </td>
                            <td>
                                <div class="fw-bold" style="color: var(--brand-color);"><?= $show['username'];?></div>
                            </td>
                            <td>
                                <span class="status-badge <?= $statusClass; ?>">
                                    <?= strtoupper($show['status']);?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="update_users.php?user_id=<?= $show['user_id'];?>" class="action-btn action-edit" title="ແກ້ໄຂ">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="delete_users.php?user_id=<?= $show['user_id'];?>" class="action-btn action-delete delete" title="ລົບ">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted small">© 2026 Garage Management System</span>
                <span class="badge rounded-pill bg-white text-dark shadow-sm px-3 py-2 border">ລວມທັງໝົດ: <b class="text-primary"><?= ($i-1); ?></b> ລາຍການ</span>
            </div>
            
        </div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchQuery = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('.search-item');

        tableRows.forEach(function(row) {
            let name = row.querySelector('.user-name').textContent.toLowerCase();
            let tel = row.querySelector('.user-tel').textContent.toLowerCase();
            
            // ຖ້າຊື່ ຫຼື ເບີໂທ ກົງກັບທີ່ພິມຫາ ໃຫ້ສະແດງ, ຖ້າບໍ່ກົງໃຫ້ເຊື່ອງໄວ້
            if (name.includes(searchQuery) || tel.includes(searchQuery)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>