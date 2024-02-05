<?php
include "header.php";
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
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">Service Type - Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post">
                <div>
                    <label for="groupFname"> Name</label>
                    <input id="groupFname" name="name" type="text" class="form-input" 
                    required value="<?php echo isset($mode) ? $data['name'] : '' ?>"
                    <?php echo isset($mode) == 'view' ? 'readonly' : '' ?> />
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
                    <div class="relative inline-flex align-middle gap-3 mt-4 <?php echo isset($mode) == 'view' ? 'hidden' : '' ?>">
                        <button type="submit" name="save" id="save" class="btn btn-success">Save </button>
                        <button type="button" class="btn btn-danger" onclick="window.location='service_type.php'"
                        >Close</button>
                    </div>
            </form>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>