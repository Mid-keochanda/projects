<?php
// 1. ຕັ້ງຄ່າ Header ແລະ ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
header("Content-Type: text/html; charset=utf-8");
include("../cennect_dbstock.php"); 
mysqli_set_charset($connect, "utf8"); 

// 2. Logic ການຄົ້ນຫາ (Search)
$search = "";
if(isset($_POST['search_text'])){
    $search = mysqli_real_escape_string($connect, trim($_POST['search_text']));
}

// 3. Query ຂໍ້ມູນ (ເອົາອັນໃໝ່/ອັບເດດຫຼ້າສຸດຂຶ້ນກ່ອນ)
$query = "SELECT * FROM categories 
          WHERE name_la LIKE '%$search%' 
          OR name_en LIKE '%$search%' 
          ORDER BY updated_at DESC, id DESC";

$sql = mysqli_query($connect, $query);
$count = mysqli_num_rows($sql);
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>ລະບົບຈັດການປະເພດຂີ້ເຫຍື້ອ</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary-blue: #047394;
            --bg-body: #f4f7f6;
        }

        body { 
            font-family: 'Noto Sans Lao', sans-serif; 
            background-color: var(--bg-body);
            color: #333;
            font-size: 12px;
        }

        .main-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            background: #fff;
            overflow: hidden;
        }

        .card-header-custom {
            background: var(--primary-blue);
            padding: 1rem;
            color: #fff;
        }

        /* Search Form UI */
        .search-wrapper {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 5px 15px;
            display: flex;
            align-items: center;
            transition: 0.3s;
        }
        .search-wrapper:focus-within { background: rgba(255, 255, 255, 0.3); }
        .search-input {
            background: transparent;
            border: none;
            color: #fff;
            padding: 5px;
            outline: none;
            width: 180px;
        }
        .search-input::placeholder { color: #e0e0e0; }

        /* Table UI */
        .table thead th {
            background-color: #f8f9fa;
            color: #0e0e0e;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 16px;
            padding: 12px;
            border-bottom: 2px solid #edf2f7;
        }
        .table tbody td {
            padding: 10px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-status {
            font-size: 11px;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Action Buttons */
        .btn-action {
            width: 30px;
            height: 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: 0.2s;
            text-decoration: none;
        }
        .btn-edit { background: #fff4e5; color: #ff9800; border: 1px solid #ffe0b2; }
        .btn-delete { background: #ffebee; color: #f44336; border: 1px solid #ffcdd2; }
        .btn-edit:hover { background: #ff9800; color: #fff; }
        .btn-delete:hover { background: #f44336; color: #fff; }

        .dt-text { font-size: 11px; color: #888; display: block; }
        .dt-updated { color: #ff9800; font-weight: 500; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="main-card">
        <div class="card-header-custom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 fw-bold">ຈັດການປະເພດຂີ້ເຫຍື້ອ</h4>
                <p class="mb-0 opacity-75 small">ຈັດການ ແລະ ຕິດຕາມຂໍ້ມູນປະເພດຂີ້ເຫຍື້ອໃນລະບົບ</p>
            </div>

            <div class="d-flex align-items-center gap-3">
                <form action="" method="POST" class="search-wrapper">
                    <i class="bi bi-search me-2"></i>
                    <input type="text" name="search_text" class="search-input" placeholder="ຄົ້ນຫາຊື່..." value="<?= htmlspecialchars($search) ?>">
                    <?php if($search != ""): ?>
                        <a href="?" class="text-white ms-2"><i class="bi bi-x-circle-fill"></i></a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-sm btn-light ms-2 py-1 px-3 fw-bold" style="border-radius: 7px;">ຄົ້ນຫາ</button>
                </form>

                <a href="form_categories.php" class="btn btn-warning fw-bold px-4 d-flex align-items-center gap-2" style="border-radius: 10px; height: 45px;">
                    <i class="bi bi-plus-lg"></i> ເພີ່ມໃໝ່
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="text-center">
                        <tr>
                            <th width="70">ລຳດັບ</th>
                            <th class="text-start">ຊື່ພາສາລາວ</th>
                            <th class="text-start">ຊື່ພາສາອັງກິດ</th>
                            <th>ສະຖານະ</th>
                            <th>ວັນທີບັນທຶກ</th>
                            <th>ອັບເດດຫຼ້າສຸດ</th> <th width="130">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($count > 0){
                            $i = 1;
                            while($row = mysqli_fetch_array($sql)){
                                $created = date('d/m/Y H:i', strtotime($row['created_at']));
                                $updated = date('d/m/Y H:i', strtotime($row['updated_at']));
                                // ກວດສອບວ່າມີການແກ້ໄຂຫຼືບໍ່
                                $is_edited = ($row['updated_at'] != $row['created_at']);
                        ?>
                        <tr class="text-center">
                            <td><span class="text-muted fw-bold"><?= $i++ ?></span></td>
                            <td class="text-start fw-bold text-dark"><?= $row['name_la'] ?></td>
                            <td class="text-start text-secondary"><?= $row['name_en'] ?></td>
                            <td>
                                <?php if($row['is_recyclable'] == 1): ?>
                                    <span class="badge-status bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-recycle me-1"></i> ຣີໄຊເຄິນໄດ້
                                    </span>
                                <?php else: ?>
                                    <span class="badge-status bg-danger-subtle text-danger border border-danger-subtle">
                                        <i class="bi bi-x-circle me-1"></i> ຣີໄຊເຄິນບໍ່ໄດ້
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="dt-text"><i class="bi bi-calendar3 me-1"></i> <?= $created ?></span>
                            </td>
                            <td>
                                <?php if($is_edited): ?>
                                    <span class="dt-text dt-updated"><i class="bi bi-clock-history me-1"></i> <?= $updated ?></span>
                                <?php else: ?>
                                    <span class="text-muted small italic opacity-50">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="update_categories.php?id=<?= $row['id'] ?>" class="btn-action btn-edit" title="ແກ້ໄຂ">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="delete_categories.php?id=<?= $row['id'] ?>" class="btn-action btn-delete delete" title="ລຶບ">
                                        <i class="bi bi-trash3"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='py-5 text-center text-muted'><i class='bi bi-folder-x fs-1 d-block mb-2'></i> ບໍ່ພົບຂໍ້ມູນທີ່ທ່ານຕ້ອງການ</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <p class="mb-0 text-muted small">ສະແດງທັງໝົດ <span class="fw-bold text-dark"><?= $count ?></span> ລາຍການ</p>
        </div>
    </div>
</div>

<script>
    $(function(){
        // SweetAlert ຢືນຢັນການລຶບ
        $('.delete').on('click', function(e){
            e.preventDefault();
            const href = $(this).attr('href');
            Swal.fire({
                title: 'ຢືນຢັນການລຶບ?',
                text: "ຂໍ້ມູນນີ້ຈະຖືກລຶບອອກຖາວອນ!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#047394',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'ຕົກລົງ, ລຶບເລີຍ!',
                cancelButtonText: 'ຍົກເລີກ',
                customClass: {
                    confirmButton: 'rounded-pill px-4',
                    cancelButton: 'rounded-pill px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            })
        })
    })
</script>

</body>
</html>