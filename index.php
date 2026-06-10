<!DOCTYPE html>
<html lang="lo">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Garage & WMS | ລະບົບບໍລິຫານຮ້ານສ້ອມແປງລົດ</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;600;700&display=swap');
    
    * { 
        margin: 0; padding: 0; box-sizing: border-box; 
        font-family: 'Noto Sans Lao', sans-serif; 
    }

    body { 
        display: flex; align-items: center; justify-content: center; 
        min-height: 100vh; position: relative; overflow: hidden;
        background-color: #0f172a;
    }

    /* ພື້ນຫຼັງອູ່ຊ້ອມລົດແບບເຕັມຈໍພ້ອມ Overlay ໃຫ້ເບິ່ງມືດໆ */
    body::before {
        content: "";
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background-image: url('https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?q=80&w=1974&auto=format&fit=crop'); 
        background-size: cover; background-position: center;
        filter: brightness(0.35) blur(2px); 
        z-index: -1;
    }

    /* ແບບແກ້ວໃສ (Glassmorphism) */
    .wrapper { 
        max-width: 420px; width: 90%; 
        background: rgba(15, 23, 42, 0.65); 
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px; 
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        padding: 40px 35px; 
    }

    .logo-area {
        text-align: center; margin-bottom: 30px;
    }

    .logo-area i {
        font-size: 55px; 
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
        filter: drop-shadow(0 4px 6px rgba(234, 88, 12, 0.3));
    }

    .title { 
        color: #ffffff; font-size: 26px; font-weight: 700; 
        letter-spacing: 1px; margin-bottom: 5px;
    }

    .subtitle {
        color: #94a3b8; font-size: 14px; font-weight: 300;
    }

    .row-input { position: relative; margin-bottom: 25px; }

    .row-input i { 
        position: absolute; left: 18px; top: 50%; 
        transform: translateY(-50%); color: #94a3b8; font-size: 18px;
        transition: 0.3s;
    }

    .row-input input { 
        width: 100%; height: 55px; padding-left: 50px; padding-right: 15px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1); 
        border-radius: 12px; color: #ffffff;
        font-size: 15px; transition: all 0.3s ease; outline: none;
    }

    .row-input input::placeholder { color: #64748b; }

    .row-input input:focus { 
        background: rgba(255, 255, 255, 0.1);
        border-color: #f97316; 
        box-shadow: 0 0 15px rgba(249, 115, 22, 0.2); 
    }
    .row-input input:focus + i, .row-input input:not(:placeholder-shown) ~ i {
        color: #f97316;
    }

    #loginBtn { 
        width: 100%; height: 55px; 
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #fff; border: none; border-radius: 12px; 
        font-size: 16px; font-weight: 600; cursor: pointer; 
        transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1.5px;
        box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);
    }

    #loginBtn:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 6px 20px rgba(234, 88, 12, 0.4); 
    }
    #loginBtn:active { transform: translateY(1px); }
    
    .footer-text {
        text-align: center; margin-top: 30px; color: #64748b; font-size: 12px;
    }
    .show_msg { display: none; } /* ເຊື່ອງໄວ້ ເພາະເຮົາຈະໃຊ້ SweetAlert ແທນ */
  </style>
</head>
<body>

  <div class="wrapper">
    <div class="logo-area">
        <i class="fas fa-tools"></i>
        <div class="title">GARAGE PRO</div>
        <div class="subtitle">ລະບົບບໍລິຫານ ຈັດການອູ່ຊ້ອມລົດ</div>
    </div>
    
    <form id="loginForm">
      <div class="row-input">
        <input type="text" placeholder="ຊື່ຜູ້ນຳໃຊ້ (Username)" id="username" autocomplete="off" required />
        <i class="fas fa-user"></i>
      </div>
      <div class="row-input">
        <input type="password" placeholder="ລະຫັດຜ່ານ (Password)" id="password" required />
        <i class="fas fa-lock"></i>
      </div>
      <button type="button" id="loginBtn">
          <i class="fas fa-sign-in-alt mr-2"></i> ເຂົ້າສູ່ລະບົບ
      </button>
      <div class="show_msg"></div>
    </form>

    <div class="footer-text">
        &copy; 2026 Auto Service System. All Rights Reserved.
    </div>
  </div>

  <script>
    $(document).ready(function() {
      $("#loginBtn").click(function() {
        let user = $("#username").val().trim();
        let pass = $("#password").val().trim();

        if (user == "" || pass == "") {
          Swal.fire({ 
              icon: 'warning', title: 'ແຈ້ງເຕືອນ', 
              text: 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ ແລະ ລະຫັດຜ່ານ!',
              confirmButtonColor: '#f97316',
              background: '#1e293b',
              color: '#fff'
          });
          return;
        }

        // Effect Loading
        let btn = $(this);
        let originalText = btn.html();
        btn.html('<i class="fas fa-circle-notch fa-spin"></i> ກຳລັງກວດສອບ...').prop('disabled', true);

        // ສົ່ງຂໍ້ມູນໄປກວດສອບ
        $.post("check_users.php", { username: user, password: pass }, function(data) {
          $(".show_msg").html(data); // ໃຫ້ໂຄ້ດໃນ check_users.php ຣັນ SweetAlert
          btn.html(originalText).prop('disabled', false);
        });
      });

      $(document).keypress(function(e) {
        if (e.which == 13) { $("#loginBtn").click(); }
      });
    });
  </script>
</body>
</html>