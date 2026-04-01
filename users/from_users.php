<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຟອມບັນທຶກຜູ້ໃຊ້ງານ</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="../sweetalert/dist/sweetalert2.all.min.js"></script>
    <script src="../jquery.js"></script>

    <style>
        /* ນຳເຂົ້າ Font ພາສາລາວ */
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap');

        body {
            background-color: #f4f7f6;
            font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif;
            color: #333;
        }

        .main-container {
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background-color: #ffffff;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #0d6efd, #00d4ff);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            text-align: center;
            border-bottom: none;
        }

        .card-header-custom h3 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 5px;
            margin-top: 15px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
            border-color: #86b7fe;
        }

        .gender-group {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 5px;
            padding: 8px 0;
        }

        .btn-custom {
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-save {
            background-color: #198754;
            color: white;
            border: none;
        }

        .btn-save:hover {
            background-color: #157347;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
        }

        .btn-clear {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-clear:hover {
            background-color: #bb2d3b;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }

        .password-toggle-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 5px;
            font-size: 0.9em;
            color: #666;
        }

        .form-section {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            height: 100%;
        }
        
        /* ປັບແຕ່ງ Checkbox ໃຫ້ນ້ອຍລົງໜ້ອຍໜຶ່ງ */
        .form-check-input {
            cursor: pointer;
        }
    </style>

    <script>
        // ໂຊລະຫັດຜ່ານ - Confirm
        function myFunction() {
            var x = document.getElementById("confirm_password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        // ໂຊລະຫັດຜ່ານ - Main
        function myFunct() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        $(function(){
            // ຊື່ມາອັດຕະໂຕມັດ (ແຂວງ -> ເມືອງ)
            $('#pro_id').change(function(){
                var pro_id = $('#pro_id').val();
                $.post('insert_user_name.php',{ pro_id : pro_id }, function(output){
                    $("#dis_id").html(output);
                });
            });

            // ເລືອກເມືອງໃຫ້ບ້ານມາອັດຕະໂນມັດ (ເມືອງ -> ບ້ານ)
            $('#dis_id').change(function(){
                var dis_id = $('#dis_id').val();
                $.post('insert_vil_name.php',{ dis_id : dis_id }, function(output){
                    $("#vil_id").html(output);
                });
            });

            $("#send").click(function(){
                var fname = $("#fname").val();
                var lname = $("#lname").val();
                var gender = $("input[name='gender']:checked").val(); // ປັບໃຫ້ໃຊ້ name='gender' ເພື່ອໃຫ້ radio ເຮັດວຽກຖືກຕ້ອງ
                var dob = $("#dob").val();
                var tel = $("#tel").val();
                var pro_id = $("#pro_id").val();
                var dis_id = $("#dis_id").val();
                var vill_id = $("#vill_id").val();
                var status = $("#status").val();
                var username = $("#username").val();
                var password = $("#password").val();
                var confirm_password = $("#confirm_password").val();
                var remark = $("#remark").val();

                if(fname == ""){
                    Swal.fire({
                        icon: 'warning',
                        title: 'ກະລຸນາປ້ອນຊື່',
                        confirmButtonText: 'ຕົກລົງ'
                    });
                } else if(password != confirm_password){
                    Swal.fire({
                        icon: 'warning',
                        title: 'ລະຫັດບໍ່ຕົງກັນ',
                        confirmButtonText: 'ຕົກລົງ'
                    });
                } else {
                    $.post("insert_users.php",{
                        fname : fname,
                        lname : lname,
                        gender : gender,
                        dob : dob,
                        tel : tel,
                        pro_id : pro_id,
                        dis_id : dis_id,
                        vill_id : vill_id,
                        status : status,
                        username : username,
                        password : password,
                        remark : remark
                    },
                    function(output){
                        $("#show").html(output);
                    });
                }
            });
        });
    </script>
</head>
<body>

    <div class="container main-container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card card-custom">
                    
                    <div class="card-header-custom">
                        <h3><i class="bi bi-person-lines-fill me-2"></i>ຟອມບັນທຶກຜູ້ໃຊ້ງານ</h3>
                    </div>
                    
                    <div class="card-body p-2">
                        <form id="userForm">
                            <div class="row g-4">
                                
                                <div class="col-md-4">
                                    <div class="form-section">
                                        <h5 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-person me-2"></i>ຂໍ້ມູນສ່ວນຕົວ</h5>
                                        
                                        <label for="fname" class="form-label mt-0">ຊື່:</label>
                                        <input type="text" id="fname" class="form-control" placeholder="ກະລຸນາປ້ອນຊື່...">

                                        <label for="lname" class="form-label">ນາມສະກູນ:</label>
                                        <input type="text" id="lname" class="form-control" placeholder="ກະລຸນາປ້ອນນາມສະກຸນ...">

                                        <label class="form-label">ເພດ:</label>
                                        <div class="gender-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" id="genderM" value="ຊາຍ" checked>
                                                <label class="form-check-label" for="genderM">ຊາຍ</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" id="genderF" value="ຍິງ">
                                                <label class="form-check-label" for="genderF">ຍິງ</label>
                                            </div>
                                        </div>

                                        <label for="dob" class="form-label">ວັນເດືອນປີເກີດ:</label>
                                        <input type="date" id="dob" class="form-control">

                                        <label for="tel" class="form-label">ເບີໂທ:</label>
                                        <input type="text" id="tel" class="form-control" placeholder="ຕົວຢ່າງ: 020 5555xxxx">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-section">
                                        <h5 class="text-info border-bottom pb-2 mb-3"><i class="bi bi-geo-alt me-2"></i>ທີ່ຢູ່ & ສະຖານະ</h5>

                                        <label for="pro_id" class="form-label mt-0">ເລືອກແຂວງ:</label>
                                        <select class="form-select" id="pro_id">
                                            <option value="">-- ກະລຸນາເລືອກແຂວງ --</option>
                                            <?php
                                                // ສົມມຸດວ່າເຊື່ອມຕໍ່ DB ແລ້ວ
                                                include("../cennect_dbstock.php");
                                                $select = mysqli_query($connect, "select * from provinces");
                                                while($show = mysqli_fetch_array($select)){
                                                    echo "<option value='".$show['pro_id']."'>".$show['pro_name']."</option>";
                                                }
                                            ?>
                                        </select>

                                        <label for="dis_id" class="form-label">ເລືອກເມືອງ:</label>
                                        <select class="form-select" id="dis_id">
                                            <option value="">-- ກະລຸນາເລືອກເມືອງ --</option>
                                             <?php
                                                // ສົມມຸດວ່າເຊື່ອມຕໍ່ DB ແລ້ວ
                                                include("../cennect_dbstock.php");
                                                $select = mysqli_query($connect, "select * from districts");
                                                while($show = mysqli_fetch_array($select)){
                                                    echo "<option value='".$show['dis_id']."'>".$show['dis_name']."</option>";
                                                }
                                            ?>
                                        </select>

                                        <label for="vill_id" class="form-label">ເລືອກບ້ານ:</label>
                                        <select class="form-select" id="vill_id">
                                            <option value="">-- ກະລຸນາເລືອກບ້ານ --</option>
                                             <?php
                                                // ສົມມຸດວ່າເຊື່ອມຕໍ່ DB ແລ້ວ
                                                include("../cennect_dbstock.php");
                                                $select = mysqli_query($connect, "select * from villages");
                                                while($show = mysqli_fetch_array($select)){
                                                    echo "<option value='".$show['vill_id']."'>".$show['vill_name']."</option>";
                                                }
                                            ?>
                                        </select>

                                        <label for="status" class="form-label">ສະຖານະຜູ້ໃຊ້:</label>
                                        <select class="form-select" id="status">
                                            <option value="">-- ກະລຸນາເລືອກສະຖານະ --</option>
                                            <option value="ພະນັກງານ">ພະນັກງານ</option>
                                            <option value="ຜູ້ບໍລິຫານ">ຜູ້ບໍລິຫານ</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-section">
                                        <h5 class="text-danger border-bottom pb-2 mb-3"><i class="bi bi-shield-lock me-2"></i>ຂໍ້ມູນເຂົ້າລະບົບ</h5>

                                        <label for="username" class="form-label mt-0">ຊື່ຜູ້ໃຊ້ງານ (Username):</label>
                                        <input type="text" id="username" class="form-control" placeholder="User123...">

                                        <label for="password" class="form-label">ລະຫັດຜ່ານ:</label>
                                        <input type="password" id="password" class="form-control" placeholder="••••••••">
                                        <div class="password-toggle-group">
                                            <input type="checkbox" class="form-check-input" id="showPass1" onclick="myFunct()">
                                            <label class="form-check-label" for="showPass1">ສະແດງລະຫັດຜ່ານ</label>
                                        </div>

                                        <label for="confirm_password" class="form-label">ຢືນຢັນລະຫັດຜ່ານ:</label>
                                        <input type="password" id="confirm_password" class="form-control" placeholder="••••••••">
                                        <div class="password-toggle-group">
                                            <input type="checkbox" class="form-check-input" id="showPass2" onclick="myFunction()">
                                            <label class="form-check-label" for="showPass2">ສະແດງລະຫັດຜ່ານ</label>
                                        </div>

                                        <label for="remark" class="form-label">ໝາຍເຫດ:</label>
                                        <textarea id="remark" class="form-control" rows="1" placeholder="ຄຳອະທິບາຍເພີ່ມເຕີມ..."></textarea>
                                    </div>
                                </div>

                            </div>

                            <hr class="mt-1 mb-1 text-muted">

                            <div class="text-center">
                                <button type="button" id="send" class="btn btn-save btn-custom me-3">
                                    <i class="bi bi-save me-2"></i>ບັນທຶກຂໍ້ມູນ
                                </button>
                                <button type="reset" class="btn btn-clear btn-custom">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>ລ້າງຂໍ້ມູນ
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-footer bg-white border-0 text-center" id="show"></div>
                    
                </div>
            </div>
        </div>
    </div>

</body>
</html>