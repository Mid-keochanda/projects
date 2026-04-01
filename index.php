<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ລະບົບບໍລິຫານ ສາງສິນຄ້າຂະໜາດນ້ອຍ</title>
  <link rel="stylesheet" href="icon/css/all.min.css" />
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <script src="jquery.js"></script>
  <script src="sweetalert/dist/sweetalert2.all.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }

    body {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      padding: 15px;
      background: #1abc9c;
      overflow: hidden;
    }

    .wrapper {
      max-width: 500px;
      width: 100%;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0px 4px 10px 1px rgba(0, 0, 0, 0.1);
    }

    .wrapper .title {
      height: 120px;
      background: #16a085;
      border-radius: 5px 5px 0 0;
      color: #fff;
      font-size: 30px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .wrapper form {
      padding: 25px 35px;
    }

    .wrapper form .row {
      height: 60px;
      margin-top: 15px;
      position: relative;
    }

    .wrapper form .row input {
      height: 100%;
      width: 100%;
      outline: none;
      padding-left: 70px;
      border-radius: 5px;
      border: 1px solid lightgrey;
      font-size: 18px;
      transition: all 0.3s ease;
    }

    #login {
      border-color: #16a085;
    }

    form .row input::placeholder {
      color: #999;
    }

    .wrapper form .row i {
      position: absolute;
      width: 55px;
      height: 100%;
      color: #fff;
      font-size: 22px;
      background:#16a085;
      border: 1px solid #16a085;
      border-radius: 5px 0 0 5px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .wrapper form .pass {
      margin-top: 12px;
    }

    .wrapper form .pass a {
      color: #16a085;
      font-size: 17px;
      text-decoration: none;
    }

    .wrapper form .pass a:hover {
      text-decoration: underline;
    }

    .wrapper form #login {
      margin-top: 20px;
      color: #fff;
      font-size: 20px;
      font-weight: 500;
      padding-left: 0px;
      background: #16a085;
      border: 1px solid #16a085;
      cursor: pointer;
    }

    #login {
      background:12876f;
    }

    .wrapper form .signup-link {
      text-align: center;
      margin-top: 45px;
      font-size: 17px;
    }

    .wrapper form .signup-link a {
      color: #16a085;
      text-decoration: none;
    }

    form .signup-link a:hover {
      text-decoration: underline;
    }
  </style>
  <script>
    $(function(){
            $("#login").click(function(){
                var username = $("#username").val();
                var password = $("#password").val();
                if(username == ""){
                    Swal.fire({
                        icon: 'warning', //error, warning, success, question
		                title: 'ກະລຸນາປ້ອນຊື່',//ຫົວຂໍ້
                    });
                  }else if(password == ""){
                    Swal.fire({
                        icon: 'warning', //error, warning, success, question
		                title: 'ກະລຸນາປ້ອນລະຫັດຜ່ານ',//ຫົວຂໍ້
                    });
                }else{
                    $.post("check_users.php",{
                        username : username,
                        password : password
                    },
                    function(output){
                        $(".show").html(output);
                    })
                } //ປິດ else 
            })
        })
    </script>
</head>
<style>
    *{
        font-family: 'phetsarath ot';
    }
</style>
<body>
  <div class="wrapper">
    <div class="card card-body title text-center">
      <span>ໜ້າເຂົ້າສູ່ລະບົບ ບໍລິຫານສາ້ງສິນຄ້າ (ຂະໜາດນ້ອຍ)</span>
    </div>
    <form>
      <div class="row">
        <i class="fas fa-user"></i>
        <input type="text" placeholder="ຊື່ຜູ້ນຳໃຊ້" id='username'/>
      </div>
      <div class="row">
        <i class="fas fa-lock"></i>
        <input type="password" placeholder="ລະຫັດຜ່ານ" id='password'/>
      </div>
      <div class="row button">
        <button type="button" id="login" class="btn btn-block btn-lg"> ເຂົ້າສູ່ລະບົບ</button>
        <div class="show"></div>
      </div>
    </form>
  </div>
</body>

</html>