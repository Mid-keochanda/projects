<?php include("../cennect_dbstock.php"); ?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ເປີດບິນສ້ອມແປງໃໝ່</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        body { 
            font-family: 'Noto Sans Lao', sans-serif; 
            background-color: #f1f5f9; 
        }
        .custom-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .card-header {
            border-radius: 20px 20px 0 0 !important;
            background: linear-gradient(135deg, #4361ee, #3f37c9);
        }
        .form-label {
            color: #475569;
        }
        .form-select, .form-control {
            border-radius: 10px;
            padding: 0.6rem 1rem;
            border: 1px solid #cbd5e1;
        }
        .form-select:focus, .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }
        .btn-submit {
            border-radius: 10px;
            padding: 0.75rem;
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            border: none;
            transition: all 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card custom-card mx-auto" style="max-width: 500px;">
        <div class="card-header text-white text-center py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i>ເປີດບິນສ້ອມແປງໃໝ່</h5>
        </div>
        <div class="card-body p-4">
            <form action="insert_service_logs.php" method="POST">
                
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="fas fa-car text-muted me-1"></i> ເລືອກລົດ</label>
                    <select name="car_id" class="form-select" required>
                        <option value="">-- ເລືອກທະບຽນລົດ --</option>
                        <?php 
                        // 🌟 ປັບປຸງ Query: ຕັດລົດທີ່ status = 'inactive' ອອກໄປ
                        $cars = mysqli_query($connect, "SELECT car_id, car_plate FROM cars WHERE status = 'active' OR status IS NULL ORDER BY car_id DESC");
                        while($c = mysqli_fetch_array($cars)) {
                            echo "<option value='".$c['car_id']."'>".$c['car_plate']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="fas fa-user-cog text-muted me-1"></i> ຊ່າງຜູ້ຮັບຜິດຊອບ</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- ເລືອກຊ່າງໃນລະບົບ --</option>
                        <?php 
                        $users = mysqli_query($connect, "SELECT user_id, fname, lname FROM users ORDER BY fname ASC");
                        if(mysqli_num_rows($users) > 0) {
                            while($u = mysqli_fetch_array($users)) {
                                echo "<option value='".$u['user_id']."'>".$u['fname']." ".$u['lname']."</option>";
                            }
                        } else {
                            echo "<option value='' disabled>--- ບໍ່ມີຂໍ້ມູນຊ່າງໃນລະບົບ ---</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold"><i class="fas fa-notes-medical text-muted me-1"></i> ອາການເບື້ອງຕົ້ນ / ບັນຫາທີ່ພົບ</label>
                    <textarea name="symptoms" class="form-control" rows="3" placeholder="ກະລຸນາລະບຸອາການຂອງລົດ..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-submit w-100 fw-bold shadow-sm">
                    <i class="fas fa-save me-1"></i> ບັນທຶກເປີດບິນ
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>