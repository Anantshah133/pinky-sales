<?php
include "header.php";

$stmt = $obj->con1->prepare("SELECT * FROM `superadmin`");
$stmt->execute();
$Res = $stmt->get_result();
$data = $Res->fetch_assoc();
$stmt->close();

if(isset($_REQUEST["save"])){
    $current_password = $_REQUEST["current"];
    $new_password = $_REQUEST["new"];
    $stmt = $obj->con1->prepare("UPDATE superadmin SET password=? WHERE superadmin.password=?");
    $stmt->bind_param("ss", $new_password, $current_password);
    $Res = $stmt->execute();
    $stmt->close();

    if($Res){
        setcookie("msg", "passChange", time() + 3600, "/");
        header("location:index.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:index.php");
    }
}
?>

<div class="p-6">
    <form class="mb-5 panel rounded-md border border-[#ebedf2] bg-white p-4 dark:border-[#191e3a] dark:bg-[#0e1726]">
        <h6 class="mb-5 font-bold text-primary text-2xl">General Information</h6>
        <div class="flex flex-col sm:flex-row pb-4">
            <div class="grid flex-1 grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="font-bold text-base" for="name">Name</label>
                    <input id="name" type="text" readonly class="form-input" value="<?php echo $data["name"] ?>" />
                </div>
                <div>
                    <label class="font-bold text-base" for="profession">Email</label>
                    <input id="profession" type="email" readonly class="form-input" value="<?php echo $data["email"] ?>" />
                </div>
                <div>
                    <label class="font-bold text-base" for="name">Mobile No</label>
                    <input id="name" type="text" readonly class="form-input" value="<?php echo $data["phno"] ?>" />
                </div>
                <div>
                    <label class="font-bold text-base" for="profession">Username</label>
                    <input id="profession" type="email" readonly class="form-input" value="<?php echo $data["username"] ?>" />
                </div>
            </div>
        </div>
    </form>
    <form class="mb-5 panel rounded-md border border-[#ebedf2] bg-white p-4 dark:border-[#191e3a] dark:bg-[#0e1726]" onsubmit="return validatePassword()">
        <h6 class="mt-3 mb-4 font-bold text-primary text-2xl">Change Password :-</h6>
        <div class="flex flex-col sm:flex-row">
            <div class="grid flex-1 grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="font-bold text-base text-logo" for="current">Current Password</label>
                    <input id="current" name="current" type="text" class="form-input" required/>
                </div>
                <div></div>
                <div>
                    <label class="font-bold text-base text-logo" for="new">New Password</label>
                    <input id="new" name="new" type="text" class="form-input" required pattern=".{8,}" title="Password should be at least 8 characters long" />
                </div>
                <div></div>
                <div>
                    <label class="font-bold text-base text-logo" for="confirm">Confirm Password</label>
                    <input id="confirm" name="confirm" type="text" class="form-input" required pattern=".{8,}" title="Password should be at least 8 characters long" />
                </div>
                <div></div>
            </div>
        </div>
        <div class="mt-3 mb-3 sm:col-span-2">
            <button type="submit" name="save" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

<script>
    function validatePassword(){
        let password = '<?php echo $data["password"]; ?>';
        let currentPassword = document.getElementById("current").value;
        let newPass = document.getElementById("new").value;
        let confirmPass = document.getElementById("confirm").value;

        if(password === currentPassword){
            if(newPass == confirmPass){
                return true;
            } else {
                coloredToast("danger", "Password didn't match !!");
            }
        } else {
            coloredToast("danger", "Incorrect Current Password");
        }
        return false;
    }
</script>

<?php
include "footer.php";
?>