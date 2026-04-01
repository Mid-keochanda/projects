<?php
session_start();
// ກວດສອບ Session ແລະ ຢຸດການເຮັດວຽກຖ້າບໍ່ມີການ Login
if (@$_SESSION['checked'] <> 1) {
    echo "<script>alert('ກະລຸນາລົງຊື່ເຂົ້າໃຊ້ກ່ອນ'); location='index.php';</script>";
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ລະບົບບໍລິຫານ ສາງສິນຄ້າ (Admin)</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <style>
    * { font-family: 'Phetsarath OT', sans-serif; }
    /* ປັບໃຫ້ iframe ສະແດງເຕັມຈໍ */
    .content-wrapper { overflow: hidden; position: relative; }
    iframe[name="frame"] {
      width: 100%;
      height: calc(100vh - 101px); /* ຫັກລົບ Navbar ແລະ Footer */
      border: none;
      display: block;
    }
    .main-sidebar { user-select: none; }
  </style>

  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="sweetalert/dist/sweetalert2.all.min.js"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="Homepage.php" target="frame" class="nav-link">ໜ້າຫຼັກ</a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <span class="nav-link">
          <i class="fas fa-user-clock"></i> 
          <?php echo $_SESSION['fname'] . " " . $_SESSION['lname']; ?>
        </span>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          <button class="btn btn-outline-danger btn-sm">
            <i class="fa fa-power-off"></i> ອອກຈາກລະບົບ
          </button>
        </a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">ລະບົບ ສາງສິນຄ້າ</span>
    </a>

          <li class="nav-header">ຂໍ້ມູນພື້ນຖານ</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-map-marker-alt"></i>
              <p>ແຂວງ/ເມືອງ/ບ້ານ <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="provinces/form_provinces.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon"></i> <p>ຂໍ້ມູນແຂວງ</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="districts/form_districts.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon"></i> <p>ຂໍ້ມູນເມືອງ</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="villages/form_villages.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon"></i> <p>ຂໍ້ມູນບ້ານ</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>ຈັດການພະນັກງານ <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="users/from_users.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon"></i> <p>ບັນທຶກພະນັກງານ</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="users/select_form_users.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon"></i> <p>ລາຍງານພະນັກງານ</p>
                </a>
              </li>
            </ul>
          </li>

        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <iframe name="frame" src="Homepage.php"></iframe>
  </div>

  <footer class="main-footer">
    <strong>Copyright &copy; 2026 ລະບົບສາງສິນຄ້າ.</strong>
    <div class="float-right d-none d-sm-inline-block"><b>Version</b> 1.0</div>
  </footer>
</div>

<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="dist/js/adminlte.js"></script>
</body>
</html>