<?php 
include("../cennect_dbstock.php"); 
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການຂໍ້ມູນລູກຄ້າ</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Noto Sans Lao', sans-serif; }
        .custom-card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); background: #fff; }
        
        /* Search Box Styling */
        .search-container { position: relative; }
        .search-container i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .search-input { padding-left: 40px !important; border-radius: 10px !important; border: 1px solid #e2e8f0 !important; height: 45px; transition: 0.3s; }
        .search-input:focus { box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1) !important; border-color: #4e73df !important; }

        /* Table Styling */
        .table-custom thead th { 
            background-color: #f8f9fc; 
            color: #4e73df; 
            font-size: 0.9rem; 
            font-weight: 700;
            padding: 15px; 
            border-bottom: 2px solid #eef2f7;
        }
        .table-custom tbody tr:hover { background-color: rgba(78, 115, 223, 0.03); }
        
        /* Badges & Buttons */
        .badge-male { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 500; }
        .badge-female { background-color: #fff1f2; color: #be123c; border: 1px solid #fecdd3; font-weight: 500; }
        .btn-action { width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; border: none; text-decoration: none; font-size: 0.85rem; }
        .btn-edit { background: #eff6ff; color: #2563eb; }
        .btn-edit:hover { background: #2563eb; color: #fff; }
        .btn-delete { background: #fef2f2; color: #dc2626; }
        .btn-delete:hover { background: #dc2626; color: #fff; }
        
        .date-info { font-size: 0.75rem; line-height: 1.4; }
        .updated-text { color: #f59e0b; font-weight: 500; }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="row mb-4 align-items-center">
            <div class="col-lg-4 col-md-12 mb-3 mb-lg-0">
                <h3 class="fw-bold text-dark mb-1"><i class="fas fa-address-book text-primary me-2"></i>ລາຍຊື່ລູກຄ້າ</h3>
                <p class="text-muted small mb-0">ຈັດການ ແລະ ກວດສອບຂໍ້ມູນລູກຄ້າທັງໝົດ</p>
            </div>
            
            <div class="col-lg-8 col-md-12">
                <div class="row g-2 justify-content-lg-end">
                    <div class="col-md-6 col-lg-5">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" class="form-control search-input" placeholder="ຄົ້ນຫາຊື່, ເບີໂທ ຫຼື ທີ່ຢູ່...">
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <a href="form_customers.php" class="btn btn-primary rounded-3 px-4 shadow-sm fw-bold w-100 d-flex align-items-center justify-content-center" style="height: 45px;">
                            <i class="fas fa-plus me-2"></i> ເພີ່ມລູກຄ້າໃໝ່
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card custom-card">
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0 text-nowrap" id="customerTable">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>ຊື່ລູກຄ້າ</th>
                            <th>ນາມສະກຸນ</th>
                            <th class="text-center">ເພດ</th>
                            <th>ເບີໂທລະສັບ</th>
                            <th>ທີ່ຢູ່</th>
                            <th>ວັນທີບັນທຶກ/ແກ້ໄຂ</th>
                            <th class="text-center">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody">
                        <?php
                        $i = 1;
                        $sql = mysqli_query($connect, "SELECT * FROM customers ORDER BY created_at DESC");
                        while($row = mysqli_fetch_array($sql)){
                        ?>
                        <tr>
                            <td class="text-center text-muted fw-bold"><?= $i ?></td>
                            <td class="fw-bold text-dark"><?= $row["cust_name"]; ?></td>
                            <td><?= $row["cust_surname"]; ?></td>
                            <td class="text-center">
                                <?php if($row["gender"] == "ຊາຍ"): ?>
                                    <span class="badge badge-male px-3">ຊາຍ</span>
                                <?php else: ?>
                                    <span class="badge badge-female px-3">ຍິງ</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-primary fw-bold"><i class="fas fa-phone me-1 small"></i> <?= $row["tel"]; ?></span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= ($row["address"] != "") ? $row["address"] : "-"; ?>
                                </small>
                            </td>
                            <td>
                                <div class="date-info text-muted">
                                    <div><i class="far fa-calendar-plus me-1 text-primary"></i> <?= date('d/m/Y', strtotime($row["created_at"])); ?></div>
                                    <?php if(!empty($row["updated_at"]) && $row["updated_at"] != $row["created_at"]): ?>
                                        <div class="updated-text mt-1">
                                            <i class="fas fa-edit me-1"></i> <?= date('d/m/Y H:i', strtotime($row["updated_at"])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="update_customers.php?cust_id=<?=$row['cust_id'];?>" class="btn-action btn-edit" title="ແກ້ໄຂ">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="delete_customers.php?cust_id=<?=$row['cust_id'];?>" class="btn-action btn-delete delete-link" title="ລົບ">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php $i++; } ?>
                    </tbody>
                </table>
            </div>
            
            <div id="noData" class="text-center py-5 d-none">
                <i class="fas fa-search fa-3x text-light mb-3"></i>
                <p class="text-muted">ບໍ່ພົບຂໍ້ມູນທີ່ທ່ານຄົ້ນຫາ...</p>
            </div>

            <div class="card-footer bg-white border-0 py-3 text-center border-top">
                <small class="text-muted">ລວມທັງໝົດ: <span class="fw-bold text-primary" id="rowCount"><?= ($i-1) ?></span> ທ່ານ</small>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function(){
            // --- ລະບົບຄົ້ນຫາ (Search Filter) ---
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                var visibleRows = 0;

                $("#customerTableBody tr").filter(function() {
                    var match = $(this).text().toLowerCase().indexOf(value) > -1;
                    $(this).toggle(match);
                    if(match) visibleRows++;
                });

                // ສະແດງຂໍ້ຄວາມເມື່ອບໍ່ພົບຂໍ້ມູນ
                if(visibleRows === 0) {
                    $("#noData").removeClass("d-none");
                    $("#customerTable").addClass("d-none");
                } else {
                    $("#noData").addClass("d-none");
                    $("#customerTable").removeClass("d-none");
                }
                
                $("#rowCount").text(visibleRows);
            });

            // --- ປຸ່ມລົບ (Delete Confirmation) ---
            $('.delete-link').on('click', function(e){
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'ຢືນຢັນການລົບ?',
                    text: "ທ່ານແນ່ໃຈບໍ່ວ່າຈະລົບຂໍ້ມູນລູກຄ້ານີ້?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
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