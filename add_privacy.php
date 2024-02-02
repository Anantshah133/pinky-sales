<?php
include "header.php";
error_reporting(E_ALL);

if(isset($_REQUEST['save']) )
{
 
  $detail = $_REQUEST['quill_input'];
    $type = $_REQUEST['type'];


  try
  {
$stmt = $obj->con1->prepare("INSERT INTO `privacy_policy`(`detail`,`type`) VALUES (?,?)");
$stmt->bind_param("ss",$detail,$type);
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
    //   header("location:privacy_policy.php");
  }
  else
  {
 setcookie("msg", "fail",time()+3600,"/");
    //   header("location:privacy_policy.php");
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

    <!-- service-center table -->

    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Privacy Policy Add</h5>
        </div>
        <form class="space-y-5" id="privacy_form">
            <lable for="">Detail</lable>
            <div id="editor" name="detail" class="!mt-0"></div>
            <label for="groupFname">Type</label>

            <select class="form-select text-white-dark" required name="type">
                <option value="user">User</option>
                <option value="service">Service-center</option>
                <option value="technician">Technician</option>
            </select>
            <div class="relative inline-flex align-middle gap-5 mt-4">
                <button type="submit" name="save" id="save" class="btn btn-primary"
                    onclick="formSubmit('quill-input')">Save</button>
                <button type="button" class="btn btn-warning ">Close</button>
            </div>
    </div>

    <!-- <button id="normal-content" type="button" class="p-2 bg-danger text-white" onclick="test()">Test</button> -->
    <input type="hidden" name="quill_input" id="quill-input">
    </form>




</div>




<!-- script -->
<script src="assets/js/quill.js"></script>
<script>
document.addEventListener("alpine:init", () => {
    Alpine.data("form", () => ({
        tableData: {
            id: 1,
            name: 'John Doe',
            email: 'johndoe@yahoo.com',
            date: '10/08/2020',
            sale: 120,
            status: 'Complete',
            register: '5 min ago',
            progress: '40%',
            position: 'Developer',
            office: 'London'
        },
    }));
});




// quill-editor

var quill = new Quill('#editor', {
    theme: 'snow'
});
var toolbar = quill.container.previousSibling;
toolbar.querySelector('.ql-picker').setAttribute('title', 'Font Size');
toolbar.querySelector('button.ql-bold').setAttribute('title', 'Bold');
toolbar.querySelector('button.ql-italic').setAttribute('title', 'Italic');
toolbar.querySelector('button.ql-link').setAttribute('title', 'Link');
toolbar.querySelector('button.ql-underline').setAttribute('title', 'Underline');
toolbar.querySelector('button.ql-clean').setAttribute('title', 'Clear Formatting');
toolbar.querySelector('[value=ordered]').setAttribute('title', 'Ordered List');
toolbar.querySelector('[value=bullet]').setAttribute('title', 'Bullet List');

function formSubmit(ele) {
    let xyz = document.getElementById(ele);
    xyz.value = quill.root.innerHTML;
}
</script>



<?php
include "footer.php";
?>