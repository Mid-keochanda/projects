<?php
session_start();
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
  <title>WMS | Admin Management</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.0/css/OverlayScrollbars.min.css">
  
  <style>
    :root { --main-bg: #f4f6f9; --sidebar-color: #1e293b; }
    body { font-family: 'Noto Sans Lao', sans-serif; background-color: var(--main-bg); }

    /* Navbar Glassmorphism */
    .main-header {
        border-bottom: none !important;
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }

    /* Sidebar Gradient Style */
    .main-sidebar { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%) !important; }
    .nav-sidebar .nav-link.active {
        background: linear-gradient(90deg, #3b82f6, #06b6d4) !important;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        border-radius: 8px;
    }

    /* Iframe Styling (The "Card" Look) */
    .content-wrapper { padding: 15px; background: var(--main-bg); }
    iframe[name="frame"] {
        width: 100%;
        height: calc(100vh - 145px);
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background: white;
        display: block;
    }

    /* Logout Button */
    .btn-logout {
        border-radius: 20px;
        padding: 4px 15px;
        font-weight: 600;
        transition: 0.3s;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="Homepage.php" target="frame" class="nav-link font-weight-bold text-primary">ໜ້າຫຼັກ</a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">
      <li class="nav-item mr-3">
        <span class="badge badge-light px-3 py-2 shadow-sm" style="border-radius: 15px; color: #475569;">
          <i class="fas fa-user-circle mr-1 text-primary"></i> 
          <?php echo $_SESSION['fname'] . " " . $_SESSION['lname']; ?>
        </span>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          <button class="btn btn-outline-danger btn-logout btn-sm">
            <i class="fas fa-power-off"></i> ອອກຈາກລະບົບ
          </button>
        </a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link text-center">
      <span class="brand-text font-weight-bold" style="letter-spacing: 1px;">WMS MANAGER</span>
    </a>

    <div class="sidebar">
      <nav class="mt-3">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <li class="nav-header text-uppercase opacity-50 small" style="letter-spacing: 1px;">ຂໍ້ມູນພື້ນຖານ</li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-friends"></i>
              <p>ຈັດການເຄື່ອງອາໄຫຼ່ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="parts/form_parts.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-info"></i> <p>ເພີ່ມຂໍ້ມູນອາໄຫຼ່</p>
                </a>
              </li>
            </ul>
          </li>
          
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-friends"></i>
              <p>ຈັດການລົດ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="cars/form_cars.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-info"></i> <p>ເພີ່ມຂໍ້ມູນລົດ</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-friends"></i>
              <p>ຈັດການລູກຄ້າ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="customers/form_customers.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-info"></i> <p>ເພີ່ມຂໍ້ມູນລູກຄ້າ</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-map-marked-alt"></i>
              <p>ແຂວງ/ເມືອງ/ບ້ານ <i class="right fas fa-angle-left"></i></p>
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
              <p>ຈັດການພະນັກງານ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="users/from_users.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon"></i> <p>ບັນທຶກພະນັກງານ</p>
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

  <footer class="main-footer text-center py-2 small">
    <strong>Copyright &copy; 2026 <span class="text-primary">Warehouse Management</span>.</strong>
  </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.0/js/jquery.overlayScrollbars.min.js"></script>
<script src="dist/js/adminlte.js"></script>

<script>
  // ເພີ່ມຄວາມ Smooth ເວລາກົດເມນູ
  $(document).ready(function() {
    $('.nav-link[target="frame"]').on('click', function() {
      $('iframe[name="frame"]').css('opacity', '0.5');
      setTimeout(function() {
        $('iframe[name="frame"]').css('opacity', '1');
      }, 200);
    });
  });
</script>

</body>
</html>