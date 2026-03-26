<?php
    include("../cennect_dbstock.php");

    // ຮັບຄ່າຈາກ GET
    $pro_id = $_GET['pro_id'];
    $dis_id = $_GET['dis_id'];
    $vil_name = $_GET['vil_name'];
    $remark = $_GET['remark'];

    // 1. ກວດສອບຊື່ບ້ານຊ້ຳ (ໃຊ້ $connect ແທນ $insert)
    $select = mysqli_query($connect, "SELECT * FROM villages WHERE vil_name='$vil_name'");
    $check = mysqli_num_rows($select);

    if($check > 0){
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ຊື່ນີ້ມີຢູ່ແລ້ວ!',
                    text: 'ບ້ານ $vil_name ມີໃນລະບົບແລ້ວ',
                    confirmButtonColor: '#dc3545'
                });
              </script>";
    } else {
        // 2. ບັນທຶກຂໍ້ມູນ (ໃຊ້ $connect ແລະ ປ່ຽນຊື່ຕົວປ່ຽນຮັບຄ່າເປັນ $query_run ເພື່ອບໍ່ໃຫ້ຊ້ຳ)
        $sql = "INSERT INTO villages (pro_id, dis_id, vil_name, remark) VALUES ('$pro_id', '$dis_id', '$vil_name', '$remark')";
        $query_run = mysqli_query($connect, $sql);

        if($query_run) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'ບັນທຶກສຳເລັດ',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = 'form_villages.php'; // ກັບໄປໜ້າຟອມ
                    });
                  </script>";
        } else {
            echo "Error: " . mysqli_error($connect);
        }
    }
?>