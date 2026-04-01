
<html>
<style>
*{font-family:'Phetsarath OT';}
</style>
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ລະບົບບໍລິຫານ ສາງສິນຄ້າ (User)</title>
<link rel="stylesheet" href="icon/css/all.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
	<script src="../sweetalert/dist/sweetalert2.all.min.js"></script>		
	<script src="../jquery.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<?php
session_start();
if(@$_SESSION['checked']<>1){
	echo "<script>alert('ລົງຊືີ່ເຂົ້າໃຊ້ກ່ອນ');
	</script>";
	}
else{
?>
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="Homepage.php" target="frame" class="nav-link">ໜ້າຫຼັກ</a>
      </li>
      
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
     
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
		         <i class="fas fa-expand-arrows-alt"></i>
        
        </a>
      </li>   
	  <li class="nav-item">
        <a class="nav-link" href="#" role="button">
		<i class="fas fa-user-clock"></i>
	<?php
	echo $_SESSION['fname']." ".$_SESSION['lname'];
	?>
        </a>
      </li>
      <li class="nav-item">
      <a class="nav-link" href="logout.php" 
      role="button"><button class="btn btn-outline-danger"> 
       <i class="fa fa-power-off" aria-hidden="true"></i>&ensp;ອອກຈາກລະບົບ
        </button></a>
       
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">ລະບົບ ສາງສິນຄ້າ</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->


      <!-- SidebarSearch Form -->
     

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                ສ້າງສິນຄ້າ
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">	
              <i class="fas fa-font"></i>
              <p>
                ປະເພດສິນຄ້າ
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
               <!-- ຕໍາແໜງຂອງຟອມບັນທຶກປະເພດສິນຄ້າ -->
                <a href="categories/form_categorice.php" target="frame" class="nav-link">
                  <i class="fas fa-plus-circle"></i>
                  <p>ເພີ່ມປະເພດສິນຄ້າ</p>
                </a>
              </li>
              <li class="nav-item">
               <!-- ຕໍາແໜງຂອງລາຍງານປະເພດສິນຄ້າ -->
                <a href="categories/select.php" target="frame" class="nav-link">
                  <i class="fas fa-eye"></i>
                  <p>ສະແດງປະເພດສິນຄ້າ</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
            <i class="fas fa-users"></i>
              <p>
                ຂໍ້ມູນສິນຄ້າ
                 <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
               <!-- ຟອມບັນທຶກຂໍ້ມູນສິນຄ້າ -->
                <a href="products/form_products.php" target="frame" class="nav-link">
                  <i class="fas fa-plus-circle"></i>
                  <p>ບັນທຶກຂໍ້ມູນສິນຄ້າ</p>
                </a>
              </li>
              <li class="nav-item">
               <!-- ຟາຍລາຍງານຂໍ້ມູນສິນຄ້າ -->
                <a href="products/select_products.php" target="frame" class="nav-link">
                  <i class="fas fa-eye"></i>
                  <p>ລາຍງານຂໍ້ມູນສິນຄ້າ</p>
                </a>
              </li>
              <li class="nav-item">
                <!-- ຟາຍຄົ້ນຫາຂໍ້ມູນສິນຄ້າ -->
                <a href=# target="frame" class="nav-link">
                  <i class="fas fa-search"></i>
                  <p>ຄົ້ນຫາຂໍ້ມູນສິນຄ້າ</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
            <i class="fas fa-cart-arrow-down"></i>
              <p>
                ຂໍ້ມູນສິນຄ້ານຳເຂົ້າ
                 <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
               <!-- ຟອມບັນທຶກຂໍ້ມູນສິນຄ້ານຳເຂົ້າ -->
                <a href="receives/form_receives.php" target="frame" class="nav-link">
                  <i class="fas fa-plus-circle"></i>
                  <p>ບັນທຶກຂໍ້ມູນສິນຄ້ານຳເຂົ້າ</p>
                 <i class="fas fa-angle-left right"></i>
                </a>
              </li>
              <li class="nav-item">
               <!-- ຟາຍລາຍງານຂໍ້ມູນສິນຄ້ານຳເຂົ້າ -->
                <a href="receives/select_receives.php" target="frame" class="nav-link">
                  <i class="fas fa-eye"></i>
                  <p>ສະແດງຂໍ້ມູນສິນຄິນນຳເຂົ້າ</p>
                 <i class="fas fa-angle-left right"></i>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
            <i class="fas fa-shopping-cart"></i>
              <p>
                ສິນຄ້າຂາຍອອກ
                 <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
               <!-- ຟອມບັນທຶກສິນຄ້າຂາຍອອກ -->
                <a href="orders/form_orders.php" target="frame" class="nav-link">
                  <i class="fas fa-plus-circle"></i>
                  <p>ບັນທຶກສິນຄ້າຂາຍອອກ</p>
                </a>
              </li>
              <li class="nav-item">
               <!-- ຟາຍລາຍງານຂໍ້ມູນສິນຄ້າຂາຍອອກ -->
                <a href="orders/select_orders.php" target="frame" class="nav-link">
                  <i class="fas fa-eye"></i>
                  <p>ສະແດງຂໍ້ມູນຂາຍອອກ</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">


	<iframe width="100%" height="100%" frameborder="0" name="frame" src="Homepage.php"></iframe>
       
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
   
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
</body>
</html>

<?php
 }
?>
<!-- update !-->

