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

<div class='p-6' x-data='exportTable'>
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
                            <input name="complaint_num" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="service_center">Service Center</label>
                            <select name="service_center" class="form-select text-white-dark" required>
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
                            <input name="product_srno" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="srno_img">Serial NO. Image</label>
                            <input name="srno_img" type="file"
                                class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 rtl:file:ml-5 file:text-white file:hover:bg-primary"
                                required />
                        </div>
                        <div>
                            <label for="product_modal"> Product Model </label>
                            <input name="product_modal" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="product_modal_img"> Product Model Image </label>
                            <input name="product_modal_img" type="file"
                                class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 rtl:file:ml-5 file:text-white file:hover:bg-primary"
                                required />
                        </div>
                    </div>
                    <div class="w-6/12 px-3 space-y-5">
                        <div>
                            <label for="purchase_date"> Purchase Date </label>
                            <input name="purchase_date" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="purchase_date_img">Purchase Date Image</label>
                            <input id="purchase_date_img" type="file"
                                class="form-input file:py-2 file:px-4 file:border-0 file:font-semibold p-0 file:bg-primary/90 ltr:file:mr-5 rtl:file:ml-5 file:text-white file:hover:bg-primary"
                                required />
                        </div>
                        <div>
                            <label for="technician_name"> Technician </label>
                            <select name="technician_name" class="form-select text-white-dark" required>
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
                            <select name="call_status" class="form-select text-white-dark" required>
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

<?php 
include "footer.php";
?>