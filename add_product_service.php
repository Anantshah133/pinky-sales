<?php
include "header.php";

if (isset($_COOKIE['viewId'])) {
    $mode = 'view';
    $viewId = $_COOKIE['viewId'];
    $stmt = $obj->con1->prepare("SELECT ps1.*, pc1.name AS product_category, s1.name AS service_type FROM product_service ps1, product_category pc1, service_type s1 WHERE ps1.srno=? AND ps1.pid=pc1.id AND ps1.sid=s1.id");
    $stmt->bind_param('i', $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if (isset($_COOKIE['editId'])) {
    $mode = 'edit';
    $editId = $_COOKIE['editId'];
    $stmt = $obj->con1->prepare("SELECT ps1.*, pc1.name AS product_category, s1.name AS service_type FROM product_service ps1, product_category pc1, service_type s1 WHERE ps1.srno=? AND ps1.pid=pc1.id AND ps1.sid=s1.id");
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_REQUEST['update'])){
    $editId = $_COOKIE['editId'];
    $product = $_REQUEST["product_id"];
    $service = $_REQUEST["service_id"];
    $status = $_REQUEST["default_radio"];
    
    $stmt = $obj->con1->prepare("UPDATE `product_service` SET pid=?, sid=?, status=? WHERE srno=?");
    $stmt->bind_param("iisi", $product, $service, $status, $editId);
    $Res = $stmt->execute();
    $stmt->close();
    
    if ($Res) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:product_service.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:product_service.php");
    }
}

if (isset($_REQUEST["save"])) {
    $product = $_REQUEST["product_id"];
    $service = $_REQUEST["service_id"];
    $status = $_REQUEST["default_radio"];
    try {
        $stmt = $obj->con1->prepare(
            "INSERT INTO `product_service`(`pid`,`sid`,`status`) VALUES (?,?,?)"
        );
        $stmt->bind_param("iis", $product, $service, $status);
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
        header("location:product_service.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:product_service.php");
    }
}
?>
<div class='p-6'>
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Product-Service - <?php echo isset($mode) ? ($mode == 'edit' ? 'Edit' : 'View') : 'Add' ?></h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post" id="mainForm">
                <div>
                    <label for="groupFname">Product</label>
                    <select class="form-select text-white-dark" name="product_id" id="product_id" required onchange="<?php echo isset($mode) ? '':'get_service_list(this.value)' ?>"
                        <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?>>
                        <option value="">Choose Product</option>
                        <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `product_category`"
                            );
                            $stmt->execute();
                            $Resp = $stmt->get_result();
                            $stmt->close();

                            while ($result = mysqli_fetch_array($Resp)) { 
                        ?>
                            <option value="<?php echo $result["id"]; ?>" 
                            <?php echo isset($mode) && $data["pid"] == $result["id"] ? "selected" : ""; ?> >
                                <?php echo $result["name"]; ?>
                            </option>
                        <?php 
                            }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="groupFname">Service</label>
                    <select class="form-select text-white-dark" name="service_id" id="service_id" required
                        <?php echo isset($mode) && $mode == 'view' ?'disabled' :''?>>
                        <option value="">Choose Service</option>
                        <?php
                            if(isset($mode)){
                                $stmt = $obj->con1->prepare(
                                    "SELECT * FROM `service_type`"
                                );
                                $stmt->execute();
                                $Resp = $stmt->get_result();
                                $stmt->close();

                                while ($result = mysqli_fetch_array($Resp)) { 
                        ?>
                            <option value="<?php echo $result["id"]; ?>"
                            <?php echo isset($mode) && $data["sid"] == $result["id"] ? "selected" : ""; ?>
                            >
                                <?php echo $result["name"]; ?>
                            </option>
                        <?php 
                            } }
                        ?>
                    </select>
                </div>
                <p class="mt-3 text-danger text-base font-bold" id="demo"></p>
                <div>
                    <label for="gridStatus">Status</label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="enable" class="form-radio" checked required 
                            <?php echo isset($mode) && $data["status"] == "enable" ? "checked" : ""; ?> 
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?>
                        />
                        <span>Enable</span>
                    </label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="disable" class="form-radio text-danger" required 
                            <?php echo isset($mode) && $data["status"] == "disable" ? "checked" : ""; ?> 
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?>
                        />
                        <span>Disable</span>
                    </label>
                </div>
                <div class="relative inline-flex align-middle gap-3 mt-4">
                    <!-- Save/Update button -->
                    <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save' ?>"
                    id="save" class="btn btn-success" onclick="return localValidate()" <?php echo isset($mode) && $mode == 'view' ? 'style="display:none;"' : '' ?>>
                        <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                    </button>
                    <!-- Close button -->
                    <button type="button" class="btn btn-danger" onclick="window.location='product_service.php'">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    
    function localValidate(){
        let form = document.getElementById('mainForm');
        let product_id = document.getElementById('product_id');
        let service_id = document.getElementById('service_id');
        let submitButton = document.getElementById('save');
        <?php if (isset($mode)) { ?>
            if(form.checkValidity() && check_product_service(product_id.value,service_id.value,'<?php echo $data['srno'] ?>')){
                console.log("if")
                setTimeout(() => {
                    submitButton.disabled = true;
                }, 0);
                return true;
            }
        <?php } else { ?>
                console.log("else")
                if(form.checkValidity()){
                setTimeout(() => {
                    submitButton.disabled = true;
                }, 0);
                return true;
        }
        <?php } ?>
    }

    function get_service_list(product_id){
        const obj = new XMLHttpRequest();
        obj.open("GET",`./ajax/get_service_list.php?product_id=${product_id}`, false);
        obj.send();

        if(obj.status == 200){
            let x = obj.responseText;
            console.log(x);
            document.getElementById("service_id").innerHTML = "";
            document.getElementById("service_id").innerHTML = x;
        }
    }

    function check_product_service(product_id,service_id,pid){
        console.log("yes");
        const obj = new XMLHttpRequest();
        obj.open("GET",`./ajax/check_product_service.php?product_id=${product_id}&service_id=${service_id}&pid=${pid}`, false);
        obj.send();
        
        if(obj.status == 200){
            let x = obj.responseText;
            console.log(x);
            document.getElementById('service_id').value = "";
            if (x >= 1) {
                document.getElementById("demo").innerHTML = "Sorry this Product Service Already Exist!";
                return false;
            }
            else {
                document.getElementById("demo").innerHTML = "";
                return true;
            }
        }
    }

</script>

<?php include "footer.php";
?>