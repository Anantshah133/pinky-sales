<?php
include "header.php";
error_reporting(E_ALL);
?>

<?php 
    if(isset($_POST['save_btn'])){
        $complaintNum = $_POST['complaint_num'];
        $serviceCenter = $_POST['service_center'];
        $productSerialNum = $_POST['product_srno'];
        $serialNum = $_POST['srno_img'];
        $productModal = $_POST['product_modal'];
        $purchaseDate = $_POST['purchase_date'];
        $technicianDate = $_POST['technician_name'];
        $callStatus = $_POST['call_status'];
    }
?>

<div class='p-6'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-5'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">Add - Call Allocation</h5>
        </div>
        <div class="mb-5">
            <form class="" method="post" onsubmit="">
                <div class="flex flex-wrap">
                    <div class="w-6/12 px-3 space-y-5">
                        <div>
                            <label for="complaint_num"> Complaint No. </label>
                            <input name="complaint_num" id="complaint_num" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="service_center">Service Center</label>
                            <select name="service_center" id="service_center" class="form-select text-white-dark" required>
                                <option value=''>-none-</option>
                                <?php
                            $stmt = $obj->con1->prepare(
                                "SELECT * FROM `service_center` WHERE status='enable'"
                            );
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

                            <img src="" class="mt-8 hidden w-80" alt="" id="srNoImg">
                            <h6 id='errSrNoImg' class='error-elem'></h6>
                        </div>
                        <div>
                            <label for="product_modal"> Product Model </label>
                            <input name="product_modal" id="product_modal" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="product_modal_img"> Product Model Image </label>
                            <input name="product_modal_img" id="product_modal_img" type="file"
                                class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 file:text-white file:hover:bg-primary"
                                required onchange="readURL(this, 'previewModalImage', 'errModalImg')" />

                            <img src="" class="mt-8 hidden w-80" alt="" id="previewModalImage">
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
                            <input id="purchase_date_img" type="file"
                                class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 file:text-white file:hover:bg-primary"
                                required onchange="readURL(this, 'purDateImg', 'errPurDateImg')" />

                            <img src="" class="mt-8 hidden w-80" alt="" id="purDateImg">
                            <h6 id='errPurDateImg' class='error-elem'></h6>
                        </div>
                        <div>
                            <label for="technician_name"> Technician </label>
                            <select name="technician_name" id="technician_name" class="form-select text-white-dark" required>
                                <option value=''>-none-</option>
                                <option>Deepak Kumar</option>
                                <option>Kadam</option>
                                <option>MAHENDRA</option>
                                <option>Nagender Tiwari</option>
                                <option>RANAPRATAP</option>
                                <option>SANJAY SINGH</option>
                                <option>Tech 1</option>
                                <option>VILAS</option>
                                <option>Waris</option>
                            </select>
                        </div>
                        <div>
                            <label for="call_status"> Status</label>
                            <select name="call_status" id="call_status" class="form-select text-white-dark" required>
                                <option value=''>-none-</option>
                                <option>New</option>
                                <option>Pending</option>
                                <option>Canclled</option>
                                <option>Closed</option>
                                <option>Allocated</option>
                            </select>
                        </div>
                        <div>
                            <label for="rson"> Reason </label>
                            <input id="rson" type="text" class="form-input" required />
                        </div>
                    </div>
                </div>
                <div class="relative inline-flex align-middle gap-3 mt-10">
                    <button type="submit" name="save_btn" class="btn btn-success">Save</button>
                    <button type="button" name="close_btn" class="btn btn-danger">Close</button>
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
            console.log('Alpine Intialized !!');
            flatpickr(document.getElementById('purchase_date'), {
                dateFormat: 'd-m-Y',
                defaultDate: this.date1,
            })
        }
    }));
});


function readURL(input, preview, errElement) {
    if (input.files && input.files[0]) {
        var filename = input.files[0].name;
        var reader = new FileReader();
        var extn = filename.split('.').pop().toLowerCase();
        var allowedExtns = ["jpg", "jpeg", "png", "bmp", "webp"];

        console.log(filename, reader, extn)

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
</script>

<?php 
include "footer.php";
?>