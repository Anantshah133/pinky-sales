<?php
include "header.php";

if (isset($_COOKIE['editId'])) {
    $mode = 'edit';
    $editId = $_COOKIE['editId'];
    $stmt = $obj->con1->prepare("SELECT p1.name AS product, c1.id, CONCAT(c1.fname, ' ', c1.lname) AS customer_name, c1.date, c1.complaint_no, c1.barcode FROM customer_reg c1, product_category p1 WHERE warranty=2 AND p1.id=c1.product_category AND c1.id=?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if (isset($_COOKIE['viewId'])) {
    $mode = 'view';
    $viewId = $_COOKIE['viewId'];
    $stmt = $obj->con1->prepare("SELECT p1.name AS product, c1.id, CONCAT(c1.fname, ' ', c1.lname) AS customer_name, c1.date, c1.complaint_no, c1.barcode FROM customer_reg c1, product_category p1 WHERE warranty=2 AND p1.id=c1.product_category AND c1.id=?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if (isset($_REQUEST['update'])) {
    $editId = $_COOKIE['editId'];
    $date = date("Y-m-d", strtotime($_REQUEST["warranty_date"]));

    try {
        $stmt = $obj->con1->prepare("UPDATE customer_reg SET date=? WHERE id=?");
        $stmt->bind_param("si", $date, $editId);
        $Res = $stmt->execute();
        if (!$Res) throw new Exception("Problem in adding! " . strtok($obj->con1->error, "("));
        $stmt->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Res) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:warranty.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:warranty.php");
    }
}

// if (isset($_REQUEST["save"])) {
//     $name = $_REQUEST["name"];
//     $mode = "";
//     try {
//         $stmt = $obj->con1->prepare("INSERT INTO state(name) VALUES (?)");
//         $stmt->bind_param("s", $name);
//         $Resp = $stmt->execute();
//         if (!$Resp) {
//             throw new Exception(
//                 "Problem in adding! " . strtok($obj->con1->error, "(")
//             );
//         }
//         $stmt->close();
//     } catch (\Exception $e) {
//         setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
//     }

//     if ($Resp) {
//         setcookie("msg", "data", time() + 3600, "/");
//         header("location:state.php");
//     } else {
//         setcookie("msg", "fail", time() + 3600, "/");
//         header("location:state.php");
//     }
//     exit();
// }
?>

<div class='p-6'>
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-3'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">
                <?php echo isset($mode) ? ($mode == 'edit' ? 'Edit' : 'View') : '' ?> Warranty Details
            </h5>
        </div>
        <div class="mb-5">
            <form class="space-y-5" method="post" id="mainForm">

                <div>
                    <label for="customerName" class="font-bold">Customer Name</label>
                    <input id="customerName" name="customer_name" type="text" class="form-input" pattern="^\s*\S.*$" required value="<?php echo isset($mode) ? $data['customer_name'] : '' ?>" readonly />
                </div>
                <div>
                    <label for="complaint_no" class="font-bold">Warranty Number</label>
                    <input id="complaint_no" name="complaint_no" type="text" class="form-input" pattern="^\s*\S.*$" required value="<?php echo isset($mode) ? $data['complaint_no'] : '' ?>" readonly />
                </div>
                <div>
                    <label for="product_name" class="font-bold">Product Name</label>
                    <input id="product_name" name="product_name" type="text" class="form-input" pattern="^\s*\S.*$" required value="<?php echo isset($mode) ? $data['product'] : '' ?>" readonly />
                </div>
                <div>
                    <label for="barcode" class="font-bold">Product Barcode</label>
                    <input id="barcode" name="barcode" type="text" class="form-input" pattern="^\s*\S.*$" required value="<?php echo isset($mode) ? $data['barcode'] : '' ?>" readonly />
                </div>
                <div x-data="warrantyDate">
                    <label class="font-bold">Warranty Start Date</label>
                    <input x-model="date2" name="warranty_date" id="warranty_date" class="form-input" required <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> />
                </div>

                <div class="relative inline-flex align-middle gap-3 mt-4">
                    <?php if(isset($mode) && $mode == "edit"){ ?>
                        <button type="submit" name="update" id="save" class="btn btn-success">
                            Update
                        </button>
                    <?php } ?>
                    <button type="button" class="btn btn-danger" onclick="window.location='warranty.php'">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("alpine:init", () => {
        Alpine.data("warrantyDate", () => ({
            date2: '<?php echo isset($mode) ? date("d-m-Y", strtotime($data['date'])) : date("d-m-Y") ?>',
            init() {
                flatpickr(document.getElementById('warranty_date'), {
                    dateFormat: 'd-m-Y',
                    defaultDate: this.date2,
                })
            }
        }));
    });
</script>

<?php
include "footer.php";
?>