<?php
session_start();
include("cennect_dbstock.php");

if (isset($_POST['username']) && isset($_POST['password'])) {
    
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";

    $result = mysqli_query($connect, $sql);
    
    // ກວດສອບ Error ຂອງ SQL (ໃຊ້ເພື່ອ Debug)
    if (!$result) {
        die("<script>Swal.fire('Error SQL', '" . mysqli_error($connect) . "', 'error');</script>");
    }

    $count = mysqli_num_rows($result);

    if ($count > 0) {
        $row = mysqli_fetch_array($result);

        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['fname']   = $row['fname'];
        $_SESSION['lname']   = $row['lname'];
        $_SESSION['status']  = $row['status'];
        $_SESSION['checked'] = 1;

        // ກວດສອບຊື່ Status ໃຫ້ກົງກັບໃນ DB (ຜູ້ຈັດການ ຫຼື ຜູ້ບໍລິຫານ)
        $link = ($row['status'] == "ຜູ້ຈັດການ" || $row['status'] == "ຜູ້ບໍລິຫານ") ? "menu_admin.php" : "menu_user.php";

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'ເຂົ້າສູ່ລະບົບສຳເລັດ',
                text: 'ຍິນດີຕ້ອນຮັບ: " . $row['fname'] . "',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '$link';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ເຂົ້າສູ່ລະບົບບໍ່ສຳເລັດ',
                text: 'ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ!',
                footer: 'ໝາຍເຫດ: ກວດສອບການເຂົ້າລະຫັດໃຫ້ກົງກັນ'
            });
        </script>";
    }
}
?>