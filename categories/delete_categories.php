
?>
<?php
    include('../cennect_dbstock.php');
    $id = $_GET['id'];
    $delete = mysqli_query($connect, "delete from categories where id='$id'");
    if($delete){
        echo "<script>location='select_categories.php'</script>";
    }
?>