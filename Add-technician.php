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

  try
  {  
$stmt = $obj->con1->prepare("INSERT INTO `technician`(`name`,`email`,`contact`,`userid`,`password`,`status`) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("ssssss",$name,$email,$contact,$user_id,$pass,$status);
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

        <form class="space-y-5" method="post">
            <div>
                <label for="groupFname"> Name</label>
                <input id="groupFname" type="text" name="name" placeholder="Enter First Name" class="form-input" />
            </div>
            <div>
                <label for="ctnEmail">Email address</label>
                <input id="ctnEmail" type="email" name="email" placeholder="name@example.com" class="form-input" required />
            </div>
            <div>
                <label for="groupFname">Contact</label>
                <input id="groupFname" type="text" name="contact" placeholder="" class="form-input" />
            </div>
            <div>
                <label for="groupFname"> Service Center</label>

                <select class="form-select text-white-dark">
                    <option>-none-</option>
                    <option>MIRA BHAYANDER</option>
                    <option>N H SERVICE</option>
                    <option>NO SERVICE</option>
                    <option>PALGHAR</option>
                    <option>Test Service center</option>
                    <option>VIRAR NSP VASAI</option>
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

                    <button type="button" class="btn btn-success ltr:rounded-l-none rtl:rounded-r-none"><a href=""><i
                                class="ri-upload-2-fill"></i></a>&nbsp;&nbsp;Add Image</button>
                </div>
            </div>

            <div>
                <label for="gridStatus">Status</label>
                <label class="inline-flex">
                    <input type="radio" name="default_radio" class="form-radio" checked />
                    <span>Enable</span>
                </label>
                <label class="">
                    <input type="radio" name="default_radio" class="form-radio text-danger" />
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









<?php
include "footer.php";
?>