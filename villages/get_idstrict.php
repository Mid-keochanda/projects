<?php
include("../cennect_dbstock.php");
$pro_id = $_POST['pro_id'];
$select = mysqli_query($connect, "SELECT * FROM districts WHERE pro_id='$pro_id'");

echo '<option value="">-- ເລືອກເມືອງ --</option>'; 
while($data = mysqli_fetch_array($select)){
    echo '<option value="'.$data['dis_id'].'">'.$data['dis_name'].'</option>';
}
?>