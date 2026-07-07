<?php
session_start();
if (@$_SESSION['checked'] <> 1) {
    echo "<script>alert('ກະລຸນາລົງຊື່ເຂົ້າໃຊ້ກ່ອນ'); location='index.php';</script>";
    exit(); 
}

// ກວດສອບສິດ (ຖ້າບໍ່ມີສະຖານະ ຖືວ່າເປັນ user ທົ່ວໄປກ່ອນ)
$is_admin = (isset($_SESSION['status']) && $_SESSION['status'] == 'admin');

// ກຳນົດໜ້າທຳອິດຕາມສິດການໃຊ້ງານ
$default_page = $is_admin ? "Homepage.php" : "service_logs/form_service_logs.php";
?>
<!DOCTYPE html>
<html lang="lo">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Garage & WMS | Management</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.0/css/OverlayScrollbars.min.css">
  
  <style>
    :root { 
        --main-bg: #f1f5f9; 
        --sidebar-bg: #0f172a; 
        --sidebar-hover: rgba(255, 255, 255, 0.05);
        --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    
    body { 
        font-family: 'Noto Sans Lao', sans-serif; 
        background-color: var(--main-bg); 
    }

    /* ---------------- Navbar Glassmorphism ---------------- */
    .main-header {
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .navbar-nav .nav-link {
        color: #475569 !important;
        font-weight: 500;
    }
    .navbar-nav .nav-link:hover {
        color: #2563eb !important;
    }

    /* ---------------- Sidebar Premium Style ---------------- */
    .main-sidebar { 
        background: var(--sidebar-bg) !important; 
        box-shadow: 4px 0 20px rgba(0,0,0,0.1);
    }
    .brand-link {
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
        padding: 1.25rem 1rem !important;
    }
    .brand-text {
        font-weight: 700 !important;
        letter-spacing: 1.5px;
        color: #f8fafc;
        font-size: 1.1rem;
    }
    
    /* Sidebar Menu Items */
    .nav-sidebar .nav-item {
        margin-bottom: 2px;
    }
    .nav-sidebar .nav-item > .nav-link {
        border-radius: 10px;
        margin: 0 12px;
        color: #cbd5e1;
        transition: all 0.3s ease;
        padding: 10px 15px;
    }
    
    .nav-sidebar .nav-item > .nav-link:hover {
        background: var(--sidebar-hover);
        color: #ffffff;
        transform: translateX(4px);
    }
    
    .nav-sidebar .nav-link.active {
        background: var(--primary-gradient) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        border-radius: 10px;
    }
    
    .nav-sidebar .nav-icon {
        font-size: 1.1rem;
        margin-right: 8px;
        color: #94a3b8;
    }
    .nav-sidebar .nav-link:hover .nav-icon,
    .nav-sidebar .nav-link.active .nav-icon {
        color: #ffffff;
    }

    /* Sub-menu styling */
    .nav-treeview > .nav-item > .nav-link {
        margin: 0 12px 0 20px;
        border-radius: 8px;
        padding: 8px 15px;
        font-size: 0.95rem;
    }
    .nav-treeview > .nav-item > .nav-link:hover {
        background: rgba(255, 255, 255, 0.03);
    }

    /* ---------------- Iframe & Content Area ---------------- */
    .content-wrapper { 
        padding: 20px; 
        background: var(--main-bg); 
    }
    iframe[name="frame"] {
        width: 100%;
        height: calc(100vh - 110px);
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 35px rgba(0,0,0,0.06);
        background: white;
        display: block;
        transition: opacity 0.3s ease;
    }

    /* ---------------- Logout & User Badge ---------------- */
    .user-badge {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        font-weight: 600;
        border-radius: 30px;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }
    
    .btn-logout {
        border-radius: 30px;
        padding: 6px 18px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s;
        border: 1px solid #fee2e2;
        color: #ef4444;
        background: #fef2f2;
    }
    .btn-logout:hover {
        background: #ef4444;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }
    
    /* Footer */
    .main-footer {
        background: var(--main-bg);
        border-top: none;
        color: #8b6464;
        padding: 10px 20px;
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
        <a href="<?php echo $default_page; ?>" target="frame" class="nav-link text-primary"><i class="fas fa-home me-1"></i> ໜ້າຫຼັກ</a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">
      <li class="nav-item mr-3">
        <div class="user-badge shadow-sm">
          <i class="fas fa-user-circle text-primary" style="font-size: 1.2rem;"></i> 
          <?php echo $_SESSION['fname'] . " " . $_SESSION['lname']; ?>
          <?php if($is_admin) echo '<span class="badge bg-danger ms-2">Admin</span>'; ?>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link p-0" href="logout.php" style="margin-right: 15px;">
          <button class="btn btn-logout">
            <i class="fas fa-sign-out-alt"></i> ອອກຈາກລະບົບ
          </button>
        </a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo $default_page; ?>" target="frame" class="brand-link text-center">
      <i class="fas fa-tools text-primary mr-2" style="font-size: 1.3rem;"></i>
      <span class="brand-text">GARAGE MANAGER</span>
    </a>

    <div class="sidebar">
      <nav class="mt-0">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <?php if($is_admin): ?>
          <li class="nav-header text-uppercase opacity-50 small" style="letter-spacing: 1px; color: #94a3b8;">ເມນູຫຼັກ</li>
          
          <li class="nav-item <?php echo $is_admin ? 'menu-open' : ''; ?>">
            <a href="#" class="nav-link <?php echo $is_admin ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p> ແຜງຄວບຄຸມ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="Homepage.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-info"></i> <p>ຍອດການເງິນ & ຄັງສິນຄ້າ</p>
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>

          <li class="nav-header text-uppercase opacity-10 small mt-0" style="letter-spacing: 1px; color: #94a3b8;">ລະບົບບໍລິການ</li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
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
              <i class="nav-icon fas fa-car"></i>
              <p>ຈັດການລົດລູກຄ້າ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="cars/form_cars.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-info"></i> <p>ເພີ່ມຂໍ້ມູນລົດ</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item <?php echo !$is_admin ? 'menu-open' : ''; ?>">
            <a href="#" class="nav-link <?php echo !$is_admin ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-file-invoice-dollar"></i>
              <p>ອອກບິນ & ສ້ອມແປງ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="service_logs/form_service_logs.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i> <p>ອອກບິນສ້ອມແປງລົດ</p>
                </a>
                <a href="service_logs/manage_sale.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-warning"></i> <p>ໜ້າຂາຍເຄື່ອງອາໄຫຼ່</p>
                </a>
              </li>
            </ul>
          </li>
          
          <?php if($is_admin): ?>
          <li class="nav-header text-uppercase opacity-10 small mt-0" style="letter-spacing: 1px; color: #94a3b8;">ລະບົບສາງສິນຄ້າ</li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tags"></i>
              <p>ປະເພດອາໄຫຼ່ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="categories/form_categories.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-info"></i> <p>ເພີ່ມຂໍ້ມູນປະເພດອາໄຫຼ່</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-box-open"></i>
              <p>ລາຍການອາໄຫຼ່ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="parts_profile/form_parts_profile.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-info"></i> <p>ເພີ່ມຂໍ້ມູນອາໄຫຼ່</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>ນຳເຄື່ອງອາໄຫຼ່ເຂົ້າສາງ <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="part_purchases/form_part_purchases.php" target="frame" class="nav-link">
                  <i class="far fa-circle nav-icon text-success"></i> <p>ເພີ່ມຂໍ້ມູນນຳອາໄຫຼ່ເຂົ້າ</p>
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>

        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <iframe name="frame" src="<?php echo $default_page; ?>"></iframe>
  </div>

  <footer class="main-footer text-center py-2 small">
    <strong>Copyright &copy; 2026 <span class="text-primary font-weight-bold">Garage Management System</span>.</strong> All rights reserved.
  </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.0/js/jquery.overlayScrollbars.min.js"></script>
<script src="dist/js/adminlte.js"></script>

<script>
  $(document).ready(function() {
    // ຈັດການການເປີດ/ປິດ Active state ຂອງເມນູໃຫ້ເບິ່ງງາມຂຶ້ນ
    $('.nav-sidebar .nav-link').on('click', function() {
      // ຖ້າກົດເມນູຍ່ອຍ ໃຫ້ປ່ຽນສີພື້ນຫຼັງ
      if(!$(this).hasClass('active') && $(this).attr('target') === 'frame'){
          $('.nav-sidebar .nav-link').removeClass('active');
          $(this).addClass('active');
          $(this).closest('.has-treeview').children('.nav-link').addClass('active');
      }
    });

    // Effect ຕອນໂຫຼດໜ້າ Iframe (Smooth Loading)
    $('.nav-link[target="frame"]').on('click', function() {
      $('iframe[name="frame"]').css({'opacity': '0.3', 'transform': 'scale(0.99)'});
      setTimeout(function() {
        $('iframe[name="frame"]').css({'opacity': '1', 'transform': 'scale(1)'});
      }, 350);
    });
  });
</script>

</body>
</html>