<?php
include "header.php";

if (isset($_REQUEST["viewId"])) {
    $mode = 'view';
    $viewId = $_REQUEST["viewId"];
    $stmt = $obj->con1->prepare("SELECT * FROM `customer_reg` where id=?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if (isset($_REQUEST["editId"])) {
    $mode = 'edit';
    $viewId = $_REQUEST["editId"];
    $stmt = $obj->con1->prepare("SELECT * FROM `customer_reg` where id=?");
    $stmt->bind_param('i', $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if(isset($_REQUEST['update'])){
    $editId = $_REQUEST['editId'];
    $fname = $_REQUEST['fname'];
    $lname = $_REQUEST['lname'];
    $email = $_REQUEST['mail'];
    $contact = $_REQUEST['contact_num'];
    $alt_contact = $_REQUEST['alt_contact_num'];
    $map_location = $_REQUEST['map_location'];
    $area = $_REQUEST['area'];
    $address = $_REQUEST['address'];
    $pincode = $_REQUEST['pincode'];
    $service_type = $_REQUEST['service_type'];
    $product_category = $_REQUEST['product_category'];
    $dealer_name = $_REQUEST['dealer_name'];
    $date = $_REQUEST['complaint_date'];
    $time = $_REQUEST['complaint_time'];
    $day = Date("d");
    $month = Date("m");
    $year = Date("y");

    $stmt = $obj->con1->prepare("UPDATE `customer_reg` SET fname=?,lname=?,email=?,contact=?,alternate_contact=?,area=?,map_location=?,address=?,zipcode=?,complaint_no=?,service_type=?,product_category=?,dealer_name=?,date=?,time=?  WHERE id=?");
    $stmt->bind_param("sssssisssssisssi", $fname, $lname, $email, $contact, $alt_contact, $area, $map_location, $address, $pincode, $complaint_no, $service_type, $product_category, $dealer_name, $date, $time, $editId);
    $Res = $stmt->execute();
    $stmt->close();

    if ($Res) {
        setcookie("msg", "update", time() + 3600, "/");
        header("location:complaint_demo.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:complaint_demo.php");
    }
}

if (isset($_POST['save'])) {
    $fname = $_REQUEST['fname'];
    $lname = $_REQUEST['lname'];
    $email = $_REQUEST['mail'];
    $contact = $_REQUEST['contact_num'];
    $alt_contact = $_REQUEST['alt_contact_num'];
    $map_location = $_REQUEST['map_location'];
    $area = $_REQUEST['area'];
    $address = $_REQUEST['address'];
    $pincode = $_REQUEST['pincode'];
    $service_type = $_REQUEST['service_type'];
    $product_category = $_REQUEST['product_category'];
    $dealer_name = $_REQUEST['dealer_name'];
    $date = $_REQUEST['complaint_date'];
    $time = $_REQUEST['complaint_time'];
    $day = Date("d");
    $month = Date("m");
    $year = Date("y");

    // get max customer id - added by Rachna
    $stmt = $obj->con1->prepare("select IFNULL(count(id)+1,1) as customer_id from customer_reg where date ='" . date("d-m-Y") . "'");
    $stmt->execute();
    $row_dailycounter = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $dailycounter = (int)$row_dailycounter["customer_id"];
    $string = str_pad($dailycounter, 4, '0', STR_PAD_LEFT);
    $complaint_no = "ONL" . $day . $month . $year . $string;
    $date = date("d-m-Y");
    $time = date("h:i A");
    //--------------//

    try {
        $stmt = $obj->con1->prepare("INSERT INTO `customer_reg`(`fname`, `lname`, `email`, `contact`, `alternate_contact`, `area`, `map_location`, `address`, `zipcode`, `complaint_no`, `service_type`, `product_category`, `dealer_name`, `date`, `time`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssisssssisss", $fname, $lname, $email, $contact, $alt_contact, $area, $map_location, $address, $pincode, $complaint_no, $service_type, $product_category, $dealer_name, $date, $time);
        $Resp = $stmt->execute();
        $stmt->close();

        //allocate call -added by Rachna

        // get service area

        $stmt = $obj->con1->prepare("select * from service_center where area=? ");
        $stmt->bind_param("i", $area);
        $stmt->execute();
        $service_center = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        //insert into call allocation
        $product_serial_no = "";
        $product_model = "";
        $purchase_date = "";
        $techinician = "";
        $allocation_date = "";
        $allocation_time = "";
        $status = "new";
        //---------------//



        $stmt = $obj->con1->prepare("INSERT INTO `call_allocation`( `complaint_no`, `service_center_id`, `product_serial_no`, `product_model`, `purchase_date`, `technician`, `allocation_date`, `allocation_time`, `status`) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sisssisss", $complaint_no, $service_center["id"], $product_serial_no, $product_model, $purchase_date, $techinician, $allocation_date, $allocation_time, $status);
        $result = $stmt->execute();
        $stmt->close();
        if (!$Resp) {
            throw new Exception("Problem in adding! " . strtok($obj->con1->error,  '('));
        }
    } catch (\Exception  $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
    }

    if ($Resp) {
        setcookie("msg", "data", time() + 3600, "/");
        header("location:complaint_demo.php");
    } else {
        setcookie("msg", "fail", time() + 3600, "/");
        header("location:complaint_demo.php");
    }
}
?>

<div class='p-6'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-5'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">Complaint / Demo -
            <?php echo isset($mode) ? ($mode == 'view' ? 'View' : ($mode == 'edit' ? 'Edit' : 'Add')) : 'Add'; ?>
            </h5>
        </div> 
        <div class="mb-5">
            <form id="call_form" method="post" onsubmit="" enctype="multipart/form-data">
                <div class="flex flex-wrap">
                    <div class="w-6/12 px-3 space-y-5">
                        <div>
                            <label for="fname"> First Name </label>
                            <input name="fname" id="fname" type="text" class="form-input" value="<?php echo (isset($mode)) ? $data['fname'] : '' ?>" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> />
                        </div>
                        <div>
                            <label for="lname"> Last Name </label>
                            <input name="lname" id="lname" type="text" class="form-input" value="<?php echo (isset($mode)) ? $data['lname'] : '' ?>" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> />
                        </div>
                        <div>
                            <label for="mail">Email</label>
                            <input name="mail" id="mail" type="email" class="form-input" value="<?php echo (isset($mode)) ? $data['email'] : '' ?>" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> />
                        </div>
                        <div>
                            <label for="contact_num"> Contact </label>
                            <input name="contact_num" id="contact_num" type="tel" class="form-input" value="<?php echo (isset($mode)) ? $data['contact'] : '' ?>" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> />
                        </div>
                        <div>
                            <label for="alt_contact_num"> Alternate Contact </label>
                            <input name="alt_contact_num" id="alt_contact_num" type="tel" class="form-input" value="<?php echo (isset($mode)) ? $data['alternate_contact'] : '' ?>" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> /> 
                        </div>
                        <div>
                            <label for="area"> City </label>
                            <select name="area" id="area" class="form-select text-white-dark" required>
                                <option value="">Choose City</option>
                                <?php 
                                    $query = $obj->con1->prepare("SELECT * FROM `city` WHERE status='enable'");
                                    $query->execute();
                                    $Resp = $query->get_result();
                                    while($row = mysqli_fetch_array($Resp)){
                                ?>
                                    <option value="<?php echo $row['srno'] ?>"
                                        <?php echo isset($mode) && $row['srno'] == $data['area'] ? 'selected' : '' ?>
                                    >
                                        <?php echo $row['ctnm'] ?>
                                    </option>
                                <?php 
                                    }
                                    $query->close();
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="address"> Address </label>
                            <textarea autocomplete="on" name="address" id="address" class="form-textarea" rows="2" value="" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?>><?php echo (isset($mode)) ? $data['address'] : '' ?></textarea>
                        </div>
                    </div>
                    <div class="w-6/12 px-3 space-y-5">
                        <div>
                            <label for="pincode"> Pincode </label>
                            <input name="pincode" id="pincode" type="number" class="form-input" value="<?php echo (isset($mode)) ? $data['zipcode'] : '' ?>" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> /> 
                        </div>
                        <div>
                            <label for="service_type"> Service Type </label>
                            <select name="service_type" id="service_type" class="form-select text-white-dark" required>
                                <option value="">Choose Service Type</option>
                                <?php 
                                    $query = $obj->con1->prepare("SELECT * FROM `service_type` WHERE status='enable'");
                                    $query->execute();
                                    $Resp = $query->get_result();
                                    while($row = mysqli_fetch_array($Resp)){
                                ?>
                                    <option value="<?php echo $row["id"]; ?>"
                                    <?php echo isset($mode) && $row['id'] == $data['service_type'] ? 'selected' : '' ?>
                                    >
                                        <?php echo $row["name"]; ?>
                                    </option>
                                <?php 
                                    }
                                    $query->close();
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="product_category"> Product Category </label>
                            <select name="product_category" id="product_category" class="form-select text-white-dark" required>
                                <option value="">Choose Product Category</option>
                                <?php 
                                    $query = $obj->con1->prepare("SELECT * FROM `product_category`");
                                    $query->execute();
                                    $Resp = $query->get_result();
                                    while($row = mysqli_fetch_array($Resp)){
                                ?>
                                    <option value="<?php echo $row["id"]; ?>" <?php echo isset($mode) && $row['id'] == $data['product_category'] ? 'selected' : '' ?>
                                    >
                                        <?php echo $row["name"]; ?>
                                    </option>
                                <?php 
                                    }
                                    $query->close();
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="dealer_name"> Dealer Name </label>
                            <input name="dealer_name" id="dealer_name" type="text" class="form-input" value="<?php echo (isset($mode)) ? $data['dealer_name'] : '' ?>" required
                            <?php echo isset($mode) && $mode == 'view' ? 'readonly' : ''?> /> 
                        </div>
                        <div x-data="cmplnDate">
                            <label>Date </label>
                            <input x-model="date2" name="complaint_date" id="complaint_date" class="form-input" value="<?php echo (isset($mode)) ? $data['date'] : '' ?>" required
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?> /> 
                        </div>
                        <div x-data="complaintTime">
                            <label>Time </label>
                            <input x-model="time" name="complaint_time" id="complaint_time" class="form-input" value="<?php echo (isset($mode)) ? $data['time'] : '' ?>" required
                            <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?> />  
                        </div>
                    </div>
                </div>
                <div class="relative inline-flex align-middle gap-3 mt-4 <?php echo isset($mode) && $mode == 'view' ? "hidden" : ""; ?>">
                    <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save' ?>" id="save" class="btn btn-success"><?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?> </button>
                    <button type="button" id="close_btn" name="close_btn" class="btn btn-danger" onclick="window.location='complaint_demo.php'">Close</button>
                </div>
                <!------ Hidden Inputs ------>
                <input type="hidden" name="map_location" id="map_location">
            </form>
        </div>
    </div>
</div>


<script>
    function resetForm() {
        window.location = "complaint_demo.php"
    }

    document.addEventListener("alpine:init", () => {
        let todayDate = new Date();
        let formattedToday = todayDate.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }).split('/').join('-')

        Alpine.data("cmplnDate", () => ({
            date2: formattedToday,
            init() {
                flatpickr(document.getElementById('complaint_date'), {
                    dateFormat: 'd-m-Y',
                    minDate: formattedToday,
                    defaultDate: this.date2,
                    minDate: "today",
                })
            }
        }));

        Alpine.data("complaintTime", () => ({
            time: todayDate.toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }),
            init() {
                flatpickr(document.getElementById('complaint_time'), {
                    defaultDate: this.time,
                    noCalendar: true,
                    enableTime: true,
                    dateFormat: 'H:i'
                });
            }
        }));
    });

    function showPosition(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        const mapLocation = `${latitude},${longitude}`;
        document.getElementById('map_location').value = mapLocation;
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        getLocation();
    });
</script>

<?php
include "footer.php";
?>