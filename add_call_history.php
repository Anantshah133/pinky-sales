<?php
include "header.php";

(isset($_REQUEST["save"])) {
    $complaint_no = $_REQUEST["complaint_no"];
    $service_center = $_REQUEST["service_center"];
    $technician = $_REQUEST["technician"];
    $parts_used= $_REQUEST["parts_used"];
    $call_type = $_REQUEST["call_type"];
    $service_charges = $_REQUEST["service_charges"];
    $parts_charges = $_REQUEST["parts_charges"];
    $status = $_REQUEST["status"];
    $reason = $_REQUEST['reason'];
    $date_time = date("d-m-Y h:i A");

    try {
        $stmt = $obj->con1->prepare(
            "INSERT INTO `call_history`(`complaint_no`,`service_center`,`technician`,`parts_used`,`call_type`,`service_charges`,`parts_charges`,`status`,`reason`,`date_time`) VALUES (?,?,?,?,?,?,?,?,?,?)"
        );
        $stmt->bind_param("siisssssss",$complaint_no,$service_center,$technician,$parts_used,$call_type,$service_charges,$parts_charges,$status,$reason,$date_time);
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
        header("location:add_call_allocation.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:add_call_allocation.php");
    }
}
?>

<div class='p-6'>

    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Call History-Add</h5>
        </div>
        <form class="space-y-5" method="post">
            <div>
                <label for="name">Complaint No.</label>
                <input id="name" type="text" name="name" placeholder="Enter Name" class="form-input" required />
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
                <!-- <input name="contact_num" id="contact_num" type="tel" class="form-input" value="<?php echo (isset($mode)) ? $data['contact'] : '' ?>" required
                     <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> maxlength="10" pattern="[0-9]+" title="Please enter numbers only" /> -->

                <div class="flex">
                    <div class="bg-[#eee] flex justify-center items-center ltr:rounded-l-md rtl:rounded-r-md px-3 font-semibold border ltr:border-r-0 rtl:border-l-0 border-[#e0e6ed] dark:border-[#17263c] dark:bg-[#1b2e4b]">
                        +91</div>
                    <input name="contact_num" id="contact_num" type="tel" placeholder="1234567890"
                        class="form-input ltr:rounded-l-none rtl:rounded-r-none" 
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                        value="<?php echo (isset($mode)) ? $data['contact'] : '' ?>"
                        <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> maxlength="10" minlength="10"
                        pattern="[0-9]+" title="Please enter numbers only" required />
                </div>
                </div>
                <div>
                    <label for="address">Address</label>
                    <textarea autocomplete="on" name="address" id="address" class="form-textarea" rows="2"
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
                        pattern="^(?=.[!@#$%^&])(?=.*[0-9]).{8,}$"
                        title="Password should be of atleast length 8 and should contain atleast 1 special character"
                        value="<?php echo (isset($mode)) ? $data['password'] : '' ?>" required
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

                <div
                    class="relative inline-flex align-middle gap-3 mt-4 <?php echo isset($mode) && $mode == 'view' ? 'hidden' : '' ?>">
                    <button type="submit" class="btn btn-success"
                        name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save'; ?>"
                        id="save"><?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save'; ?></button>
                    <button type="button" class="btn btn-danger"
                        onclick="window.location='service_type.php'">Close</button>
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