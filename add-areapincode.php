<?php
include "header.php";
if(isset($_REQUEST['save']))
{
 
  $service_area_id = $_REQUEST['area_id'];
  $pincode = $_REQUEST['pincode'];
 
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `area_pincode`(`service_area_id`,`pincode`) VALUES (?,?)");
    $stmt->bind_param("is",$service_area_id,$pincode);
    $Resp=$stmt->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
    
  }
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
    setcookie("msg", "data",time()+3600,"/");
      header("location:add-areapincode.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:add-areapincode.php");
  }
}
?>
<div class='p-6' x-data='exportTable'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-lg font-semibold dark:text-white-light">Area Pincode- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post">
                <div>
                    <label for="groupFname"> Service Area Id</label>

                    <select class="form-select text-white-dark" name="area_id">
                        <option>-none-</option>
                    <?php 
                        $stmt = $obj->con1->prepare("SELECT * FROM `service_area`");
                        $stmt->execute();
                        $Resp=$stmt->get_result();
                        $stmt->close();

                        while($result = mysqli_fetch_array($Resp)){
                    ?>
                        <option value="<?php echo $result['id'] ?>"><?php echo $result['name'] ?></option>
                        <!-- <option>Gujarat</option>
                        <option>Bhayander</option>
                        <option>VIRAR NSP VASAI</option>
                        <option>Thane</option> -->
                    <?php } ?>
                    </select>
                </div>
                <div>
                    <label for="groupFname"> Pincode </label>
                    <input id="groupFname" name="pincode" type="text" class="form-input" />
                    <div class="relative inline-flex align-middle gap-3 mt-4">
                        <button type="submit" name="save" id="save" class="btn btn-primary">Save </button>
                        <button type="button" class="btn  btn-warning ">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>