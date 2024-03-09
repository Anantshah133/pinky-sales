<?php
include "header.php";

if (isset($_COOKIE['viewId'])) {
    $mode = 'view';
    $viewId = $_COOKIE['viewId'];
    $stmt = $obj->con1->prepare("SELECT c1.*, s1.name as service_type_name FROM `customer_reg` c1, service_type s1 WHERE c1.service_type=s1.id AND c1.id=?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if (isset($_COOKIE['editId'])) {
    $mode = 'edit';
    $editId = $_COOKIE['editId'];
    $stmt = $obj->con1->prepare("SELECT * FROM `customer_reg` where id=?");
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    $stmt->close();
}

if (isset($_REQUEST['update'])) {
    $editId = $_COOKIE['editId'];
    $fname = $_REQUEST['fname'];
    $lname = $_REQUEST['lname'];
    $email = $_REQUEST['mail'];
    $contact = $_REQUEST['contact_num'];
    $alt_contact = $_REQUEST['alt_contact_num'];
    $map_location = $_REQUEST['map_location'];
    $address = $_REQUEST['address'];
    $pincode = $_REQUEST['pincode'];
    $service_type = $_REQUEST['service_type'];
    $product_category = $_REQUEST['product_category'];
    $dealer_name = $_REQUEST['dealer_name'];
    $complaint_date = $_REQUEST['complaint_date'];
    $complaint_time = date('h:i A', strtotime($_REQUEST['complaint_time']));
    $complaint_no = $data['complaint_no'];
    $description = $_REQUEST['description'];

    $stmt = $obj->con1->prepare("UPDATE `customer_reg` SET fname=?,lname=?,email=?,contact=?,alternate_contact=?,map_location=?,address=?,zipcode=?,complaint_no=?,service_type=?,product_category=?,dealer_name=?,description=?, date=?,time=?  WHERE id=?");
    $stmt->bind_param("ssssssssssissssi", $fname, $lname, $email, $contact, $alt_contact, $map_location, $address, $pincode, $complaint_no, $service_type, $product_category, $dealer_name, $description, $complaint_date, $complaint_time, $editId);
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
    $address = $_REQUEST['address'];
    $pincode = $_REQUEST['pincode'];
    $service_type = $_REQUEST['service_type'];
    $product_category = $_REQUEST['product_category'];
    $dealer_name = $_REQUEST['dealer_name'];
    $complaint_date = $_REQUEST['complaint_date'];
    $complaint_time = date('h:i A', strtotime($_REQUEST['complaint_time']));
    $day = Date("d");
    $month = Date("m");
    $year = Date("y");
    $description = $_REQUEST['description'];
    $barcode = $_REQUEST['barcode'];
    $source = "web";
    $joined_date = date("dmy", strtotime($complaint_date));

    // get max customer id - added by Rachna
    $stmt = $obj->con1->prepare("select IFNULL(count(id)+1,1) as customer_id from customer_reg where date ='" . date("Y-m-d", strtotime($complaint_date)) . "'");
    $stmt->execute();
    $row_dailycounter = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $dailycounter = (int) $row_dailycounter["customer_id"];
    $string = str_pad($dailycounter, 4, '0', STR_PAD_LEFT);
    $complaint_no = "ONL" . $joined_date . $string;

    //--------------//
    $complaint_date = date("Y-m-d", strtotime($complaint_date));
    
    try {
        // allocate call - added by Rachna
        // ------- get city by anant
        $stmt = $obj->con1->prepare("SELECT c1.ctnm,a1.* FROM area_pincode a1, city c1 WHERE a1.city_id=c1.srno AND a1.pincode=?");
        $stmt->bind_param("s", $pincode);
        $stmt->execute();
        $res_area=$stmt->get_result();
        $num_area=$res_area->num_rows;
        $city_data = $res_area->fetch_assoc();
        if($num_area>0)
        {
            $fetched_city_id = $city_data["city_id"];
        }
        else{
            $fetched_city_id = 0;
        }
        $stmt->close();

        $stmt = $obj->con1->prepare("INSERT INTO `customer_reg`(`fname`, `lname`, `email`, `contact`, `alternate_contact`, `area`, `map_location`, `address`, `zipcode`, `complaint_no`, `service_type`, `product_category`, `dealer_name`, `description`, `barcode`, `source`, `date`, `time`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssissssiissssss", $fname, $lname, $email, $contact, $alt_contact, $fetched_city_id, $map_location, $address, $pincode, $complaint_no, $service_type, $product_category, $dealer_name, $description, $barcode, $source, $complaint_date, $complaint_time);
        $Resp = $stmt->execute();
        $stmt->close();

        // echo "<br/> Insert Customer_reg :- INSERT INTO `customer_reg`(`fname`, `lname`, `email`, `contact`, `alternate_contact`, `map_location`, `address`, `zipcode`, `complaint_no`, `service_type`, `product_category`, `dealer_name`, `description`, `barcode`,`source`, `date`, `time`) VALUES (". $fname.", ". $lname.", ". $email.", ". $contact.", ". $alt_contact.", ". $map_location.", ". $address.", ". $pincode.", ". $complaint_no.", ". $service_type.", ". $product_category.", ". $dealer_name.", ". $description.", ". $barcode.", ". $source.", ". $complaint_date.", ". $complaint_time.")";

        // ------- get service center from city by anant
        
        $stmt = $obj->con1->prepare("SELECT * FROM `service_center` WHERE area=?");
        $stmt->bind_param("i", $fetched_city_id);
        $stmt->execute();
        $service_center = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // insert into call allocation
        $product_serial_no = "";
        $product_model = "";
        $purchase_date = "";
        $techinician = 0;
        $allocation_date = "";
        $allocation_time = "";
        $status = "new";
        //---------------//

        $stmt = $obj->con1->prepare("INSERT INTO `call_allocation`(`complaint_no`, `service_center_id`, `product_serial_no`, `product_model`, `purchase_date`, `technician`, `allocation_date`, `allocation_time`, `status`) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sisssisss", $complaint_no, $service_center["id"], $product_serial_no, $product_model, $purchase_date, $techinician, $allocation_date, $allocation_time, $status);
        $result = $stmt->execute();
        $stmt->close();


        //  echo "<br/> Insert Call allocation :- INSERT INTO `call_allocation`(`complaint_no`, `service_center_id`, `product_serial_no`, `product_model`, `purchase_date`, `technician`, `allocation_date`, `allocation_time`, `status`) VALUES (" . $complaint_no . " " . $service_center['id'] . " " . $product_serial_no . " " . $product_model . " " . $purchase_date . " " . $techinician . " " . $allocation_date . " " . $allocation_time . " " . $status . ")";

        // $noti_msg = "New Complaint recieved";
        // $noti_type = "";
        // $admin_status = 1;
        // $admin_play_status = 1;
        // $service_status = 0;
        // $service_play_status = 0;

        // $stmt = $obj->con1->prepare("INSERT INTO `notification`(`complaint_no`, `type`, `msg`, `admin_status`, `admin_play_status`, `service_status`, `service_play_status`) VALUES (?,?,?,?,?,?,?)");
        // $stmt->bind_param("sssiiii", $complaint_no, $noti_type, $noti_type, $admin_status, $admin_play_status, $service_status, $service_play_status);
        // $Response = $stmt->execute();
        // $stmt->close();

        // if(!$Response){
        //     echo $obj->con1->error;
        //     throw new Exception("Problem in adding! " . strtok($obj->con1->error, '('));
        // }

        if (!$Resp) {
            echo $obj->con1->error;
            throw new Exception("Problem in adding! " . strtok($obj->con1->error, '('));
        }
    } catch (\Exception $e) {
        setcookie("sql_error", urlencode($e->getMessage()), time() + 3600, "/");
        echo "<br/>".urlencode($e->getMessage());
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
    <div class="panel mt-2">
        <div class='flex items-center justify-between mb-5'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">Complaint / Demo -
                <?php echo isset($mode) ? ($mode == 'view' ? 'View' : ($mode == 'edit' ? 'Edit' : 'Add')) : 'Add'; ?>
            </h5>
        </div>
        <div class="mb-5">
            <form method="post" id="mainForm" enctype="multipart/form-data">
                <div class="flex flex-wrap mb-1">
                    <div class="w-6/12 px-3 space-y-5">
                        <div>
                            <label for="fname"> First Name </label>
                            <input name="fname" id="fname" type="text" class="form-input" value="<?php echo isset($mode) ? $data['fname'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
                        </div>
                        <div>
                            <label for="lname"> Last Name </label>
                            <input name="lname" id="lname" type="text" class="form-input" value="<?php echo isset($mode) ? $data['lname'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
                        </div>
                        <div>
                            <label for="mail">Email</label>
                            <input name="mail" id="mail" type="email" class="form-input" pattern="^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$" title="Invalid Email Format" value="<?php echo isset($mode) ? $data['email'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
                        </div>
                        <div>
                            <label for="contact_num">Contact </label>
                            <div class="flex">
                                <div class="bg-[#eee] flex justify-center items-center ltr:rounded-l-md rtl:rounded-r-md px-3 font-semibold border ltr:border-r-0 rtl:border-l-0 border-[#e0e6ed] dark:border-[#17263c] dark:bg-[#1b2e4b]">+91</div>
                                <input name="contact_num" id="contact_num" type="tel" placeholder="1234567890" class="form-input ltr:rounded-l-none rtl:rounded-r-none" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="<?php echo isset($mode) ? $data['contact'] : '' ?>" <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> maxlength="10" minlength="10" pattern="[0-9]+" title="Please enter numbers only" required />
                            </div>
                        </div>
                        <div>
                            <label for="alt_contact_num"> Alternate Contact </label>
                            <div class="flex">
                                <div class="bg-[#eee] flex justify-center items-center ltr:rounded-l-md rtl:rounded-r-md px-3 font-semibold border ltr:border-r-0 rtl:border-l-0 border-[#e0e6ed] dark:border-[#17263c] dark:bg-[#1b2e4b]">+91</div>
                                <input name="alt_contact_num" id="alt_contact_num" type="tel" placeholder="1234567890" class="form-input ltr:rounded-l-none rtl:rounded-r-none"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="<?php echo isset($mode) ? $data['alternate_contact'] : '' ?>" <?php echo isset ($mode) && $mode == 'view' ? 'readonly' : '' ?> maxlength="10" minlength="10" pattern="[0-9]+" title="Please enter numbers only" />
                            </div>
                        </div>
                        <div>
                            <label for="address">Address </label>
                            <textarea autocomplete="on" name="address" id="address" class="form-textarea" rows="2"
                                value="" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?>><?php echo isset($mode) ? $data['address'] : '' ?></textarea>
                        </div>
                        <div>
                            <label for="pincode"> Pincode </label>
                            <input name="pincode" id="pincode" type="text" class="form-input" pattern="^[1-9][0-9]{5}$" title="enter valid pincode" maxlength="6" required onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="<?php echo isset($mode) ? $data['zipcode'] : '' ?>"  onblur="<?php echo isset($_SESSION['type_center']) ? 'checkPincode(this)' : '' ?>" <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
                            <p class="mt-3 text-danger text-base font-bold" id="demo"></p>
                        </div>
                    </div>
                    <div class="w-6/12 px-3 space-y-5">
                        <div>
                            <label for="product_category">Product Category </label>
                            <select name="product_category" id="product_category" class="form-select text-white-dark" <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?> required onchange="getServiceType(this.value)">
                                <option value="">Choose Product Category</option>
                                <?php
                                    $query = $obj->con1->prepare("SELECT * FROM `product_category`");
                                    $query->execute();
                                    $Resp = $query->get_result();
                                    while ($row = mysqli_fetch_array($Resp)) {
                                        ?>
                                            <option value="<?php echo $row["id"]; ?>" <?php echo isset($mode) && $row['id'] == $data['product_category'] ? 'selected' : '' ?>>
                                                <?php echo $row["name"]; ?>
                                            </option>
                                        <?php
                                    }
                                    $query->close();
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="service_type"> Service Type </label>
                            <select name="service_type" id="service_type" class="form-select text-white-dark" required <?php echo isset($mode) && $mode == 'view' ? 'disabled' : ''?>>
                                <option value=""><?php echo isset($mode) && $mode == 'view' ? $data["service_type_name"] : 'Choose Service Type' ?></option>
                            </select>
                        </div>
                        <div>
                            <label for="description">Description </label>
                            <input name="description" id="description" type="text" class="form-input" value="<?php echo isset($mode) ? $data['description'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
                        </div>
                        <div>
                            <label for="dealer_name">Dealer Name </label>
                            <input name="dealer_name" id="dealer_name" type="text" class="form-input" value="<?php echo isset($mode) ? $data['dealer_name'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
                        </div>
                        <div>
                            <label for="barcode">Barcode </label>
                            <input name="barcode" id="barcode" type="text" class="form-input" value="<?php echo isset($mode) ? $data['barcode'] : '' ?>" required <?php echo isset($mode) && $mode == 'view' ? 'readonly' : '' ?> />
                        </div>
                        <div x-data="cmplnDate">
                            <label>Date </label>
                            <input x-model="date2" name="complaint_date" id="complaint_date" class="form-input" value="" required <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> />
                        </div>
                        <div x-data="complaintTime">
                            <label>Time </label>
                            <input name="complaint_time" id="complaint_time" class="form-input" required <?php echo isset($mode) && $mode == 'view' ? 'disabled' : '' ?> />
                        </div>
                    </div>
                </div>
                <div class="relative inline-flex align-middle gap-3 mt-4">
                        <!-- Save/Update button -->
                        <button type="submit" name="<?php echo isset($mode) && $mode == 'edit' ? 'update' : 'save' ?>" id="save" class="btn btn-success" onclick="return validateAndDisable()" <?php echo isset($mode) && $mode == 'view' ? 'style="display:none;"' : '' ?>>
                            <?php echo isset($mode) && $mode == 'edit' ? 'Update' : 'Save' ?>
                        </button>
                        <!-- Close button -->
                        <button type="button" class="btn btn-danger" onclick="window.location='complaint_demo.php'">
                            Close
                        </button>
                    </div>
                <!------ Hidden Inputs ------>
                <input type="hidden" name="map_location" id="map_location">
            </form>
        </div>
    </div>
</div>


<script>
    function resetForm() {
        window.location = "complaint_demo.php";
    }

    function getServiceType(pid, stid = 0){
        const http = new XMLHttpRequest();
        http.open("GET", `ajax/get_services.php?pid=${pid}&stid=${stid}`);
        http.send();
        http.onload = function(){
            document.getElementById("service_type").innerHTML = http.responseText;
        }
    }

    function checkPincode(input){
        let pincode = input.value;
        let city_id = <?php echo isset($_SESSION['type_center']) ? $_SESSION['sc_city'] : 0 ?>;

        if(input.value.length == 6){
            const obj = new XMLHttpRequest();
            obj.open("GET", `ajax/check_pincode_center.php?pincode=${pincode}&cityId=${city_id}`, false);
            obj.send();

            if(obj.status == 200){
                let res = obj.responseText;
                if(res < 1){
                    input.value = "";
                    input.focus();
                    document.getElementById("demo").innerHTML = "Please enter the valid pincode for your center !";
                }
            } else {
                document.getElementById("demo").innerHTML = "";
            }
        }
    }

    document.addEventListener("alpine:init", () => {
        let todayDate = new Date();
        let formattedToday = todayDate.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }).split('/').join('-')

        Alpine.data("cmplnDate", () => ({
            date2: '<?php echo isset($mode) ? date("d-m-Y", strtotime($data['date'])) : date("d-m-Y") ?>',
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
            <?php if (!isset($mode)) { ?>
                    time: todayDate.toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }),
            <?php } ?>
            init() {
                flatpickr(document.getElementById('complaint_time'), {
                    defaultDate: '<?php echo isset($mode) ? $data['time'] : date("h:i a") ?>',
                    noCalendar: true,
                    enableTime: true,
                    dateFormat: 'h:i K'
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

    document.addEventListener("DOMContentLoaded", function () {
        getLocation();
    });
</script>

<?php
if (isset($mode) && $mode == 'edit') {
    echo "<script>
        const pid = document.getElementById('product_category').value;
        const stid =" . json_encode($data['service_type']) . ";
        console.log(pid, stid);
        getServiceType(pid, stid);
    </script>";
}
?>

<?php
include "footer.php";
?>