<?php
    include('../cennect_dbstock.php');
    $pro_id = $_POST['pro_id'];
    $select = mysqli_query($connect, "select*from districts where pro_id='$pro_id'");
?>
<label for="">ເລືອກເມືອງ:</label>
<select class="form-control">
    <option value="">ກະລຸນາເລືອກແຂວງ</option>
        <?php
            include("../cennect_dbstock.php");
                                               
            while($show = mysqli_fetch_array($select)){
        ?>
        <option value="<?= $show['dis_id'];?>"><?= $show['dis_name'];?></option>
        <?php
            }
        ?>
</select>