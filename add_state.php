<?php
include "header.php";


if(isset($_REQUEST['editId'])){
    $mode = 'edit';
    $editId = $_REQUEST['editId'];
    $stmt = $obj->con1->prepare("SELECT * FROM `state` WHERE id=?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_REQUEST['viewId'])){
    $mode = 'view';
    $viewId = $_REQUEST['viewId'];
    $stmt = $obj->con1->prepare("SELECT * FROM `state` WHERE id=?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_REQUEST['update'])){
    $editId = $_REQUEST['editId'];
    $name = $_REQUEST["name"];

    $stmt = $obj->con1->prepare("UPDATE `state` SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $editId);
    $Res = $stmt->execute();
    $stmt->close();

    if ($Res) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:state.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:state.php");
    }
    exit();
}

if (isset($_REQUEST["save"])) {
    $name = $_REQUEST["name"];

    try {
        $stmt = $obj->con1->prepare("INSERT INTO `state`(`name`) VALUES (?)");
        $stmt->bind_param("s", $name);
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
        header("location:state.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:state.php");
    }
    exit();
}
?>
<div class='p-6' >
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">State - 
                <?php echo isset($mode) ? ($mode == 'edit' ? 'Edit' : 'View') : 'Add' ?>
            </h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post" id="mainForm">
                <div>
                    <label for="groupFname">Name </label>
                    <input id="groupFname" name="name" type="text" class="form-input" onblur="checkName(this)" pattern="^\S+$"
                        value="<?php echo (isset($mode)) ? $data['name'] : '' ?>" required
                        <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?>
                    />
                    <span id="demo"><span>
                    <div class="relative inline-flex align-middle gap-3 mt-4 <?php echo isset($mode) && $mode == 'view' ? 'hidden' : '' ?>">
                        <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save' ?>" id="save" class="btn btn-success" onclick="return validateAndDisable()"><?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?></button>
                        <button type="button" class="btn btn-danger" onclick="window.location='state.php'">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function checkName(c1){
        n=c1.value;
        //alert(name);
        const obj=new XMLHttpRequest();
        obj.onload=function(){
            x=obj.responseText;

            if(x==1)
            {
                c1.value="";
                c1.focus();
                document.getElementById("demo").innerHTML="Sorry the name alredy exist!";
                
            }
            else{
                document.getElementById("demo").innerHTML="";
            }
        }
        obj.open("GET","check_state.php?name="+n,true);
        obj.send();
    }

</script>

<?php 
include "footer.php";
?>
