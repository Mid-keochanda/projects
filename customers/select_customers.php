<?php 
include("../cennect_dbstock.php"); 
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນລູກຄ້າ - Modern View</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --bg: #f8fafc;
        }

        body { 
            background-color: var(--bg); 
            font-family: 'Noto Sans Lao', sans-serif; 
            color: #1e293b;
        }

        .custom-card { 
            border: none; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
            background: #fff; 
            overflow: hidden;
        }
        
        /* Table Design */
        .table-custom thead th { 
            background-color: #f1f5f9; 
            color: #475569; 
            font-size: 0.8rem; 
            font-weight: 700;
            padding: 18px 12px; 
            border: none;
            text-transform: uppercase;
        }
        .table-custom tbody td { 
            font-size: 0.85rem; 
            padding: 10px 12px; 
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .table-custom tbody tr:hover { background-color: #fcfdfe; }

        /* Badge Style */
        .badge-pill { padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .bg-male { background-color: #e0f2fe; color: #0369a1; }
        .bg-female { background-color: #fff1f2; color: #be123c; }

        /* Action Buttons */
        .btn-action { 
            width: 38px; height: 38px; border-radius: 12px; 
            display: inline-flex; align-items: center; justify-content: center; 
            border: none; transition: 0.3s; text-decoration: none;
        }
        .btn-edit { background: #eef2ff; color: var(--primary); }
        .btn-edit:hover { background: var(--primary); color: #fff; transform: translateY(-3px); }
        .btn-delete { background: #fef2f2; color: var(--danger); }
        .btn-delete:hover { background: var(--danger); color: #fff; transform: translateY(-3px); }
        
        /* Time Styling */
        .time-box { line-height: 1.2; }
        .date-text { font-weight: 600; color: #334155; display: block; font-size: 0.85rem; }
        .hour-text { font-size: 0.7rem; color: #94a3b8; }
        
        .search-box {
            border-radius: 15px; border: 1.5px solid #e2e8f0;
            padding: 10px 20px; transition: 0.3s;
        }
        .search-box:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    </style>
</head>
<body>

    <div class="container-fluid px-4 py-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-white shadow-sm rounded-4 me-3">
                        <i class="fas fa-user-friends text-primary fs-4"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold m-0">ລາຍຊື່ລູກຄ້າ</h2>
                        <p class="text-muted small m-0">ຈັດການ ແລະ ຕິດຕາມຂໍ້ມູນລູກຄ້າທັງໝົດ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="d-flex gap-2 justify-content-md-end">
                    <input type="text" id="searchInput" class="form-control search-box w-50" placeholder="ຄົ້ນຫາລາຍຊື່...">
                    <a href="form_customers.php" class="btn btn-primary px-4 py-2 rounded-4 fw-bold shadow-sm">
                        <i class="fas fa-plus me-2"></i> ເພີ່ມໃໝ່
                    </a>
                </div>
            </div>
        </div>

        <div class="card custom-card">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead class="text-center">
                        <tr>
                            <th width="50">#</th>
                            <th class="text-start">ຊື່ລູກຄ້າ</th>
                            <th class="text-start">ນາມສະກຸນ</th>
                            <th>ເພດ</th>
                            <th>ເບີໂທລະສັບ</th>
                            <th>ທີ່ຢູ່</th>
                            <th class="text-start">ໝາຍເຫດ</th>
                            <th>ວັນທີບັນທຶກ</th>
                            <th>ອັບເດດລ່າສຸດ</th>
                            <th>ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody">
                        <?php
                        $i = 1;
                        $sql = mysqli_query($connect, "SELECT * FROM customers ORDER BY cust_id DESC");
                        while($row = mysqli_fetch_array($sql)){
                        ?>
                        <tr class="text-center">
                            <td><span class="text-muted"><?= $i++ ?></span></td>
                            <td class="text-start fw-bold"><?= htmlspecialchars($row["cust_name"]); ?></td>
                            <td class="fw-bold text-start text-secondary"><?= htmlspecialchars($row["cust_surname"]); ?></td>
                            <td>
                                <span class="badge-pill <?= ($row["gender"] == "ຊາຍ") ? 'bg-male' : 'bg-female' ?>">
                                    <i class="fas <?= ($row["gender"] == "ຊາຍ") ? 'fa-mars' : 'fa-venus' ?> me-1"></i><?= $row["gender"]; ?>
                                </span>
                            </td>
                            <td>
                                <a href="tel:<?= $row["tel"]; ?>" class="text-primary fw-bold text-decoration-none small">
                                    <?= $row["tel"]; ?>
                                </a>
                            </td>
                            <td class="fw-bold text-muted small">
                                <?= $row["address"] ?: '<span class="opacity-25">-</span>'; ?>
                            </td>
                            <td class="text-start">
                                <span class="text-info small fw-medium"><?= $row["remark"] ?: '<span class="opacity-25">-</span>'; ?></span>
                            </td>
                            <td>
                                <div class="time-box">
                                    <span class="date-text"><?= date('d/m/Y', strtotime($row["created_at"])); ?></span>
                                    <span class="hour-text"><?= date('H:i', strtotime($row["created_at"])); ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if(!empty($row["updated_at"]) && $row["updated_at"] != "0000-00-00 00:00:00"): ?>
                                    <div class="time-box text-success">
                                        <span class="date-text text-success"><?= date('d/m/Y', strtotime($row["updated_at"])); ?></span>
                                        <span class="hour-text text-success opacity-75"><?= date('H:i', strtotime($row["updated_at"])); ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small italic opacity-50">- ຍັງບໍ່ມີ -</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="update_customers.php?cust_id=<?=$row['cust_id'];?>" class="btn-action btn-edit" title="ແກ້ໄຂ"><i class="fas fa-pen"></i></a>
                                    <a href="delete_customers.php?cust_id=<?=$row['cust_id'];?>" class="btn-action btn-delete delete-link" title="ລົບ"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 py-4 px-4 border-top text-center">
                <p class="m-0 text-muted small">ລາຍການທັງໝົດ: <span class="fw-bold text-primary fs-6"><?= ($i-1) ?></span> ລາຍຊື່</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function(){
            // ຄົ້ນຫາຂໍ້ມູນ
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#customerTableBody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // ຢືນຢັນການລົບ
            $('.delete-link').on('click', function(e){
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລົບ?',
                    text: "ຂໍ້ມູນລູກຄ້າຈະຖືກລົບອອກຖາວອນ!",
                    icon: 'warning',
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
</body>
</html>