<?php
include "header.php";
if(isset($_REQUEST['save']))
{
    $city_name = $_REQUEST['city_id'];
    $state_name = $_REQUEST['state_id'];
    $status = $_REQUEST['default_radio'];
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `city`(`ctnm`,`stnm`,`status`) VALUES (?,?,?)");
    $stmt->bind_param("sis",$city_name,$state_name,$status);
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
      header("location:city.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:city.php");
  }
}
?>
<div class='p-6'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">City- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post">
                
                <div>
                    
                    <label for="groupFname"> State Name</label>

                    <select class="form-select text-white-dark" name="state_id" required>
                        <option>-none-</option>
                    <?php 
                        $stmt = $obj->con1->prepare("SELECT * FROM `state`");
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
                    <label for="groupFname"> City Name </label>
                    <input id="groupFname" name="city_id" type="text" class="form-input" required />
                </div>    
                <div>
                    <label for="gridStatus">Status</label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="enable" class="form-radio" checked required />
                        <span>Enable</span>
                    </label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="disable" class="form-radio text-danger" required />
                        <span>Disable</span>
                    </label>
                </div>
                    
                    <div class="relative inline-flex align-middle gap-3 mt-4">
                        <button type="submit" name="save" id="save" class="btn btn-success">Save </button>
                        <button type="button" class="btn btn-danger" 
                        onclick="window.location='city.php'">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>