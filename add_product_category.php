<?php
include "header.php";

if (isset($_COOKIE['viewId'])) {
    $mode = 'view';
    $viewId = $_COOKIE['viewId'];
    $stmt = $obj->con1->prepare("SELECT * FROM `product_category` where id=?");
    $stmt->bind_param('i', $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_COOKIE['editId'])){
    $mode = 'edit';
    $editId = $_COOKIE['editId'];
    $qry = $obj->con1->prepare("SELECT * FROM `product_category` WHERE  id=?");
    $qry->bind_param("i", $editId);
    $qry->execute();
    $Res = $qry->get_result();
    $data = $Res->fetch_assoc();
    $qry->close();
}

if(isset($_REQUEST['update'])){
    $name = $_REQUEST["name"];
    $warranty = $_REQUEST["warranty"];

    $qry = $obj->con1->prepare("UPDATE `product_category` SET name=?, warranty_period=? WHERE id=?");
    $qry->bind_param("ssi", $name, $warranty, $editId);
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

if (isset($_REQUEST["save"])) {
    $name = $_REQUEST["name"];
    $warranty = $_REQUEST["warranty"];

    try {
        $stmt = $obj->con1->prepare(
            "INSERT INTO product_category(`name`, `warranty_period`) VALUES (?,?)"
        );
        $stmt->bind_param("ss", $name, $warranty);
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
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Product Category-<?php echo isset($mode) ? ($mode == 'edit' ? 'Edit' : 'View' ) : 'Add' ?></h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post" id="mainForm">
                <div>
                    <label for="name">Name</label>
                    <input type="hidden" id="pid" value="<?php echo isset($mode) ? $data['id'] : '' ?>">
                    <input id="name" name="name" type="text" class="form-input" pattern="^\s*\S.*$" 
                    value="<?php echo (isset($mode)) ? $data['name'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> />
                    <p class="mt-2 hidden text-danger text-base font-bold" id="demo"></p>
                </div>
                <div>
                    <label for="warranty">Warranty Period (In months)</label>
                    <input id="warranty" name="warranty" type="number" class="form-input" pattern="^\s*\S.*$" 
                    value="<?php echo isset($mode) ? $data['warranty_period'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> placeholder="12" min="0" />
                </div>

                <div class="relative inline-flex align-middle gap-3 mt-4">

                    <?php if(isset($mode) && $mode != "view" || !isset($mode)){ ?>
                        <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save' ?>" id="save" class="btn btn-success"> 
                            <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                        </button>
                    <?php } ?>

                    <button type="button" class="btn btn-danger" onclick="window.location='product_category.php'">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    
document.addEventListener('DOMContentLoaded', function() {
        const submitButton = document.getElementById('save');
        const form = document.getElementById('mainForm');

        submitButton.addEventListener('click', function() {
            const c1 = document.getElementById("name");
            const id = document.getElementById("pid");
            if (!checkName(c1,id)) {
                return false;
            }
        });
    });

function checkName(c1,id) {
    const n = c1.value;
    const pid = id.value;

    const obj = new XMLHttpRequest();
    obj.open("GET", "./ajax/check_product.php?name=" + n +"&pid="+pid, false); // synchronous request
    obj.send();

    if (obj.status == 200) {
        const x = obj.responseText;
        if (x >= 1) {
            c1.value = "";
            c1.focus();
            document.getElementById("demo").innerHTML = "Sorry the product already exists!";
            document.getElementById("demo").classList.remove("hidden");
            return false;
        } else {
            document.getElementById("demo").innerHTML = "";
            document.getElementById("demo").classList.add("hidden");
            return true;
        }
    } else {
        // Handle errors
        return false;
    }
}
   
</script>

<?php
    include "footer.php";
?>