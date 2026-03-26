<?php
       include("../cennect_dbstock.php");
      $pro_id = $_POST['pro_id'];
      $select=mysqli_query($connect,"select*from districts where pro_id='$pro_id'");
 ?>
 <select class="form_control " id="dis_id">
 <option value="">ເລືອກເມື່ອງ</option>
         <?php
             while($data=mysqli_fetch_array($select)){
        ?>
   <option value="<?php echo$data['dis_id'];?>"><?php echo$data['dis_name'];?></option>
         <?php
         }
         ?>
  </select>