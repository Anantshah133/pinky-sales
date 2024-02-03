<?php
include "header.php";
if(isset($_REQUEST['viewId'])){
    $viewId = $_REQUEST['viewId'];
    $stmt = $obj->con1->prepare("SELECT * FROM `area_pincode` where srno=?");
    $stmt->bind_param("i",$viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}
if(isset($_REQUEST['save']))
{
  $state_name = $_REQUEST['State_id'];
  $city_name = $_REQUEST['area_id'];
  $pincode = $_REQUEST['pincode'];
 
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `area_pincode`(`state_id`,`city_id`,`pincode`) VALUES (?,?,?)");
    $stmt->bind_param("iis",$state_name,$city_name,$pincode);
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
    setcookie("msg", "data", time()+3600,"/");
    header("location:area_pincode.php");
  }
  else
  {
    setcookie("msg", "fail", time()+3600,"/");
    header("location:area_pincode.php");
  }
}
?>

<div class='p-6'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">Area Pincode- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post">
                <div>
                    
                    <label for="groupFname"> State Name</label>
                    <select class="form-select text-white-dark" onchange="loadCities(this.value)" name="State_id" required>
                        <option value="">Choose State</option>
                    <?php 
                        $stmt = $obj->con1->prepare("SELECT * FROM `state`");
                        $stmt->execute();
                        $Resp=$stmt->get_result();
                        $stmt->close();

                        while($result = mysqli_fetch_array($Resp)){
                    ?>
                        <option value="<?php echo $result['id'] ?>"><?php echo $result['name'] ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div>
                    
                    <label for="groupFname">City Name</label>

                    <select class="form-select text-white-dark" name="area_id" id="area_id" required>
                        <option value="">Choose City</option>
                    <?php 
                        $stmt = $obj->con1->prepare("SELECT * FROM `city`");
                        $stmt->execute();
                        $Resp=$stmt->get_result();
                        $stmt->close();

                        while($result = mysqli_fetch_array($Resp)){
                    ?>
                        <option value="<?php echo $result['srno'] ?>"><?php echo $result['ctnm'] ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div>
                    <label for="groupFname"> Pincode </label>
                    <input id="groupFname" name="pincode" type="text" class="form-input" />
                    <div class="relative inline-flex align-middle gap-3 mt-4">
                        <button type="submit" name="save" id="save" class="btn btn-success">Save </button>
                        <button type="button" class="btn btn-danger" onclick="window.location='area_pincode.php'">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadCities(stid) {
    const xhttp = new XMLHttpRequest();
    xhttp.open("GET", "getcities.php?sid=" + stid);
    xhttp.send();
    xhttp.onload = function() {
        document.getElementById("area_id").innerHTML = xhttp.responseText;
    }
}
</script>
<?php
include "footer.php";
?>