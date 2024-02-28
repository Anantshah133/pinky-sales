<?php
include "header.php";

if (isset($_COOKIE['viewId'])) {
    $mode = 'view';
    $viewId = $_COOKIE['viewId'];
    $stmt = $obj->con1->prepare("SELECT * FROM `city` where srno=?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_COOKIE['editId'])){
    $mode = 'edit';
    $editId = $_COOKIE['editId'];
    $stmt = $obj->con1->prepare("SELECT * FROM `city` where srno=?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_REQUEST['update'])){
    $editId = $_COOKIE['editId'];
    $state_id = $_REQUEST["state_id"];
    $city_name = $_REQUEST['city_name'];
    $status = $_REQUEST["default_radio"];

    $stmt = $obj->con1->prepare("UPDATE `city` SET ctnm=?, state_id=?, status=? WHERE srno=?");
    $stmt->bind_param("sisi", $city_name, $state_id, $status, $editId);
    $Res = $stmt->execute();
    $stmt->close();

    if ($Res) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:city.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:city.php");
    }
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
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">
                City - <?php echo isset($mode) ? ($mode == 'view' ? 'View' : ($mode == 'edit' ? 'Edit' : 'Add')) : 'Add'; ?>
            </h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post" id="mainForm">
                <div>
                    <label for="groupFname">State Name</label>
                    <select class="form-select text-gray-500" name="state_id" id="state_id"
                    <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?> required>
                        <option value="">Choose State</option>
                        <?php
                            $stmt = $obj->con1->prepare("SELECT * FROM `state` WHERE name!='no state'");
                            $stmt->execute();
                            $Resp = $stmt->get_result();
                            $stmt->close();

                            while ($result = mysqli_fetch_array($Resp)) { 
                        ?>
                            <option value="<?php echo $result["id"]; ?>"
                                <?php echo isset($mode) && $data["state_id"] == $result["id"] ? "selected" : ""; ?> 
                            >
                                <?php echo $result["name"]; ?>
                            </option>
                        <?php 
                            }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="city_name">City Name </label>
                    <input id="city_name" name="city_name" type="text" class="form-input" onblur="checkCity(this, <?php echo isset($mode) ? $data['srno'] : -1 ?>)"
                    value="<?php echo isset($mode) ? $data["ctnm"] : ""; ?>" pattern="^\s*\S.*$" <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?>
                    required />
                    <p class="mt-3 text-danger text-base font-bold" id="demo"></p>
                </div>    
                <div>
                    <label for="gridStatus">Status</label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="enable" class="form-radio disabled:text-primary text-primary" checked
                            <?php echo isset($mode) && $data["status"] == "enable" ? "checked": ""; ?> 
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?>
                        required />
                        <span>Enable</span>
                    </label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="disable" class="form-radio disabled:text-danger text-danger" 
                            <?php echo isset($mode) && $data["status"] == "disable"? "checked": ""; ?> 
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?>
                        required />
                        <span>Disable</span>
                    </label>
                </div>
                    
                    <div class="relative inline-flex align-middle gap-3 mt-4 <?php echo isset($viewId)? "hidden" : ""; ?>">
                        <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save' ?>" id="save" class="btn btn-success" onclick="return localValidate()"><?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?> </button>
                        <button type="button" class="btn btn-danger" 
                        onclick="window.location='city.php'">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function localValidate(){
        let form = document.getElementById('mainForm');
        let submitButton = document.getElementById('save');
        let city = document.getElementById('city_name');

        if(form.checkValidity() && checkCity(city, <?php echo isset($mode) ? $data['srno'] : -1 ?>)){
            setTimeout(() => {
                submitButton.disabled = true;
            }, 0);
            return true;
        }
    }

    function checkCity(c1, id){
        let n = c1.value;
        var state_id = document.getElementById('state_id').value;
        let cityId = id;

        const obj = new XMLHttpRequest();
        obj.open("GET",`./ajax/check_city.php?city_name=${n}&state_id=${state_id}&ctid=${cityId}`, false);
        obj.send();

        if(obj.status == 200){
            let x = obj.responseText;
            if(x>=1){
                c1.value="";
                c1.focus();
                document.getElementById("demo").innerHTML = "Sorry the name alredy exist!";
                return false;
            } else{  
                document.getElementById("demo").innerHTML = "";
                return true;
            }
        }
    }

</script>


<?php
include "footer.php";
?>