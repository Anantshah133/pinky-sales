<?php
include "header.php";
Insert:

if(isset($_REQUEST['save']))
{
 
  $name = $_REQUEST['name'];
  $email = $_REQUEST['email'];
  $contact = $_REQUEST['contact'];
  $user_id = $_REQUEST['userid'];
  $pass = $_REQUEST['password']; 
  $status = $_REQUEST['default_radio'];
  $address=$_REQUEST['address'];
  $state = $_REQUEST['state'];

  try
  {
    // echo "INSERT INTO `service_center`(`name`,`email`,`contact`,`userid`,`password`,`status`,`address`,`area`) VALUES ($name,$email,$contact,$user_id,$pass,$status,$address,$state)";
    $stmt = $obj->con1->prepare("INSERT INTO `service_center`(`name`,`email`,`contact`,`userid`,`password`,`status`,`address`,`area`) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssi",$name,$email,$contact,$user_id,$pass,$status,$address,$state);
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
      header("location:service_center.php");
  }
  else
  {
 setcookie("msg", "fail",time()+3600,"/");
      header("location:service_center.php");
  }
 }


?>

<div class='p-6'>

    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Service Center Add</h5>
        </div>
        <form class="space-y-5" method="post">
            <div>
                <label for="groupFname"> Name</label>
                <input id="groupFname" type="text" name="name" placeholder="Enter First Name" class="form-input" />
            </div>
            <div>
                <label for="ctnEmail">Email Address</label>
                <input id="ctnEmail" type="email" name="email" placeholder="name@example.com" class="form-input" required />
            </div>
            <div>
                <label for="groupFname">Contact</label>
                <input id="groupFname" type="text" name="contact" placeholder="" class="form-input" />
            </div>

            <div>
                <label for="gridAddress1">Address</label>
                <input id="gridAddress1" type="text" name="address" placeholder="Enter Address" value="" class="form-input" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div>
                    <label for="gridState">State</label>
                    <select id="gridState" class="form-select text-white-dark"  name="state" onchange="loadCities(this.value)">
                        <option>Choose...</option>
                        <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `service_area` "
                            );
                            $stmt->execute();
                            $Res = $stmt->get_result();
                          //  $stmt->close();

                            while ($result = mysqli_fetch_assoc($Res)) { 
                        ?>
                                <option value="<?php echo $result["id"]; ?>"><?php echo $result["name"]; ?></option>
                                <?php 
                            } 
                        ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="gridCity">City</label>
                    <select id="gridcity" class="form-select text-white-dark" >
                        <option>Choose...</option>
                        
                    </select>
                </div>

                <div>
                    <label for="gridZip">Zip</label>
                    <input id="gridZip" type="text" placeholder="Enter Zip" class="form-input" />
                </div>
            </div>
            <div>
                <label for="gridUID">Userid</label>
                <input type="text" name="userid" placeholder="" class="form-input" required />
            </div>
            <div>
                <label for="gridpass">Password</label>
                <input type="password" name="password" placeholder="Enter Password" class="form-input" required />
            </div>

            <div>
                <label for="gridStatus">Status</label>
                <label class="inline-flex">
                    <input type="radio" name="default_radio" class="form-radio text-success"  value="enable" checked />
                    <span>Enable</span>
                </label>
                <label class="">
                    <input type="radio" name="default_radio" class="form-radio text-danger"  value="disable" />
                    <span>Disable</span>
                </label>
            </div>

            <div class="relative inline-flex align-middle gap-3 mt-4">
                <button type="submit" class="btn btn-primary" name="save" id="save" >Save
                </button>
                <button type="button" class="btn btn-warning ">Close</button>
            </div>
        </form>


    </div>
</div>
<?php
include "footer.php";
?>

<script>
    function loadCities(stid) 
	{
		
		// alert(stid);
  		const xhttp = new XMLHttpRequest();
  		
  		xhttp.open("GET","getcities.php?sid="+stid);
  		xhttp.send();
		xhttp.onload = function()
		{
   			 document.getElementById("gridcity").innerHTML = xhttp.responseText;
           
		}
	}

</script>