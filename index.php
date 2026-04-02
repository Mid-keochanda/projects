<!DOCTYPE html>
<html lang="lo">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ລະບົບບໍລິຫານ ຮ້ານສ້ອມແປງລົດ</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <script src="jquery.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap');
    
    * { 
        margin: 0; padding: 0; box-sizing: border-box; 
        font-family: 'Noto Sans Lao', sans-serif; 
    }

    body { 
        display: flex; align-items: center; justify-content: center; 
        min-height: 100vh; padding: 15px; position: relative;
    }

    /* ພື້ນຫຼັງຮູບຮ້ານສ້ອມແປງລົດ */
    body::before {
        content: "";
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background-image: url('https://images.unsplash.com/photo-1486006396113-ad7302ff172c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'); 
        background-size: cover; background-position: center;
        filter: brightness(0.4) blur(3px); /* ເຮັດໃຫ້ຮູບມືດ ແລະ ມົວເລັກນ້ອຍ */
        z-index: -1;
    }

    .wrapper { 
        max-width: 400px; width: 100%; 
        background: rgba(255, 255, 255, 0.95); 
        border-radius: 15px; 
        box-shadow: 0px 15px 35px rgba(0,0,0,0.5); 
        padding: 40px 30px; border-top: 8px solid #e67e22; /* ເສັ້ນສີສົ້ມແບບ Garage Style */
    }

    .logo-area {
        text-align: center; margin-bottom: 25px;
    }

    .logo-area i {
        font-size: 50px; color: #e67e22; margin-bottom: 10px;
    }

    .title { 
        color: #2c3e50; font-size: 24px; font-weight: 700; 
        text-align: center; margin-bottom: 5px;
    }

    .subtitle {
        color: #7f8c8d; text-align: center; font-size: 14px; margin-bottom: 30px;
    }

    .row-input { position: relative; margin-bottom: 20px; }

    .row-input i { 
        position: absolute; left: 15px; top: 50%; 
        transform: translateY(-50%); color: #e67e22; font-size: 18px;
    }

    .row-input input { 
        width: 100%; height: 50px; padding-left: 45px; 
        border: 2px solid #ddd; border-radius: 10px; 
        font-size: 16px; transition: 0.3s; outline: none;
    }

    .row-input input:focus { 
        border-color: #e67e22; box-shadow: 0 0 8px rgba(230, 126, 34, 0.2); 
    }

    #loginBtn { 
        width: 100%; height: 50px; 
        background: #e67e22; color: #fff; 
        border: none; border-radius: 10px; 
        font-size: 18px; font-weight: 600; cursor: pointer; 
        transition: 0.3s; text-transform: uppercase; letter-spacing: 1px;
    }

    #loginBtn:hover { background: #d35400; transform: translateY(-2px); }
    
    .footer-text {
        text-align: center; margin-top: 20px; color: #7f8c8d; font-size: 12px;
    }
  </style>
</head>
<body>

  <div class="wrapper">
    <div class="logo-area">
        <i class="fas fa-car-side"></i>
        <div class="title">Garage Management</div>
        <div class="subtitle">ລະບົບບໍລິຫານ ຮ້ານສ້ອມແປງລົດ</div>
    </div>
    
    <form id="loginForm">
      <div class="row-input">
        <i class="fas fa-user-cog"></i>
        <input type="text" placeholder="ຊື່ຜູ້ນຳໃຊ້" id="username" autocomplete="off" required />
      </div>
      <div class="row-input">
        <i class="fas fa-key"></i>
        <input type="password" placeholder="ລະຫັດຜ່ານ" id="password" required />
      </div>
      <button type="button" id="loginBtn">
          <i class="fas fa-sign-in-alt"></i> ເຂົ້າສູ່ລະບົບ
      </button>
      <div class="show_msg"></div>
    </form>

    <div class="footer-text">
        © 2026 Auto Service System. All Rights Reserved.
    </div>
  </div>

  <script>
    $(document).ready(function() {
      $("#loginBtn").click(function() {
        let user = $("#username").val();
        let pass = $("#password").val();

        if (user == "" || pass == "") {
          Swal.fire({ 
              icon: 'warning', title: 'ແຈ້ງເຕືອນ', 
              text: 'ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ!',
              confirmButtonColor: '#e67e22' 
          });
          return;
        }

        // Effect Loading
        let btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i> ກຳລັງກວດສອບ...').prop('disabled', true);

        $.post("check_users.php", { username: user, password: pass }, function(data) {
          $(".show_msg").html(data);
          btn.html('<i class="fas fa-sign-in-alt"></i> ເຂົ້າສູ່ລະບົບ').prop('disabled', false);
        });
      });

      $(document).keypress(function(e) {
        if (e.which == 13) { $("#loginBtn").click(); }
      });
    });
  </script>
</body>
</html>