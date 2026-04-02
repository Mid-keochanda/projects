<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ແກ້ໄຂຂໍ້ມູນຜູ້ໃຊ້ງານ</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script>
    <script src="../jquery.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap');

        body {
            background-color: #f0f2f5;
            font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif;
        }

        .main-card {
            border: none;
            border-radius: 13px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        .card-header-edit {
            background: linear-gradient(45deg, #17a2b8, #007bff);
            color: white;
            padding: 15px;
            text-align: center;
            border: none;
        }

        .form-section {
            background: #ffffff;
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #eef0f2;
            height: 100%;
            transition: all 0.3s;
        }

        .form-section:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f2f5;
            color: #007bff;
        }

        .form-label {
            font-weight: 500;
            margin-top: 10px;
            color: #495057;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            border-color: #80bdff;
        }

        .btn-update {
            background: #28a745;
            color: white;
            padding: 12px 40px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }

        .btn-update:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .pass-box {
            position: relative;
        }

        .show-pass-label {
            font-size: 0.85rem;
            color: #6c757d;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 5px;
        }
    </style>

    <script>
        function myFunction() {
            var x = document.getElementById("confirm_password");
            x.type = (x.type === "password") ? "text" : "password";
        }
        function myFunct() {
            var x = document.getElementById("password");
            x.type = (x.type === "password") ? "text" : "password";
        }

        $(function(){
            // ແຂວງ -> ເມືອງ
            $('#pro_id').change(function(){
                var pro_id = $('#pro_id').val();
                $.post('insert_user_name.php',{ pro_id : pro_id }, function(output){
                    $("#dis_id").html(output);
                })
            })
            // ເມືອງ -> ບ້ານ
            $('#dis_id').change(function(){
                var dis_id = $('#dis_id').val();
                $.post('insert_vill_name.php',{ dis_id : dis_id }, function(output){
                    $("#vill_id").html(output);
                })
            })

            $("#send").click(function(){
                var data = {
                    user_id : $("#user_id").val(),
                    fname : $("#fname").val(),
                    lname : $("#lname").val(),
                    gender : $("input[name='gender']:checked").val(),
                    dob : $("#dob").val(),
                    tel : $("#tel").val(),
                    pro_id : $("#pro_id").val(),
                    dis_id : $("#dis_id").val(),
                    vill_id : $("#vill_id").val(),
                    status : $("#status").val(),
                    username : $("#username").val(),
                    password : $("#password").val(),
                    remark : $("#remark").val()
                };

                if(data.fname == ""){
                    Swal.fire({ icon: 'warning', title: 'ກະລຸນາປ້ອນຊື່' });
                } else if(data.password != $("#confirm_password").val()){
                    Swal.fire({ icon: 'warning', title: 'ລະຫັດບໍ່ຕົງກັນ', confirmButtonText: "ຕົກລົງ" });
                } else {
                    $.post("save_update_users.php", data, function(output){
                        $("#show").html(output);
                    })
                }
            })
        })
    </script>
</head>
<body>

    <?php
        include("../cennect_dbstock.php");
        $user_id = $_GET['user_id'];
        $sqll = mysqli_query($connect, "SELECT a.pro_id, a.pro_name, b.dis_id, b.dis_name, c.vill_id, c.vill_name, d.* FROM users as d 
                                        LEFT JOIN provinces as a ON a.pro_id=d.pro_id 
                                        LEFT JOIN districts as b ON b.dis_id=d.dis_id 
                                        LEFT JOIN villages as c ON c.vill_id=d.vill_id 
                                        WHERE d.user_id='$user_id'"); 
        $sql = mysqli_fetch_array($sqll);
    ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card main-card">
                    <div class="card-header-edit">
                        <h3><i class="bi bi-pencil-square me-2"></i> ແກ້ໄຂຂໍ້ມູນຜູ້ໃຊ້ງານ</h3>
                        <p class="mb-0 opacity-75">ກະລຸນາກວດສອບຂໍ້ມູນໃຫ້ລະອຽດກ່ອນທຳການບັນທຶກ</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <input type="hidden" id="user_id" value="<?= $sql["user_id"]; ?>">
                        
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-section">
                                    <div class="section-title"><i class="bi bi-person-badge me-2"></i>ຂໍ້ມູນສ່ວນຕົວ</div>
                                    
                                    <label class="form-label">ຊື່:</label>
                                    <input type="text" id="fname" class="form-control" value="<?= $sql["fname"]; ?>">

                                    <label class="form-label">ນາມສະກູນ:</label>
                                    <input type="text" id="lname" class="form-control" value="<?= $sql["lname"]; ?>">

                                    <label class="form-label d-block">ເພດ:</label>
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="g_m" value="ຊາຍ" <?php if($sql['gender']=='ຊາຍ'){echo "checked";}?>>
                                            <label class="form-check-label" for="g_m">ຊາຍ</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="g_f" value="ຍີງ" <?php if($sql['gender']=='ຍີງ' || $sql['gender']=='ຍິງ'){echo "checked";}?>>
                                            <label class="form-check-label" for="g_f">ຍິງ</label>
                                        </div>
                                    </div>

                                    <label class="form-label">ວັນເດືອນປີເກີດ:</label>
                                    <input type="date" id="dob" class="form-control" value="<?= $sql["dob"] ?>">

                                    <label class="form-label">ເບີໂທ:</label>
                                    <input type="text" id="tel" class="form-control" value="<?= $sql["tel"]; ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-section">
                                    <div class="section-title"><i class="bi bi-geo-alt me-2"></i>ທີ່ຢູ່ ແລະ ຕຳແໜ່ງ</div>

                                    <label class="form-label">ແຂວງ:</label>
                                    <select class="form-select" id="pro_id">
                                        <option value="<?= $sql['pro_id'];?>"><?= $sql['pro_name'];?></option>
                                        <?php
                                            $pro_query = mysqli_query($connect, "SELECT * FROM provinces");
                                            while($p = mysqli_fetch_array($pro_query)){
                                                echo "<option value='".$p['pro_id']."'>".$p['pro_name']."</option>";
                                            }
                                        ?>
                                    </select>

                                    <label class="form-label">ເມືອງ:</label>
                                    <select class="form-select" id="dis_id">
                                        <option value="<?= $sql['dis_id'];?>"><?= $sql['dis_name'];?></option>
                                    </select>

                                    <label class="form-label">ບ້ານ:</label>
                                    <select class="form-select" id="vill_id">
                                        <option value="<?= $sql['vill_id'];?>"><?= $sql['vill_name'];?></option>
                                    </select>

                                    <label class="form-label">ສະຖານະ:</label>
                                    <select class="form-select" id="status">
                                        <option value="<?= $sql['status'];?>"><?= $sql['status'];?></option>
                                        <option value="ພະນັກງານ">ພະນັກງານ</option>
                                        <option value="ຜູ້ບໍລິຫານ">ຜູ້ບໍລິຫານ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-section">
                                    <div class="section-title"><i class="bi bi-lock me-2"></i>ຄວາມປອດໄພ</div>

                                    <label class="form-label">ຊື່ຜູ້ໃຊ້ງານ:</label>
                                    <input type="text" id="username" class="form-control" value="<?= $sql["username"]; ?>">

                                    <label class="form-label">ລະຫັດຜ່ານ:</label>
                                    <input type="password" id="password" class="form-control" value="<?= $sql["password"]; ?>">
                                    <label class="show-pass-label" onclick="myFunct()">
                                        <input type="checkbox" class="form-check-input"> ສະແດງລະຫັດ
                                    </label>

                                    <label class="form-label">ຢືນຢັນລະຫັດຜ່ານ:</label>
                                    <input type="password" id="confirm_password" class="form-control" placeholder="ປ້ອນອີກຄັ້ງ">
                                    <label class="show-pass-label" onclick="myFunction()">
                                        <input type="checkbox" class="form-check-input"> ສະແດງລະຫັດ
                                    </label>

                                    <label class="form-label">ໝາຍເຫດ:</label>
                                    <input type="text" id="remark" class="form-control" value="<?= $sql["remark"]; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-1">
                            <button type="button" id="send" class="btn btn-update">
                                <i class="bi bi-check-circle me-2"></i> ອັບເດດຂໍ້ມູນ
                            </button>
                            <a href="from_users.php" class="btn btn-outline-secondary ms-2" style="border-radius: 10px; padding: 12px 30px;">
                                ຍົກເລີກ
                            </a>
                        </div>
                    </div>
                    <div class="card-footer bg-light" id="show"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>