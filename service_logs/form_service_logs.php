<?php 
include("../cennect_dbstock.php"); 
mysqli_set_charset($connect, "utf8");
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ເປີດບິນສ້ອມແປງໃໝ່</title>
    
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            font-family: 'Phetsarath OT', sans-serif; 
            background-color: #f1f5f9;
        }
        .custom-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            padding: 1.5rem;
            border: none;
        }
        .form-control-custom {
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }
        .form-control-custom:focus {
            background: #fff;
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.15);
        }
        .btn-custom {
            border-radius: 12px;
            padding: 0.75rem;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card custom-card mx-auto" style="max-width: 550px;">
        
        <div class="card-header-custom text-white text-center">
            <h4 class="mb-1 fw-bold"><i class="fas fa-file-invoice me-2"></i> ເປີດບິນສ້ອມແປງໃໝ່</h4>
            <p class="text-white-50 small mb-0">ກະລຸນາປ້ອນຂໍ້ມູນຍານພາຫະນະ ແລະ ອາການເບື້ອງຕົ້ນ</p>
        </div>
        
        <div class="card-body p-4">
            <form action="insert_service_logs.php" method="POST">
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small"><i class="fas fa-car text-muted me-1"></i> ເລືອກລົດ</label>
                    <select name="car_id" class="form-select form-control-custom" required>
                        <option value="">-- ເລືອກທะບຽນລົດ --</option>
                        <?php 
                        // 🌟 ປັບປຸງ SQL ໃຫ້ດຶງຊື່ເຈົ້າຂອງລົດມາສະແດງນຳ ເພື່ອບໍ່ໃຫ້ຫຼົງຄັນ
                        $cars = mysqli_query($connect, "
                            SELECT c.car_id, c.car_plate, cust.cust_name 
                            FROM cars c 
                            LEFT JOIN customers cust ON c.cust_id = cust.cust_id 
                            ORDER BY c.car_plate ASC
                        ");
                        while($c = mysqli_fetch_array($cars)) {
                            $client_info = !empty($c['cust_name']) ? " (" . $c['cust_name'] . ")" : "";
                            echo "<option value='".$c['car_id']."'>".$c['car_plate'].$client_info."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small"><i class="fas fa-user-gear text-muted me-1"></i> ຊ່າງຜູ້ຮັບຜິດຊອບ</label>
                    <select name="user_id" class="form-select form-control-custom" required>
                        <option value="">-- ເລືອກຊ່າງໃນລະບົບ --</option>
                        <?php 
                        // 🌟 ປັບປຸງໃຫ້ສະແດງທັງ ຊື່ ແລະ ນາມສະກຸນ ຂອງຊ່າງ
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
                    <label class="form-label fw-bold text-dark small"><i class="fas fa-notes-medical text-muted me-1"></i> ອາການເບື້ອງຕົ້ນ / ວຽກທີ່ຕ້ອງເຮັດ</label>
                    <textarea name="symptoms" class="form-control form-control-custom" rows="4" required placeholder="ລະບຸອາການເພີ່ມເຕີມ ເຊັ່ນ: ປ່ຽນນ້ຳມັນເຄື່ອງ, ແອບໍ່ເຢັນ, ມີສຽງດັງຢູ່ກ້ອງທ້ອງ..."></textarea>
                </div>

                <hr class="text-muted my-4">

                <div class="row g-2">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary w-150 fw-bold shadow-sm btn-custom">
                            <i class="fas fa-save me-1"></i> ບັນທຶກເປີດບິນ
                        </button>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</div>

</body>
</html>