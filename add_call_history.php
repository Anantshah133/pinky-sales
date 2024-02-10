<?php
include "header.php";

if(isset($_REQUEST["save"])) {
    $complaint_no = $_REQUEST["complaint_no"];
    $service_center = $_REQUEST["service_center"];
    $technician = $_REQUEST["technician"];
    $parts_used= $_REQUEST["parts_used"];
    $call_type = $_REQUEST["call_type"];
    $service_charge = $_REQUEST["service_charge"];
    $parts_charge = $_REQUEST["parts_charge"];
    $status = $_REQUEST["status"];
    $reason = $_REQUEST['reason'];
    $date_time = date("d-m-Y h:i A");

    try {
        $stmt = $obj->con1->prepare(
            "INSERT INTO `call_history`(`complaint_no`,`service_center`,`technician`,`parts_used`,`call_type`,`service_charge`,`parts_charge`,`status`,`reason`,`date_time`) VALUES (?,?,?,?,?,?,?,?,?,?)"
        );
        $stmt->bind_param("siisssssss",$complaint_no,$service_center,$technician,$parts_used,$call_type,$service_charge,$parts_charge,$status,$reason,$date_time);
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
        // header("location:add_call_allocation.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        // header("location:add_call_allocation.php");
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
                <input id="name" type="text" name="complaint_no" placeholder="" class="form-input" 
                 value="<?php echo isset($mode) ? $data['complaint_no'] : ''?>" required />
            </div>
            <div>
                <label for="groupFname"> Service Center</label>
                <select class="form-select text-white-dark" name="service_center" required>
                    <option value="">Choose...</option>
                    <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `service_center` WHERE status='enable'"
                            );
                            $stmt->execute();
                            $Res = $stmt->get_result();
                            $stmt->close();

                            while ($result = mysqli_fetch_assoc($Res)) { 
                        ?>
                    <option value="<?php echo $result["id"]; ?>"><?php echo $result["name"]; ?></option>
                    <?php 
                            } 
                        ?>
                </select>
            </div>
            <div>
                <label for="groupFname"> Technician</label>
                <select class="form-select text-white-dark" name="technician" required>
                    <option value="">Choose...</option>
                    <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `technician` WHERE status='enable'"
                            );
                            $stmt->execute();
                            $Res = $stmt->get_result();
                            $stmt->close();

                            while ($result = mysqli_fetch_assoc($Res)) { 
                        ?>
                    <option value="<?php echo $result["id"]; ?>"><?php echo $result["name"]; ?></option>
                    <?php 
                            } 
                        ?>
                </select>
            </div>
            <div>
                <label for="groupFname"> Parts Used</label>
                <select class="form-select text-white-dark" name="parts_used" required>
                    <option value="part call" <?php echo isset($mode) && $data['parts_used'] == 'new' ? 'selected' : '' ?>>
                        Part Call
                    </option>
                    <option value="non_part call"
                        <?php echo isset($mode) && $data['Parts_used'] == 'pending' ? 'selected' : '' ?>>
                        Non-Part Call
                    </option>
                </select>
            </div>
            <div>
                <label for="groupFname"> Call Type</label>
                <select class="form-select text-white-dark" name="call_type" required>
                    <option value="warranty" <?php echo isset($mode) && $data['status'] == 'new' ? 'selected' : '' ?>>
                        Warranty
                    </option>
                    <option value="out of warranty"
                        <?php echo isset($mode) && $data['status'] == 'pending' ? 'selected' : '' ?>>
                        Out Of Warranty
                    </option>
                </select>
            </div>
            <div>
                <label for="name">Service Charge</label>
                <input id="name" type="text" name="service_charge" placeholder="" class="form-input" required />
            </div>
            <div>
                <label for="name">Parts Charge</label>
                <input id="name" type="text" name="parts_charge" placeholder="" class="form-input" required />
            </div>
            <div>
                <label for="call_status">Status</label>
                <select name="status" id="status" class="form-select text-white-dark" required
                    <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?>>
                    <option value="">Choose Status</option>
                    <option value="new" <?php echo isset($mode) && $data['status'] == 'new' ? 'selected' : '' ?>>
                        New
                    </option>
                    <option value="pending"
                        <?php echo isset($mode) && $data['status'] == 'pending' ? 'selected' : '' ?>>
                        Pending
                    </option>
                    <option value="cancelled"
                        <?php echo isset($mode) && $data['status'] == 'cancelled' ? 'selected' : '' ?>>
                        Cancelled
                    </option>
                    <option value="closed" <?php echo isset($mode) && $data['status'] == 'closed' ? 'selected' : '' ?>>
                        Closed
                    </option>
                    <option value="allocated"
                        <?php echo isset($mode) && $data['status'] == 'allocated' ? 'selected' : '' ?>>
                        Allocated
                    </option>
                </select>
            </div>
            <div>
                <label for="rson">Reason </label>
                <input id="rson" name="reason" type="text" class="form-input" required
                    <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>
                    value="<?php echo isset($mode) ? $data['reason'] : "" ?>" />
            </div>

            <div
                class="relative inline-flex align-middle gap-3 mt-4">
                <button type="submit" class="btn btn-success" name="save" id="save">Save</button>
                <button type="button" class="btn btn-danger" onclick="window.location='call_alloction.php'">Close</button>
            </div>
        </form>
    </div>
</div>


<?php
include "footer.php"; 
?>