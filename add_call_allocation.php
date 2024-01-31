<?php
include "header.php";
error_reporting(E_ALL);
?>

<?php 
    if(isset($_POST['save_btn'])){
        $complaintNum = $_POST['complaint_num'];
        $serviceCenterId = $_POST['service_center'];
        $productSerialNum = $_POST['product_srno'];
        // $serialNumImg = $_POST['srno_img'];
        // $productModelImg = $_POST['product_model_img'];
        // $purchaseDateImg = $_POST['purchase_date_img'];
        $productModel = $_POST['product_model'];
        $purchaseDate = $_POST['purchase_date'];
        $technician = $_POST['technician'];
        $callStatus = $_POST['call_status'];
        $reason = $_POST['rson'];
        $allocationDate = $_POST['allocation_date'];
        $allocationTime = $_POST['allocation_time'];


        try {
            // $serialNumImg = $_FILES['srno_img']['name'];
            // $serialNumImgPath = $_FILES['srno_img']['tmp_name'];

            // if ($_FILES["srno_img"]["name"] != ""){
            //     if(file_exists("serial_no_img/" . $serialNumImg)) {
            //         $i = 0;
            //         $PicFileName = $_FILES["srno_img"]["name"];
            //         $Arr1 = explode('.', $PicFileName);
            //         $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
            //         while (file_exists("serial_no_img/" . $PicFileName)) {
            //             $i++;
            //             $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
            //         }
            //     }
            //     else {
            //         $PicFileName = $_FILES["srno_img"]["name"];
            //     }
            // } 

            $serialNumImg = uploadImage('srno_img', 'images/serial_no_img');
            $productModelImg = uploadImage('product_model_img', 'images/product_model_img');
            $purchaseDateImg = uploadImage('purchase_date_img', 'images/purchase_date_img');
            
            $stmt = $obj->con1->prepare("INSERT INTO `call_allocation`(`complaint_no`, `service_center_id`, `product_serial_no`, `serial_no_img`, `product_model`, `product_model_img`, `purchase_date`, `purchase_date_img`, `technician`, `allocation_date`, `allocation_time`, `status`, `reason`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

            $stmt->bind_param("sissssssissss", $complaintNum, $serviceCenterId, $productSerialNum, $serialNumImg, $productModel, $productModelImg, $purchaseDate, $purchaseDateImg, $technician, $allocationDate, $allocationTime, $callStatus, $reason);
            
            $Resp=$stmt->execute();

            if(!$Resp) {
                throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
            }
            $stmt->close();
        }
        catch(\Exception  $e) {
            setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
        }
    
        if($Resp) {
            move_uploaded_file($_FILES['srno_img']['tmp_name'], "images/serial_no_img/".$serialNumImg);

            move_uploaded_file($_FILES['product_model_img']['tmp_name'], "images/product_model_img/".$productModelImg);

            move_uploaded_file($_FILES['purchase_date_img']['tmp_name'], "images/purchase_date_img/".$purchaseDateImg);

            setcookie("msg", "data",time()+3600,"/");
            header("location:call_allocation.php");
        }
        else {
            setcookie("msg", "fail",time()+3600,"/");
            header("location:call_allocation.php");
        } 
    }

    function uploadImage($inputName, $uploadDirectory) {
        $fileName = $_FILES[$inputName]['name'];
        $tmpFilePath = $_FILES[$inputName]['tmp_name'];
        echo $fileName.$tmpFilePath;
        if ($fileName != "") {
            $targetDirectory = $uploadDirectory . '/';

            if (!file_exists($targetDirectory)) {
                mkdir($targetDirectory, 0755, true);
            }

            $i = 0;
            $newFileName = $fileName;

            while (file_exists($targetDirectory . $newFileName)) {
                $i++;
                $newFileName = $i . '_' . $fileName;
            }
            
            $targetFilePath = $targetDirectory . $newFileName;
            return $newFileName;
        }

        return null;
    }    
?>

<div class='p-6'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-5'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">Add - Call Allocation</h5>
        </div>
        <div class="mb-5">
            <form id="call_form" method="post" onsubmit="" enctype="multipart/form-data">
                <div class="flex flex-wrap">
                    <div class="w-6/12 px-3 space-y-5">
                        <div>
                            <label for="complaint_num"> Complaint No. </label>
                            <input name="complaint_num" id="complaint_num" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="service_center">Service Center</label>
                            <select name="service_center" id="service_center" class="form-select text-white-dark"
                                required>
                                <option value=''>-none-</option>
                                <?php
                            $stmt = $obj->con1->prepare("SELECT * FROM `service_center` WHERE status='enable'");
                            $stmt->execute();
                            $Resp = $stmt->get_result();
                            $stmt->close();

                            while ($result = mysqli_fetch_array($Resp)) { 
                        ?>
                                <option value="<?php echo $result["id"]; ?>"><?php echo $result["name"]; ?></option>
                                <?php 
                            } 
                        ?>
                            </select>
                        </div>
                        <div>
                            <label for="product_srno"> Product Serial NO. </label>
                            <input name="product_srno" id="product_srno" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="srno_img">Serial NO. Image</label>
                            <input name="srno_img" id="srno_img" type="file"
                                class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 file:text-white file:hover:bg-primary"
                                required onchange="readURL(this, 'srNoImg', 'errSrNoImg')" />

                            <img src="" class="mt-8 hidden w-80 preview-img" alt="" id="srNoImg">
                            <h6 id='errSrNoImg' class='error-elem'></h6>
                        </div>
                        <div>
                            <label for="product_model"> Product Model </label>
                            <input name="product_model" id="product_model" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="product_model_img"> Product Model Image </label>
                            <input name="product_model_img" id="product_model_img" type="file"
                                class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 file:text-white file:hover:bg-primary"
                                required onchange="readURL(this, 'previewModalImage', 'errModalImg')" />

                            <img src="" class="mt-8 hidden w-80 preview-img" alt="" id="previewModalImage">
                            <h6 id='errModalImg' class='error-elem'></h6>
                        </div>
                    </div>
                    <div class="w-6/12 px-3 space-y-5">
                        <div x-data="purchaseDate">
                            <label for="purchase_date"> Purchase Date </label>
                            <input x-model="date1" name="purchase_date" id="purchase_date" class="form-input" />
                        </div>
                        <div>
                            <label for="purchase_date_img">Purchase Date Image</label>
                            <input id="purchase_date_img" name="purchase_date_img" type="file"
                                class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 file:text-white file:hover:bg-primary"
                                required onchange="readURL(this, 'purDateImg', 'errPurDateImg')" />

                            <img src="" class="mt-8 hidden w-80 preview-img" alt="" id="purDateImg">
                            <h6 id='errPurDateImg' class='error-elem'></h6>
                        </div>
                        <div>
                            <label for="technician"> Technician </label>
                            <select name="technician" id="technician" class="form-select text-white-dark" required>
                                <option value=''>-none-</option>
                                <option value="1">Deepak Kumar</option>
                                <option value="1">Kadam</option>
                                <option value="1">MAHENDRA</option>
                                <option value="1">Nagender Tiwari</option>
                                <option value="1">RANAPRATAP</option>
                                <option value="1">SANJAY SINGH</option>
                                <option value="1">Tech 1</option>
                                <option value="1">VILAS</option>
                                <option value="1">Waris</option>
                            </select>
                        </div>
                        <div>
                            <label for="call_status"> Status</label>
                            <select name="call_status" id="call_status" class="form-select text-white-dark" required>
                                <option value=''>-none-</option>
                                <option value="1">New</option>
                                <option value="1">Pending</option>
                                <option value="1">Canclled</option>
                                <option value="1">Closed</option>
                                <option value="1">Allocated</option>
                            </select>
                        </div>
                        <div>
                            <label for="rson"> Reason </label>
                            <input id="rson" name="rson" type="text" class="form-input" required />
                        </div>

                        <div x-data="allocationDate">
                            <label for="allocation_date"> Allocation Date </label>
                            <input x-model="date2" name="allocation_date" id="allocation_date" class="form-input" />
                        </div>

                        <div x-data="allocationTime">
                            <label for="allocation_time"> Allocation Time </label>
                            <input x-model="time" name="allocation_time" id="allocation_time" class="form-input" />
                        </div>
                    </div>
                </div>
                <div class="relative inline-flex align-middle gap-3 mt-10">
                    <button type="submit" id="save_btn" name="save_btn" class="btn btn-success">Save</button>
                    <button type="button" id="close_btn" name="close_btn" class="btn btn-danger"
                        onclick="resetForm(call_form)">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/flatpickr.js"></script>
<script>
document.addEventListener("alpine:init", () => {
    let todayDate = new Date();
    let formattedToday = todayDate.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).split('/').join('-')


    Alpine.data("purchaseDate", () => ({
        date1: formattedToday,
        init() {
            flatpickr(document.getElementById('purchase_date'), {
                dateFormat: 'd-m-Y',
                defaultDate: this.date1,
            })
        }
    }));

    Alpine.data("allocationDate", () => ({
        date2: formattedToday,
        init() {
            flatpickr(document.getElementById('allocation_date'), {
                dateFormat: 'd-m-Y',
                minDate: formattedToday,
                defaultDate: this.date2,
                minDate: "today",
            })
        }
    }));

    Alpine.data("allocationTime", () => ({
        time: todayDate.toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }),
        init() {
            flatpickr(document.getElementById('allocation_time'), {
                defaultDate: this.time,
                noCalendar: true,
                enableTime: true,
                dateFormat: 'H:i'
            });
        }
    }));
});


function readURL(input, preview, errElement) {
    if (input.files && input.files[0]) {
        var filename = input.files[0].name;
        var reader = new FileReader();
        var extn = filename.split('.').pop().toLowerCase();

        var allowedExtns = ["jpg", "jpeg", "png", "bmp", "webp"];

        if (allowedExtns.includes(extn)) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#' + preview).src = e.target.result;
                document.getElementById(preview).style.display = "block";
            };

            reader.readAsDataURL(input.files[0]);
            document.getElementById(errElement).innerHTML = "";
            document.getElementById('save_btn').disabled = false;
        } else {
            document.getElementById(preview).style.display = "none";
            document.getElementById(errElement).innerHTML = "Please Select Image Only";
            document.getElementById('save_btn').disabled = true;
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