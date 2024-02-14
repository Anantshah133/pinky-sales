<?php
include "header.php";

if(isset($_REQUEST['editId'])){
    $mode = 'edit';
    $editId = $_REQUEST['editId']; //14
    
    $qry = $obj->con1->prepare("SELECT * FROM `service_type` WHERE id=?");
    $qry->bind_param("i", $editId);
    $qry->execute();
    $Res = $qry->get_result();
    $data = $Res->fetch_assoc();
    $qry->close();
}

if(isset($_REQUEST['viewId'])){
    $mode = 'view';
    $viewId = $_REQUEST['viewId'];
    $qry = $obj->con1->prepare("SELECT * FROM `service_type` WHERE id=?");
    $qry->bind_param("i", $viewId);
    $qry->execute();
    $Res = $qry->get_result();
    $data = $Res->fetch_assoc();
    $qry->close();
}

if(isset($_REQUEST['update'])){
    $name = $_REQUEST["name"];
    $status = $_REQUEST["default_radio"];

    $qry = $obj->con1->prepare("UPDATE `service_type` SET name=?, status=? WHERE id=?");
    $qry->bind_param("ssi", $name, $status, $editId);
    $Res = $qry->execute();
    $qry->close();

    if ($Res) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:service_type.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:service_type.php");
    }
}

if (isset($_REQUEST["save"])) {
    $name = $_REQUEST["name"];
    $status = $_REQUEST["default_radio"];

    try {
        $stmt = $obj->con1->prepare("INSERT INTO `service_type`(`name`,`status`) VALUES (?,?)");
        $stmt->bind_param("ss", $name, $status);
        $Resp = $stmt->execute();
        if (!$Resp) {
            throw new Exception(
                "Problem in adding! " . strtok($obj->con1->error, "(")
            );
        }
        $stmt->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "data", time() + 3600, "/");
        header("location:service_type.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:service_type.php");
    }
}
?>
<div class='p-6' >
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">Service Type - 
                <?php echo isset($mode) ? ($mode == 'edit' ? 'Edit' : 'View') : 'Add' ?>
            </h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post" id="mainForm">
                <div>
                    <label for="groupFname"> Name</label>
                    <input id="groupFname" name="name" type="text" class="form-input" onblur="checkServicetype(this)"
                    required value="<?php echo isset($mode) ? $data['name'] : '' ?>"
                    <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
                    <p class="mt-3 text-danger text-base font-bold" id="demo"></p>
                </div>
                <div>
                    <label for="gridStatus">Status</label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="enable" class="form-radio" checked required 
                            <?php echo isset($mode) && $data["status"] == "enable" ? "checked": ""; ?> 
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?>
                        />
                        <span>Enable</span>
                    </label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="disable" class="form-radio text-danger" required 
                            <?php echo isset($mode) && $data["status"] == "disable" ? "checked": ""; ?> 
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?>
                        />
                        <span>Disable</span>
                    </label>
                </div>
                    <div class="relative inline-flex align-middle gap-3 mt-4 <?php echo isset($mode) && $mode == 'view' ? 'hidden' : '' ?>">
                        <button type="submit" name="<?php echo isset($mode) == 'edit' ? 'update' : 'save' ?>" id="save" class="btn btn-success" onclick="return validateAndDisable()"><?php echo isset($mode) == 'edit' ? 'Update' : 'Save'?></button>
                        <button type="button" class="btn btn-danger" onclick="window.location='service_type.php'"
                        >Close</button>
                    </div>
            </form>
        </div>
    </div>
</div>
<script>
    function checkServicetype(c1){
        let n = c1.value;
        const obj = new XMLHttpRequest();
        obj.onload = function(){
            let x = obj.responseText;

            if(x>=1)
            {
                c1.value="";
                c1.focus();
                document.getElementById("demo").innerHTML = "Sorry the name alredy exist!";
            }
            else{
                document.getElementById("demo").innerHTML = "";
            }
        }
        obj.open("GET","./ajax/check_servicetype.php?name="+n,true);
        obj.send();
    }

</script>

<?php
include "footer.php";
?>