<?php
include "header.php";
if (isset($_REQUEST["save"])) {
    $name = $_REQUEST["name"];

    try {
        $stmt = $obj->con1->prepare(
            "INSERT INTO `product_category`(`name`) VALUES (?)"
        );
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
        header("location:product_category.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:product_category.php");
    }
}
?>
<div class='p-6' >
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Product Category- Add</h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post">
                <div>
                    <label for="groupFname">Name</label>
                    <input id="groupFname" name="name" type="text" class="form-input" required />
                </div>
                    <div class="relative inline-flex align-middle gap-3 mt-4">
                        <button type="submit" name="save" id="save" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-danger" onclick="window.location='service_type.php'">Close</button>
                    </div>
            </form>
        </div>
    </div>
</div>

<?php
    include "footer.php";
?>