<?php
include "header.php";
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
<div class='p-6' >
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Product-Service- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post">
                <div>
                    <label for="groupFname"> Product</label>
                    <select class="form-select text-white-dark" name="product_id" required>
                        <option value="">Choose Product</option>
                        <?php
                        $stmt = $obj->con1->prepare(
                            "SELECT * FROM `product_category`"
                        );
                        $stmt->execute();
                        $Resp = $stmt->get_result();
                        $stmt->close();

                        while ($result = mysqli_fetch_array($Resp)) { ?>
                    <option value="<?php echo $result[
                        "id"
                    ]; ?>"><?php echo $result["name"]; ?></option>
                    <?php }
                        ?>   
                    </select>
                </div>
                <div>
                    <label for="groupFname">Service</label>
                    <select class="form-select text-white-dark" name="service_id" required>
                        <option value="">Choose Service</option>
                        <?php
                        $stmt = $obj->con1->prepare(
                            "SELECT * FROM `service_type`"
                        );
                        $stmt->execute();
                        $Resp = $stmt->get_result();
                        $stmt->close();

                        while ($result = mysqli_fetch_array($Resp)) { ?>
                    <option value="<?php echo $result[
                        "id"
                    ]; ?>"><?php echo $result["name"]; ?></option>
                        <?php }
                        ?>    
                    </select>
                </div>
                <div>
                    <label for="gridStatus">Status</label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="enable" class="form-radio" checked required />
                        <span>Enable</span>
                    </label>
                    <label class="inline-flex mr-3">
                        <input type="radio" name="default_radio" value="disable" class="form-radio text-danger" required />
                        <span>Disable</span>
                    </label>
                </div>
                    
                    <div class="relative inline-flex align-middle gap-3 mt-4">
                        <button type="submit" name="save" id="save" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-danger" onclick="location.href='product_service.php'">Close</button>
                    </div>
                </div>
        </div>
        </form>
    </div>
</div>

<?php include "footer.php";
?>