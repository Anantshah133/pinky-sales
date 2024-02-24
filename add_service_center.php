<?php
include "header.php";

if(isset($_COOKIE['viewId'])){
    $mode = 'view';
    $viewId = $_COOKIE['viewId'];
    $stmt = $obj->con1->prepare("SELECT sc1.*, c1.ctnm AS city, s1.id AS state_id FROM service_center sc1, city c1, state s1 WHERE c1.srno=sc1.area AND c1.state_id=s1.id AND sc1.id=?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_COOKIE['editId'])){
    $mode = 'edit';
    $editId = $_COOKIE['editId'];
    $stmt = $obj->con1->prepare("SELECT sc1.*, c1.ctnm AS city, s1.id AS state_id FROM service_center sc1, city c1, state s1 WHERE c1.srno=sc1.area AND c1.state_id=s1.id AND sc1.id=?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_REQUEST['update'])){
    $name = $_REQUEST["name"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["contact_num"];
    $user_id = $_REQUEST["userid"];
    $pass = $_REQUEST["password"];
    $status = $_REQUEST["default_radio"];
    $address = $_REQUEST["address"];
    $state = $_REQUEST["state"];
    $city = $_REQUEST['cityName'];
    $date_time = date("d-m-Y h:i A");

    $stmt = $obj->con1->prepare(
        "UPDATE service_center SET name=?, email=?, contact=?, userid=?, password=?, status=?, address=?, area=?, date_time=? WHERE id=?"
    );
    $stmt->bind_param("sssssssisi",$name,$email,$contact,$user_id,$pass,$status,$address,$city,$date_time,$editId);
    $Resp = $stmt->execute();

    if ($Resp) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:service_center.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:service_center.php");
    }
}

if (isset($_REQUEST["save"])) {
    $name = $_REQUEST["name"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["contact_num"];
    $user_id = $_REQUEST["userid"];
    $pass = $_REQUEST["password"];
    $status = $_REQUEST["default_radio"];
    $address = $_REQUEST["address"];
    $city = $_REQUEST['cityName'];
    $date_time = date("d-m-Y h:i A");

    echo "INSERT INTO `service_center`(`name`,`email`,`contact`,`userid`,`password`,`status`,`address`,`area`,`date_time`) VALUES ($name,$email,$contact,$user_id,$pass,$status,$address,$city,$date_time)";

    try {
        $stmt = $obj->con1->prepare(
            "INSERT INTO `service_center`(`name`,`email`,`contact`,`userid`,`password`,`status`,`address`,`area`,`date_time`) VALUES (?,?,?,?,?,?,?,?,?)"
        );
        $stmt->bind_param("sssssssis",$name,$email,$contact,$user_id,$pass,$status,$address,$city,$date_time);
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
        header("location:service_center.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:service_center.php");
    }
}
?>

<div class='p-6'>

    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Service Center -
                <?php echo isset($mode) ? ($mode == 'edit' ? 'Edit' : 'View' ) : 'Add' ?>
            </h5>
        </div>
        <form class="space-y-5" method="post" id="mainForm">
            <div>
                <label for="name">Name</label>
                <input id="name" type="text" name="name" placeholder="Enter Name" class="form-input"
                    value="<?php echo (isset($mode)) ? $data['name'] : '' ?>" required
                    <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> />
            </div>
            <div>
                <label for="ctnEmail">Email Address</label>
                <input id="ctnEmail" type="email" name="email" placeholder="name@example.com" class="form-input" 
                pattern="^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$" title="Invalid Email Format"
                value="<?php echo (isset($mode)) ? $data['email'] : '' ?>" required
                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> />
            </div>
            <div>
                <label for="contact_num"> Contact </label>
                <div class="flex">
                    <div class="bg-[#eee] flex justify-center items-center ltr:rounded-l-md rtl:rounded-r-md px-3 font-semibold border ltr:border-r-0 rtl:border-l-0 border-[#e0e6ed] dark:border-[#17263c] dark:bg-[#1b2e4b]">
                        +91
                    </div>
                    <input name="contact_num" id="contact_num" type="tel" placeholder="1234567890"
                        class="form-input ltr:rounded-l-none rtl:rounded-r-none" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                        value="<?php echo (isset($mode)) ? $data['contact'] : '' ?>" <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> maxlength="10" minlength="10"
                        pattern="[0-9]+" title="Please enter numbers only" required /> 
                </div>
            </div>
            <div>
                <label for="address">Address</label>
                <textarea autocomplete="on" name="address" id="address" required class="form-textarea" rows="2"
                    <?php echo isset($mode) && $mode == 'view' ? 'readonly' : null?>><?php echo (isset($mode)) ? $data['address'] : ''; ?></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div>
                    <label for="stateId">State</label>
                    <select class="form-select text-white-dark" name="state" onchange="loadCities(this.value)"
                        id="stateId" <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?> required>
                        <option value="">Choose State</option>
                        <?php
                        $stmt = $obj->con1->prepare("SELECT * FROM `state` ");
                        $stmt->execute();
                        $Res = $stmt->get_result();
                        $stmt->close();
                        while ($result = mysqli_fetch_assoc($Res)) { 
                    ?>

                        <option value="<?php echo $result["id"]; ?>"
                            <?php echo isset($mode) && $data['state_id'] == $result['id'] ? 'selected' : '' ?>>
                            <?php echo $result["name"]; ?>
                        </option>

                        <?php } ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="cityName">City</label>
                    <select id="area_id" name="cityName" class="form-select text-white-dark"
                        <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?> required>
                        <option value=""><?php echo isset($mode) ? $data['city'] : 'Choose City' ?></option>
                    </select>
                </div>
            </div>
            <div>
                <label for="gridUID">Username</label>
                <input type="text" name="userid" placeholder="" class="form-input"
                    value="<?php echo (isset($mode)) ? $data['userid'] : '' ?>" required
                    <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> />
            </div>
            <div>
                <label for="gridpass">Password</label>
                <input type="password" name="password" placeholder="Enter Password" class="form-input"
                pattern=".{8,}" title="Password should be at least 8 characters long" value="<?php echo (isset($mode)) ? $data['password'] : '' ?>" required
                <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> />
            </div>
            <div>
                <label for="gridStatus">Status</label>
                <label class="inline-flex mr-3">
                    <input type="radio" name="default_radio"  <?php echo isset($mode) && $data['status'] == 'enable' ? 'checked' : '' ?> class="form-radio text-primary" value="enable" checked
                        required <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> />
                    <span>Enable</span>
                </label>
                <label class="inline-flex mr-3">
                    <input type="radio" name="default_radio" <?php echo isset($mode) && $data['status'] == 'disable' ? 'checked' : '' ?> class="form-radio text-danger" value="disable" required <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?>/>
                    <span>Disable</span>
                </label>
            </div>
            <div class="relative inline-flex align-middle gap-3 mt-4">
                        <!-- Save/Update button -->
                        <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save' ?>"
                            id="save" class="btn btn-success" onclick="return validateAndDisable()"
                            <?php echo isset($mode) && $mode == 'view' ? 'style="display:none;"' : '' ?>>
                            <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                        </button>
                        <!-- Close button -->
                        <button type="button" class="btn btn-danger" onclick="window.location='service_center.php'">
                            Close
                        </button>
                    </div>

        </form>
    </div>
</div>
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
                const ctid =". json_encode($data['area']) .";
                loadCities(stid, ctid);
            </script>
        ";
    }
?>

<?php
include "footer.php"; 
?>