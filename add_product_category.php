<?php
include "header.php";

if(isset($_REQUEST['editId'])){
    $mode = 'edit';
    $editId = $_REQUEST['editId']; 

    $qry = $obj->con1->prepare("SELECT * FROM `product_category` WHERE  id=?");
    $qry->bind_param("i", $editId);
    $qry->execute();
    $Res = $qry->get_result();
    $data = $Res->fetch_assoc();
    $qry->close();
}
if(isset($_REQUEST['update'])){
    $name = $_REQUEST["name"];
    

    $qry = $obj->con1->prepare("UPDATE `product_category` SET name=? WHERE id=?");
    $qry->bind_param("si", $name,$editId);
    $Res = $qry->execute();
    $qry->close();

    if ($Res) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:product_category.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:product_category.php");
    }
}
if (isset($_REQUEST["viewId"])) {
    $mode = 'view';
    $viewId = $_REQUEST["viewId"];
    $stmt = $obj->con1->prepare("SELECT * FROM `product_category` where id=?");
    $stmt->bind_param('i', $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

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
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Product Category-<?php echo isset($mode) ? ($mode == 'edit' ? 'Edit' : 'View' ) : 'Add' ?></h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post" id="mainForm">
                <div>
                    <label for="groupFname">Name</label>
                    <input id="groupFname" name="name" type="text" class="form-input" value="<?php echo (isset($mode)) ? $data['name'] : '' ?>" required
                    <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> pattern="^\S+$" />
                </div>
                <div class="relative inline-flex align-middle gap-3 mt-4 <?php echo isset($mode) && $mode == 'view' ? 'hidden' : '' ?>">
                    <button type="submit" name="<?php echo isset($mode) == 'edit' ? 'update' : 'save' ?>" id="save" class="btn btn-success" onclick="return validateAndDisable()"><?php echo isset($mode) == 'edit' ? 'Update' : 'Save' ?></button>
                    <button type="button" class="btn btn-danger" onclick="window.location='product_category.php'">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
    include "footer.php";
?>