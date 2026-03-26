<?php
    include('../cennect_dbstock.php');
    $dis_id = $_POST['dis_id'];
    $select = mysqli_query($connect, "select*from villages where dis_id='$dis_id'");
?>
<label for="">ເລືອກເມືອງ:</label>
<select class="form-control">
    <option value="">ກະລຸນາເລືອກເມືອງ</option>
        <?php
            include("../cennect_dbstock.php");
                                               
            while($show = mysqli_fetch_array($select)){
        ?>
        <option value="<?= $show['vil_id'];?>"><?= $show['vil_name'];?></option>
        <?php
            }
        ?>
</select>