<?php
include "header.php";
error_reporting(E_ALL);

if (isset($_REQUEST["save"])) {
    $detail = $_REQUEST["quill_input"];
    $type = $_REQUEST["type"];
    $date_time = date("d-m-Y h:i A");
    try {
        $stmt = $obj->con1->prepare(
            "INSERT INTO `privacy_policy`(`detail`,`type`,`date_time`) VALUES (?,?,?)"
        );
        $stmt->bind_param("sss", $detail, $type, $date_time);
        $Resp = $stmt->execute();
        $stmt->close();

        if (!$Resp) {
            throw new Exception(
                "Problem in adding! " . strtok($obj->con1->error, "(")
            );
        }
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "data", time() + 3600, "/");
        header("location:privacy.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:privacy.php");
    }
}
?>

<div class='p-6'>
    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Privacy Policy Add</h5>
        </div>
        <form method="post" class="space-y-5" id="privacy_form">
            <div>
                <label for="">Type</label>
                <select class="form-select text-white-dark" required name="type">
                    <option value="">Choose Type</option>
                    <option value="user">User</option>
                    <option value="service">Service-center</option>
                    <option value="technician">Technician</option>
                </select>
            </div>
            <div>
                <label for="" class="mb-3 block">Detail</label>
                <div id="editor" name="detail" class="!mt-1"></div>
            </div>
            <div class="relative inline-flex align-middle gap-5 mt-4">
                <button type="submit" name="save" id="save" class="btn btn-success"
                    onclick="formSubmit('quill-input')">Save</button>
                <button type="button" class="btn btn-danger" onclick="location.href='privacy.php'">Close</button>
            </div>
            <input type="hidden" name="quill_input" id="quill-input">
        </form>
    </div>

</div>

<!-- script -->
<script src="assets/js/quill.js"></script>
<script>
document.addEventListener("alpine:init", () => {
    Alpine.data("form", () => ({
        tableData: {
            id: 1,
            name: 'John Doe',
            email: 'johndoe@yahoo.com',
            date: '10/08/2020',
            sale: 120,
            status: 'Complete',
            register: '5 min ago',
            progress: '40%',
            position: 'Developer',
            office: 'London'
        },
    }));
});
// quill-editor

var quill = new Quill('#editor', {
    theme: 'snow'
});
var toolbar = quill.container.previousSibling;
toolbar.querySelector('.ql-picker').setAttribute('title', 'Font Size');
toolbar.querySelector('button.ql-bold').setAttribute('title', 'Bold');
toolbar.querySelector('button.ql-italic').setAttribute('title', 'Italic');
toolbar.querySelector('button.ql-link').setAttribute('title', 'Link');
toolbar.querySelector('button.ql-underline').setAttribute('title', 'Underline');
toolbar.querySelector('button.ql-clean').setAttribute('title', 'Clear Formatting');
toolbar.querySelector('[value=ordered]').setAttribute('title', 'Ordered List');
toolbar.querySelector('[value=bullet]').setAttribute('title', 'Bullet List');

function formSubmit(ele) {
    let xyz = document.getElementById(ele);
    xyz.value = quill.root.innerHTML;
}
</script>

<?php
include "footer.php";
?>