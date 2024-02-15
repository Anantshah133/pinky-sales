<?php
include "header.php";

$center_id = $_SESSION['type'] == 'center' ? $_SESSION["scid"] : '';
if (isset($_COOKIE['editId'])) {
    $mode = 'edit';
    $editId = $_COOKIE['editId'];
    $stmt = $obj->con1->prepare("SELECT t1.*, s1.name AS service_center_name FROM technician t1, service_center s1 WHERE t1.service_center=s1.id And  t1.id=?");
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if (isset($_COOKIE['viewId'])) {
    $mode = 'view';
    $viewId = $_COOKIE['viewId'];
    $stmt = $obj->con1->prepare("SELECT t1.*, s1.name AS service_center_name FROM technician t1, service_center s1 WHERE t1.id=? AND t1.service_center=s1.id;");
    $stmt->bind_param('i', $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if (isset($_REQUEST['update'])) {
    $name = $_REQUEST["name"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["contact_num"];
    $serviceCenterId = $_REQUEST["service_center"];
    $user_id = $_REQUEST["userid"];
    $pass = $_REQUEST["password"];
    $status = $_REQUEST["default_radio"];
    $date_time = date("d-m-Y h:i A");

    if ($_FILES['idproof_img']['size'] > 0) {
        $serialNumImg = uploadImage('idproof_img', 'images/technician_idproof');
        $stmt = $obj->con1->prepare("UPDATE `technician` SET id_proof=? WHERE id=?");
        $stmt->bind_param("si", $serialNumImg, $editId);
        $Resp = $stmt->execute();
        $stmt->close();

        if (isset($data['id_proof'])) {
            $oldSerialNumImg = $data['id_proof'];
            unlink("images/technician_idproof/" . $oldSerialNumImg);
        }

        move_uploaded_file($_FILES['idproof_img']['tmp_name'], "images/technician_idproof/" . $serialNumImg);
    }

    try {
        $stmt = $obj->con1->prepare(
            "UPDATE technician SET name=?, email=?, contact=?,service_center=?, userid=?, password=?, status=?, date_time=? WHERE id=?"
        );
        $stmt->bind_param("sssissssi", $name, $email, $contact, $serviceCenterId, $user_id, $pass, $status, $date_time, $editId);
        $Resp = $stmt->execute();

        if (!$Resp) {
            throw new Exception("Problem in adding! " . strtok($obj->con1->error, '('));
        }
        $stmt->close();
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:technician.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:technician.php");
    }
}

if (isset($_REQUEST["save"])) {
    $name = $_REQUEST["name"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["contact_num"];
    $serviceCenterId = isset($center_id) ? $_SESSION['scid'] : $_REQUEST["service_center"];
    $user_id = $_REQUEST["userid"];

    $pass = $_REQUEST["password"];
    $status = $_REQUEST["default_radio"];
    $date_time = date("d-m-Y h:i A");
    $idproofImg = "123";

    echo "INSERT INTO `technician`(`name`,`email`,`contact`,`service_center`,`userid`,`password`,`id_proof`,`status`,`date_time`) VALUES ($name,$email,$contact,$serviceCenterId,$user_id,$pass,$status,$date_time,$idproofImg)";

    try {
        $idproofImg = uploadImage("idproof_img", "images/technician_idproof");
        $stmt = $obj->con1->prepare(
            "INSERT INTO `technician`(`name`,`email`,`contact`,`service_center`,`userid`,`password`,`id_proof`,`status`,`date_time`) VALUES (?,?,?,?,?,?,?,?,?)"
        );
        $stmt->bind_param("sssisssss", $name, $email, $contact, $serviceCenterId, $user_id, $pass, $idproofImg, $status, $date_time);
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
        move_uploaded_file(
            $_FILES["idproof_img"]["tmp_name"],
            "images/technician_idproof/" . $idproofImg
        );
        header("location:technician.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:technician.php");
    }
}

function uploadImage($inputName, $uploadDirectory)
{
    if (isset($_FILES[$inputName]) && isset($_FILES[$inputName]["name"])) {
        $fileName = $_FILES[$inputName]["name"];
        $tmpFilePath = $_FILES[$inputName]["tmp_name"];

        if ($fileName != "") {
            $targetDirectory = $uploadDirectory . "/";

            if (!file_exists($targetDirectory)) {
                mkdir($targetDirectory, 0755, true);
            }

            $i = 0;
            $newFileName = $fileName;

            while (file_exists($targetDirectory . $newFileName)) {
                $i++;
                $newFileName = $i . "_" . $fileName;
            }

            $targetFilePath = $targetDirectory . $newFileName;
            return $newFileName;
        }
    }

    return null;
}
?>


<div class='p-6'>
    <div class="panel border shadow-md shadow-slate-200">
        <div class="mb-5 flex items-center justify-between">
            <h5 class="text-xl text-primary font-semibold dark:text-white-light">Technician -
                <?php echo isset($mode) ? ($mode == 'edit' ? 'Edit' : 'View') : 'Add' ?>
            </h5>
        </div>

        <form class="space-y-5" method="post" id="mainForm" enctype="multipart/form-data">
            <div>
                <label for="groupFname"> Name</label>
                <input id="groupFname" type="text" name="name" placeholder="Enter Name" class="form-input"
                    value="<?php echo (isset($mode)) ? $data['name'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
            </div>
            <div>
                <label for="ctnEmail">Email address</label>
                <input id="ctnEmail" type="email" name="email" placeholder="name@example.com" class="form-input"
                    pattern="^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$" title="Invalid Email Format"
                    value="<?php echo (isset($mode)) ? $data['email'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
            </div>
            <div>
                <label for="contact_num"> Contact </label>
                <div class="flex">
                    <div
                        class="bg-[#eee] flex justify-center items-center ltr:rounded-l-md rtl:rounded-r-md px-3 font-semibold border ltr:border-r-0 rtl:border-l-0 border-[#e0e6ed] dark:border-[#17263c] dark:bg-[#1b2e4b]">
                        +91</div>
                    <input name="contact_num" id="contact_num" type="tel" placeholder="1234567890"
                        class="form-input ltr:rounded-l-none rtl:rounded-r-none"
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                        value="<?php echo (isset($mode)) ? $data['contact'] : '' ?>" <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> maxlength="10" minlength="10" pattern="[0-9]+"
                        title="Please enter numbers only" required />
                </div>
            </div>
            <div>
                <label for="groupFname"> Service Center</label>

                <select class="form-select text-white-dark" name="service_center" <?php echo (isset($mode) && $mode == 'view') || isset($center_id) ? 'disabled' : '' ?>>
                    <option value="">Choose service center</option>
                    <?php
                    $stmt = $obj->con1->prepare(
                        "SELECT * FROM `service_center` WHERE status='enable'"
                    );
                    $stmt->execute();
                    $Res = $stmt->get_result();
                    $stmt->close();

                    while ($result = mysqli_fetch_assoc($Res)) {
                        ?>
                        <option value="<?php echo $result["id"]; ?>" <?php echo isset($mode) && $result['id'] == $data['service_center'] || isset($center_id) && $center_id == $result['id'] ? 'selected' : '' ?>>
                            <?php echo $result["name"]; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="gridUID">Userid</label>
                <input type="text" placeholder="" name="userid" class="form-input"
                    value="<?php echo (isset($mode)) ? $data['userid'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
            </div>
            <div>
                <label for="gridpass">Password</label>
                <input type="password" placeholder="Enter Password" name="password" class="form-input" pattern=".{8,}"
                    title="Password should be at least 8 characters long"
                    value="<?php echo (isset($mode)) ? $data['password'] : '' ?>" <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> required />
            </div>
            <div>
                <label for="idproof_img"> Id Proof </label>
                <input name="idproof_img" id="idproof_img" type="file" <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?>
                    class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 file:text-white file:hover:bg-primary"
                    value="<?php echo isset($mode) ? $data['id_proof'] : "" ?>" required
                    onchange="readURL(this, 'previewModalImage', 'errModalImg')" />

                <img src="<?php echo (isset($mode)) ? 'images/technician_idproof/' . $data['id_proof'] : '' ?>"
                    class="mt-8 w-80 preview-img hidden" alt="" id="previewModalImage" value="">
                <h6 id='errModalImg' class='error-elem'></h6>
            </div>
            <div>
                <label for="gridStatus">Status</label>
                <label class="inline-flex mr-3">
                    <input type="radio" name="default_radio" value="enable" class="form-radio" checked required <?php echo isset($mode) && $data["status"] == "enable" ? "checked" : ""; ?> <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> />
                    <span>Enable</span>
                </label>
                <label class="inline-flex mr-3">
                    <input type="radio" name="default_radio" value="disable" class="form-radio text-danger" required
                        <?php echo isset($mode) && $data["status"] == "disable" ? "checked" : ""; ?> <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> />
                    <span>Disable</span>
                </label>
            </div>
            <div
                class="relative inline-flex align-middle gap-3 mt-4  <?php echo isset($mode) && $mode == 'view' ? 'hidden' : '' ?>">
                <button type="submit" class="btn btn-success"
                    name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save'; ?>" id="save"
                    onclick="return validateAndDisable()">
                    <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save'; ?>
                </button>
                <button type="button" class="btn btn-danger" onclick="location.href='technician.php'">Close</button>
            </div>
        </form>
    </div>
</div>


<script>
    function readURL(input, preview, errElement) {
        if (input.files && input.files[0]) {
            var filename = input.files[0].name;
            var reader = new FileReader();
            var extn = filename.split('.').pop().toLowerCase();

            var allowedExtns = ["jpg", "jpeg", "png", "bmp", "webp"];

            if (allowedExtns.includes(extn)) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector('#' + preview).src = e.target.result;
                    document.getElementById(preview).style.display = "block";
                };

                reader.readAsDataURL(input.files[0]);
                document.getElementById(errElement).innerHTML = "";
                document.getElementById('save').disabled = false;
            } else {
                document.getElementById(preview).style.display = "none";
                document.getElementById(errElement).innerHTML = "Please Select Image Only";
                document.getElementById('save').disabled = true;
            }
        }
    }

    const resetForm = (formElement) => {
        formElement.reset();
        let preview = document.querySelectorAll('.preview-img');
        preview.forEach(img => img.style.display = 'none');
    }
</script>

<?php
include "footer.php";
?>