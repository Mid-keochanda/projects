<?php
session_start();//ປະກາດໃຊ້ຕົວປ່ຽນ session
include("cennect_dbstock.php");
$username = $_POST['username'];
$password = $_POST['password'];

$select = mysqli_query($connect, "select *from users where username='$username' and password = password('$password')");
$check = mysqli_num_rows($select);

if($check <> 0){
    $data = mysqli_fetch_array($select);
    if($data['status'] == "ຜູ້ຈັດການ"){

        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['fname'] = $data['fname'];
        $_SESSION['lname'] = $data['lname'];
        $_SESSION['checked'] = 1; //ກວດສອບຖ້າຫາກບໍ່ລັນຟາຍລັອກອິນບໍ່ສາມາດໃຊ້ລະບົບໄດ້
        echo "<script>
		let timerInterval
Swal.fire({
  title: 'ກຳລັງເຂົ້າສູ່ລະບົບ!',
  timer: 1500,
  timerProgressBar: true,
  didOpen: () => {
    Swal.showLoading()
    timerInterval = setInterval(() => {
       const content = Swal.getContent()
      if (content) {
        const b = content.querySelector('b')
        if (b) {
          b.textContent = Swal.getTimerLeft()
        }
      }
    }, 100)
  },
  willClose: () => {
    clearInterval(timerInterval)
  }
}).then((result) => {
  /* Read more about handling dismissals below */
  if (result.dismiss === Swal.DismissReason.timer) {
    console.log('I was closed by the timer')
  }
})
window.setTimeout(function(){ 
    location='menu_admin.php';
} ,1500);
		</script>";
    }else if($data['status'] == "ພະນັກງານ"){

        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['fname'] = $data['fname'];
        $_SESSION['lname'] = $data['lname'];
        $_SESSION['checked'] = 1; //ກວດສອບຖ້າຫາກບໍ່ລັນຟາຍລັອກອິນບໍ່ສາມາດໃຊ້ລະບົບໄດ້
        echo "<script>
		let timerInterval
Swal.fire({
  title: 'ກຳລັງເຂົ້າສູ່ລະບົບ!',
  timer: 1500,
  timerProgressBar: true,
  didOpen: () => {
    Swal.showLoading()
    timerInterval = setInterval(() => {
       const content = Swal.getContent()
      if (content) {
        const b = content.querySelector('b')
        if (b) {
          b.textContent = Swal.getTimerLeft()
        }
      }
    }, 100)
  },
  willClose: () => {
    clearInterval(timerInterval)
  }
}).then((result) => {
  /* Read more about handling dismissals below */
  if (result.dismiss === Swal.DismissReason.timer) {
    console.log('I was closed by the timer')
  }
})
window.setTimeout(function(){ 
    location='menu_user.php';
} ,1500);
		</script>";
    }else{
        echo"ສະຖານະບໍ່ຖືກຕ້ອງ";
    }
}else{
    echo"<script>Swal.fire({
        postion: 'top',
        icon:'error',
        title:'ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ',
    })
        </script>";
}
?>