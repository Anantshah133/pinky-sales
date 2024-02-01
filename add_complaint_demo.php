<?php 
    include "header.php";

    if(isset($_POST['save_btn'])){
        $fname = $_REQUEST['fname'];
        $lname = $_REQUEST['lname'];
        $email = $_REQUEST['mail'];
        $contact = $_REQUEST['contact_num'];
        $alt_contact = $_REQUEST['alt_contact_num'];
        $map_location = $_REQUEST['map_location'];
        $area = $_REQUEST['area'];
        $address = $_REQUEST['address'];
        $pincode = $_REQUEST['pincode'];
        $complaint_no = 'ORP3101240010';
        $service_type = $_REQUEST['service_type'];
        $product_category = $_REQUEST['product_category'];
        $dealer_name = $_REQUEST['dealer_name'];
        $date = $_REQUEST['complaint_date'];
        $time = $_REQUEST['complaint_time'];

        try {
            $stmt = $obj->con1->prepare("INSERT INTO `customer_reg`(`fname`, `lname`, `email`, `contact`, `alternate_contact`, `area`, `map_location`, `address`, `zipcode`, `complaint_no`, `service_type`, `product_category`, `dealer_name`, `date`, `time`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssisssssisss", $fname, $lname, $email, $contact, $alt_contact, $area, $map_location, $address, $pincode, $complaint_no, $service_type, $product_category, $dealer_name, $date, $time);
            $Resp=$stmt->execute();
            
            if(!$Resp) {
                throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
            }
            // else{
            //     $stmt = $obj->con1->prepare("INSERT INTO `customer_reg`(`fname`, `lname`, `email`, `contact`, `alternate_contact`, `area`, `map_location`, `address`, `zipcode`, `complaint_no`, `service_type`, `product_category`, `dealer_name`, `date`, `time`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            //     $stmt->bind_param("sssssisssssisss", $fname, $lname, $email, $contact, $alt_contact, $area, $map_location, $address, $pincode, $complaint_no, $service_type, $product_category, $dealer_name, $date, $time);
            //     $Resp=$stmt->execute();
            // }
            $stmt->close();
        }
        catch(\Exception  $e) {
            setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
        }
    
        if($Resp) {
            setcookie("msg", "data",time()+3600,"/");
            header("location:complaint_demo.php");
        }
        else {
            setcookie("msg", "fail",time()+3600,"/");
            header("location:complaint_demo.php");
        }
    }
?>

<div class='p-6'>
    <div class="panel mt-6">
        <div class='flex items-center justify-between mb-5'>
            <h5 class="text-2xl text-primary font-semibold dark:text-white-light">Add - Complaint / Demo</h5>
        </div>
        <div class="mb-5">
            <form id="call_form" method="post" onsubmit="" enctype="multipart/form-data">
                <div class="flex flex-wrap">
                    <div class="w-6/12 px-3 space-y-5">
                        <div>
                            <label for="fname"> First Name </label>
                            <input name="fname" id="fname" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="lname"> Last Name </label>
                            <input name="lname" id="lname" type="text" class="form-input" required />
                        </div>
                        <div>
                            <label for="mail">Email</label>
                            <input name="mail" id="mail" type="email" class="form-input" required />
                        </div>
                        <div>
                            <label for="contact_num"> Contact </label>
                            <input name="contact_num" id="contact_num" type="number" class="form-input" required />
                        </div>
                        <div>
                            <label for="alt_contact_num"> Alternate Contact </label>
                            <input name="alt_contact_num" id="alt_contact_num" type="number" class="form-input" />
                        </div>
                        <div>
                            <label for="area"> Area </label>
                            <select name="area" id="area" class="form-select text-white-dark" required>
                                <option value="">Choose Area</option>
                                <option value="1">Surat</option>
                            </select>
                        </div>
                        <div>
                            <label for="address"> Address </label>
                            <textarea autocomplete="on" name="address" id="address" class="form-textarea" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="w-6/12 px-3 space-y-5">
                        <div>
                            <label for="pincode"> Pincode </label>
                            <input name="pincode" id="pincode" type="number" class="form-input" />
                        </div>
                        <div>
                            <label for="service_type"> Service Type </label>
                            <select name="service_type" id="service_type" class="form-select text-white-dark" required>
                                <option value="">Choose Service Type</option>
                                <option value="6">Cleaning</option>
                            </select>
                        </div>
                        <div>
                            <label for="product_category"> Product Category </label>
                            <select name="product_category" id="product_category" class="form-select text-white-dark"
                                required>
                                <option value="">Choose Product Category</option>
                                <option value="4">Cooler</option>
                            </select>
                        </div>
                        <div>
                            <label for="dealer_name"> Dealer Name </label>
                            <input name="dealer_name" id="dealer_name" type="text" class="form-input" required />
                        </div>
                        <div x-data="cmplnDate">
                            <label>Date </label>
                            <input x-model="date2" name="complaint_date" id="complaint_date" class="form-input" />
                        </div>
                        <div x-data="complaintTime">
                            <label>Time </label>
                            <input x-model="time" name="complaint_time" id="complaint_time" class="form-input" />
                        </div>
                    </div>
                </div>
                <div class="relative inline-flex align-middle gap-3 mt-10">
                    <button type="submit" id="save_btn" name="save_btn" class="btn btn-success">Save</button>
                    <button type="button" id="close_btn" name="close_btn" class="btn btn-danger"
                        onclick="window.location='complaint_demo.php'">Cancel</button>
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

document.addEventListener("DOMContentLoaded", function () {
    getLocation();
});
</script>
<?php 
    include "footer.php";
?>