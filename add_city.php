<?php
include "header.php";

if (isset($_REQUEST["viewId"])) {
    $mode = 'view';
    $viewId = $_REQUEST["viewId"];
    $stmt = $obj->con1->prepare("SELECT * FROM `city` where srno=?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}
if (isset($_REQUEST["save"])) {
    $city_name = $_REQUEST["city_name"];
    $state_name = $_REQUEST["state_id"];
    $status = $_REQUEST["default_radio"];
    try {
        $stmt = $obj->con1->prepare(
            "INSERT INTO `city`(`ctnm`,`state_id`,`status`) VALUES (?,?,?)"
        );
        $stmt->bind_param("sis", $city_name, $state_name, $status);
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
        header("location:city.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:city.php");
    }
}
?>
<div class='p-6'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">
                City - <?php echo isset($mode) == 'view' ? 'View' : 'Add' ?>
            </h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post">
                <div>
                    <label for="groupFname">State Name</label>
                    <select class="form-select text-white-dark" name="state_id" 
                    <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?> required>
                        <option value="">Choose State</option>
                    <?php
                        $stmt = $obj->con1->prepare("SELECT * FROM `state`");
                        $stmt->execute();
                        $Resp = $stmt->get_result();
                        $stmt->close();

                        while ($result = mysqli_fetch_array($Resp)) { 
                    ?>
                        <option value="<?php echo $result["id"]; ?>"
                            <?php echo isset($viewId) && $data["state_id"] == $result["id"] ? "selected" : ""; ?> 
                        >
                            <?php echo $result["name"]; ?>
                        </option>
                    <?php }
                    ?>
                    </select>
                </div>
                <div>
                    <label for="city_name">City Name </label>
                    <input id="city_name" name="city_name" type="text" class="form-input" 
                    value="<?php echo isset($viewId) ? $data["ctnm"] : ""; ?>" 
                    <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?>
                    required/>
                </div>    
                <div>
                    <label for="gridStatus">Status</label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="enable" class="form-radio disabled:text-primary text-primary" 
                        <?php echo isset($viewId) && $data["status"] == "enable" ? "checked": ""; ?> 
                        <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?>
                        required />
                        <span>Enable</span>
                    </label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="disable" class="form-radio disabled:text-danger text-danger" 
                        <?php echo isset($viewId) && $data["status"] == "disable"? "checked": ""; ?> 
                        <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?>
                        required />
                        <span>Disable</span>
                    </label>
                </div>
                    
                    <div class="relative inline-flex align-middle gap-3 mt-4 <?php echo isset($viewId)? "hidden" : ""; ?>">
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