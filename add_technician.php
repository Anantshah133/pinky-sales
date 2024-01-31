<?php
include "header.php";

Insert:

if(isset($_REQUEST['save']))
{
 
  $name = $_REQUEST['name'];
  $email = $_REQUEST['email'];
  $contact = $_REQUEST['contact'];
  $serviceCenterId = $_REQUEST['service_center'];
  $user_id = $_REQUEST['userid'];
  $pass = $_REQUEST['password']; 
  $status = $_REQUEST['default_radio'];
  $nm= $_FILES["f"]["name"];
 $sz= $_FILES["f"]["size"];
 $typ= $_FILES["f"]["type"];
 $src= $_FILES["f"]["tmp_name"];

  

  try
  {  
$stmt = $obj->con1->prepare("INSERT INTO `technician`(`name`,`email`,`contact`,'service_center',`userid`,`password`,`id_proof`,`status`) VALUES (?,?,?,?,?,?,?,?)");
$stmt->bind_param("ssssssss",$name,$email,$contact,$serviceCenterId ,$user_id,$pass,$nm,$status);
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
 setcookie("msg", "Technician added Sucessfully!",time()+3600,"/");

 
  echo $nm."".$typ."".$sz."<br/>Source=".$src;
 
 if(file_exists("photos\\".$nm))
 {
     echo "Sorry the file name is already in use!";	
 }
 else
 {
     move_uploaded_file($src,"photos\\".$nm);
     
     echo "file Uploaded!";
 }

      header("location:add-technician.php");
  }
  else
  {
 setcookie("msg", "fail",time()+3600,"/");
     header("location:add-technician.php");
  }
 }

?>

<div class='p-6'>
    <!-- <div>
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">Progress Table</h5>
        </div>
    </div>
    <div class="table-responsive border mb-5">
        <table>
            <thead class='border-b'>
                <tr class=''>
                    <th>#</th>
                    <th>Name</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody x-data='complaint'>
                <template x-for="item in tableData" :key="item.id">
                    <tr class='bg-white'>
                        <td x-text="item.id"></td>
                        <td x-text="item.name" class="whitespace-nowrap"></td>
                        <td class="p-3 border-b border-[#ebedf2] dark:border-[#191e3a] text-center">
                            <button type="button" x-tooltip="Edit">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button type="button" x-tooltip="Delete">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
   
        </table>
    </div> -->

    <!-- Service Center - Add form -->
    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Technician Add</h5>
        </div>

        <form class="space-y-5" method="post" enctype="multipart/form-data">
            <div>
                <label for="groupFname"> Name</label>
                <input id="groupFname" type="text" name="name" placeholder="Enter First Name" class="form-input" />
            </div>
            <div>
                <label for="ctnEmail">Email address</label>
                <input id="ctnEmail" type="email" name="email" placeholder="name@example.com" class="form-input"
                    required />
            </div>
            <div>
                <label for="groupFname">Contact</label>
                <input id="groupFname" type="text" name="contact" placeholder="" class="form-input" />
            </div>
            <div>
                <label for="groupFname"> Service Center</label>

                <select class="form-select text-white-dark">
                    <option>-none-</option>
                    <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `service_center` WHERE status='enable'"
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



            <div>
                <label for="gridUID">Userid</label>
                <input type="text" placeholder="" name="userid" class="form-input" required />
            </div>
            <div>
                <label for="gridpass">Password</label>
                <input type="password" placeholder="Enter Password" name="password" class="form-input" required />
            </div>
            <div>
                <label for="gridproof">Id Proof</label>
                <div class="relative inline-flex align-middle">
                    <button type="button" class="btn  ltr:rounded-r-none rtl:rounded-l-none">No Image</button>
                    <img src="" class="w-mt-8 hidden w-72" id="preview">
                    <p class="" id="errElement"><p>
                    <button type="button" class="btn btn-success ltr:rounded-l-none rtl:rounded-r-none">
                       <a href="" onclick=""> <i class="ri-upload-2-fill"></i></a>&nbsp;&nbsp;Add Image
                    </button>
                    <input type="file" name="f" onchange="readURL(this, preview, errElement)"/>
                </div>
            </div>

            <div>
                <label for="gridStatus">Status</label>
                <label class="inline-flex">
                    <input type="radio" name="default_radio" class="form-radio" checked  value="enable"/>
                    <span>Enable</span>
                </label>
                <label class="">
                    <input type="radio" name="default_radio" class="form-radio text-danger" value="disable" />
                    <span>Disable</span>
                </label>
            </div>

            <div class="relative inline-flex align-middle gap-3 mt-4">
                <button type="submit" class="btn btn-primary" name="save" id="save">Save
                </button>
                <button type="button" class="btn  btn-warning ">Close</button>
            </div>
        </form>
    </div>
</div>


<script>

function readURL(input, preview, errElement) {
    if (input.files && input.files[0]) {
        var filename = input.files[0].name;
        var reader = new FileReader();
        var extn = filename.split('.').pop().toLowerCase();
        var allowedExtns = ["jpg", "jpeg", "png", "bmp", "webp"];

        console.log(filename, reader, extn)

        if (allowedExtns.includes(extn)) {
            var reader = new FileReader();

            reader.onload = function(e) {
                document.querySelector('#' + preview).src = e.target.result;
                document.getElementById(preview).style.display = "block";
            };

            reader.readAsDataURL(input.files[0]);
            document.getElementById(errElement).innerHTML = "";
            document.getElementById('save_btn').disabled = false;
        } else {
            document.getElementById(preview).style.display = "none";
            document.getElementById(errElement).innerHTML = "Please Select Image Only";
            document.getElementById('save_btn').disabled = true;
        }
    }
}
</script>






<?php
include "footer.php";
?>