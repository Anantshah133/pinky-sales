<?php
include "header.php";
if (isset($_COOKIE['viewId'])) {
    $mode = 'view';
    $viewId = $_COOKIE['viewId']; // currently 15
    $stmt = $obj->con1->prepare("SELECT a1.*, s1.name AS state, c1.ctnm AS city FROM `area_pincode` a1, `state` s1, `city` c1 WHERE a1.id=? AND a1.state_id=s1.id AND a1.city_id=c1.srno");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_COOKIE['editId'])){
    $mode = 'edit';
    $editId = $_COOKIE['editId'];
    $stmt = $obj->con1->prepare("SELECT a1.*, s1.name AS state, c1.ctnm AS city FROM `area_pincode` a1, `state` s1, `city` c1 WHERE a1.id=? AND a1.state_id=s1.id AND a1.city_id=c1.srno");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_REQUEST['update'])){
    $editId = $_COOKIE['editId'];
    $state_name = $_REQUEST["state_id"];
    $city_name = $_REQUEST["area_id"];
    $pincode = $_REQUEST["pincode"];

    $stmt = $obj->con1->prepare("UPDATE `area_pincode` SET state_id=?, city_id=?, pincode=? WHERE id=?");
    $stmt->bind_param("iisi", $state_name, $city_name, $pincode, $editId);
    $Res = $stmt->execute();
    $stmt->close();

    if ($Res) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:area_pincode.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:area_pincode.php");
    }
}

if (isset($_REQUEST["save"])) {
    $state_name = $_REQUEST["state_id"];
    $city_name = $_REQUEST["area_id"];
    $pincode = $_REQUEST["pincode"];

    try {
        $stmt = $obj->con1->prepare(
            "INSERT INTO `area_pincode`(`state_id`,`city_id`,`pincode`) VALUES (?,?,?)"
        );
        $stmt->bind_param("iis", $state_name, $city_name, $pincode);
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
        header("location:area_pincode.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:area_pincode.php");
    }
}
?>

<div class='p-6'>
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">Area Pincode - 
                <?php echo isset($mode) ? ($mode == 'edit' ? 'Edit' : 'View' ) : 'Add' ?>
            </h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post" id="mainForm">
                <div>
                    <label for="groupFname">State Name</label>
                    <select class="form-select text-white-dark" onchange="loadCities(this.value)" name="state_id" id="stateId" required <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?>>
                        <option value="">Choose State</option>
                        <?php
                            $stmt = $obj->con1->prepare("SELECT * FROM `state`");
                            $stmt->execute();
                            $Resp = $stmt->get_result();
                            $stmt->close();
                            while ($result = mysqli_fetch_array($Resp)) { 
                        ?>
                            <option value="<?php echo $result["id"]; ?>" <?php echo isset($mode) && $result['id'] == $data['state_id'] ? 'selected' : '' ?>>
                                <?php echo $result["name"]; ?>
                            </option>
                        <?php 
                            }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="groupFname">City Name</label>
                    <select class="form-select text-white-dark" name="area_id" id="area_id" 
                    required <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?>>
                        <option value="">
                            <?php echo isset($mode) && $mode == 'view' ? $data['city'] : 'Choose City'; ?>
                        </option>
                    </select>
                </div>
                <div>
                    <label for="groupFname"> Pincode </label>
                    <input id="groupFname" name="pincode" type="text" class="form-input" onblur="checkName(this)"  pattern="^[1-9][0-9]{5}$" title="enter valid pincode" maxlength="6" 
                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                    <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> value="<?php echo isset($mode) ? $data['pincode'] : '' ?>" required />
                    <p class="mt-3 text-danger text-base font-bold" id="demo"></p>
                </div>
                <div class="relative inline-flex align-middle gap-3 mt-4">
                        <!-- Save/Update button -->
                        <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save' ?>"
                            id="save" class="btn btn-success" 
                            <?php echo isset($mode) && $mode == 'view' ? 'style="display:none;"' : '' ?>>
                            <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                        </button>
                        <!-- Close button -->
                        <button type="button" class="btn btn-danger" onclick="window.location='area_pincode.php'">
                            Close
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script>
    function checkName(c1){
        let n = c1.value;
        const obj = new XMLHttpRequest();
        obj.onload = function(){
            let x = obj.responseText;
            if(x==1)
            {
                c1.value="";
                c1.focus();
                document.getElementById("demo").innerHTML = "Sorry the pincode alredy exist!";
            }
            else{
                document.getElementById("demo").innerHTML = "";
            }
        }
    } else {
        // Handle errors
        return false;
    }
}

</script>
<script>
    function loadCities(stid, ctid = 0) {
        const xhttp = new XMLHttpRequest();
        xhttp.open("GET", `getcities.php?sid=${stid}&ctid=${ctid}`);
        xhttp.send();
        xhttp.onload = function() {
            document.getElementById("area_id").innerHTML = xhttp.responseText;
        }
    }
</script>
<?php 
    if(isset($mode) && $mode == 'edit'){
        echo "
            <script>
                const stid = document.getElementById('stateId').value;
                const ctid =". json_encode($data['city_id']) .";
                loadCities(stid, ctid);
            </script>
        ";
    }
  
?>
<?php
include "footer.php";
?>