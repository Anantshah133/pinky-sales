<?php

use Slim\Slim;
use Stripe\StripeClient;

ini_set('display_errors', 1);
error_reporting(E_ALL);
//including the required files
require_once '../include/DbOperation.php';
require '../libs/Slim/Slim.php';

date_default_timezone_set("Asia/Kolkata");
Slim::registerAutoloader();


$app = new Slim();

$app->post('/customer_registeration', function () use ($app) {
    
    $data = array();

    $response = new stdClass();

    $req_data = json_decode($app->request->post('data'));
    $keys = array_keys(json_decode($app->request->post('data'), true));   

    verifyRequiredParams_json(array('firstname','lastname','contact','address','zipcode','service_type','product_category','email','dealer_name'),$keys);
    $fname = $req_data->firstname;
    $lname = $req_data->lastname;
    $contact = $req_data->contact;

    $alternate_contact = $req_data->alternate_contact;
    $address = $req_data->address;
   
    $map_location = $req_data->map_location;
    $zipcode = $req_data->zipcode;
    $service_type = $req_data->service_type;
    $product_category = $req_data->product_category;
    $email = $req_data->email;
    $dealer_name = $req_data->dealer_name;
    $description = $req_data->description;
    $barcode = $req_data->barcode;
    $source = $req_data->source;
    
   // $device_token = isset($req_data->device_token)?$req_data->device_token:"";
   // $type = isset($req_data->type)?$req_data->type:"";
   $device_token="";
   $type="";
    $day = Date("d");
    $month = Date("m");
    $year = Date("y");
    $db = new DbOperation();
    

    $maxno = $db->getmaxcustomer();
    $row_max = $maxno->fetch_assoc();
    $dailicounter=$db->get_dailycounter();
    $row_dailycounter = $dailicounter->fetch_assoc();
    // echo "daily=".$row_dailycounter["customer_id"];
    $dailycounter=(int)$row_dailycounter["customer_id"];
    
    
     $string=str_pad($dailycounter, 4, '0', STR_PAD_LEFT); 

     // get area from zipcode
     $area_data=$db->get_area($zipcode);
     if($area_data->num_rows>0)
     {
        $area_row=$area_data->fetch_assoc();
        $city=$area_row["city_id"];
     }
     else{
        $city=21;
     }
     

     // get service center from zipcode
     
   // $complaint_no = "ORP".$day . $month . $year . $row_max["customer_id"].$string;
     $complaint_no = "ONL".$day . $month . $year .$string;
    $res = $db->do_reg_customer($fname, $lname, $email, $contact, $alternate_contact, $city, $zipcode, $address, $service_type, $product_category, $dealer_name, $complaint_no,$description,$barcode,$source,$map_location);


    if ($res == 0) {
        // get sercive center
        $service_center=$db->get_service_center($city);
        if($service_center["id"]=="")
        {
            $service_center_id=0;
        }
        else{
            $service_center_id=$service_center["id"];

        }
       
        
        //insert into call allocation
        $product_serial_no="";
        $product_model="";
        $purchase_date="";
        $techinician="";
        $allocation_date="";
        $allocation_time="";
        $status="new";
        $res = $db->call_allocation_add($complaint_no,$service_center_id, $product_serial_no, $product_model, $purchase_date, $techinician,$allocation_date,$allocation_time,$status);

        $user = $db->getUser($contact);

        //insert into send mail
        $sendmail=$db->send_mail($complaint_no);

        // send admin notification
        $typ='new_complaint';
        
        $msg="New complaint received";
        $admin_noti=$db->add_notification($complaint_no,$typ,$msg);

        // $response->id = $user['id'];
        $response->id = $complaint_no;

        // send notification to service center

        $res_token_android = $db->get_service_center_device_android($service_center["id"]);
        $res_token_admin_android = $db->get_admin_device_android();
        
        $inc = 0;
        $reg_ids = array();
        $reg_ids_android = array();
        $reg_ids_admin_android = array();
        $count_array = array();
        $not = new stdClass();
        $title = "#" .$complaint_no;
        $body = "New complaint received";        
        $not->message = "New complaint received";
        $not->title = "#" . $complaint_no;
        $not->complaint_no = $complaint_no;        
        $not->body = "New complaint received";
        $inc2 = 0;
        $inc3 = 0;
        while ($token_android = mysqli_fetch_array($res_token_android)) {
           
            $reg_ids_android[$inc2++] = $token_android['token'];
        }
        send_notification_android($not, $reg_ids_android);

        // send notification to admin

        while ($token__admin_android = mysqli_fetch_array($res_token_admin_android)) {
           
            $reg_ids_admin_android[$inc3++] = $token__admin_android['token'];
        }
        

        send_notification_android($not, $reg_ids_admin_android);

        // add user device 
        $check_user_device=$db->check_user_device($contact,$type);
        if($check_user_device->num_rows>0)
        {
            $update_user_device=$db->update_device($contact,$device_token,$type);
        }
        else
        {
            $db->insert_device($contact,$device_token,$type);
        }

        $data['result'] = true;
        $data['message'] = "Registered successfully";
        $data['response'] = $response;

        echoResponse(201, $data);
    } else if ($res == 1) {

        $data['result'] = false;
        $data['message'] = "Oops! An error occurred while registereing";
        $data['response'] = $response;

        echoResponse(200, $data);
    }


});

/* track complaint
 *  param: complaint_no
 *  method:post
 */
$app->post('/track_complaint', function () use ($app) {

    verifyRequiredParams(array('complaint_no'));
    $complaint_no = $app->request->post('complaint_no');


    $db = new DbOperation();
    $data = array();
    //$data["response"]=array();

    $complaint = $db->track_complaint($complaint_no);
    if ($complaint->num_rows > 0) {


        $data['result'] = true;
        $data['message'] = "";
        $data["response"] = array();
        $cust_detail = array();
        // $cust_detail["product_detail"]=array();
        while ($complaint_data = $complaint->fetch_assoc()) {


            $response = new stdClass();
            foreach ($complaint_data as $key => $value) {

                $response->$key = strval($value);

            }
            $complaint_number=$complaint_data["complaint_no"];
            /*$response->serial_no = "456";
            $response->product_model = "model134";
            $response->purchase_date = "02/09/2021";*/
            $response->serial_no = "";
            $response->product_model = "";
            $response->purchase_date = "";
            $cust_detail["product_detail"] = $response;


            $call_allocation = new stdClass();
            /*$call_allocation->technician_name = "xyz";
            $call_allocation->technician_phone = "9876546210";
            $call_allocation->date = "01/09/2021";
            $call_allocation->time = "05:00 PM";
            $call_allocation->parts_used = "abc";
            $call_allocation->call_type = "xyz";
            $call_allocation->service_charge = "500";
            $call_allocation->parts_charge = "1000";
            $call_allocation->status = "Closed";*/
            // fetch call detail

            $call_detail=$db->call_detail($complaint_number);
            //print_r($call_detail);
            if (is_array($call_detail) || is_object($call_detail))
            {
                foreach ($call_detail as $key2 => $value2) {

                    $call_allocation->$key2 = strval($value2);

                }
            }
            
            $cust_detail["call_allocation"] = $call_allocation;
            array_push($data["response"], $cust_detail);
        }

    } else {
        $data['result'] = false;
        $data['message'] = "Data not found";

    }


    echoResponse(200, $data);
});

/* area list
 *  param:
 *  method:post
 */
$app->get('/service_area_list', function () use ($app) {

    //verifyRequiredParams(array(''));
    $db = new DbOperation();
    $data = array();

    $area = $db->service_area_list();
    if ($area->num_rows > 0) {


        $data['result'] = true;
        $data['message'] = "";
        $data["response"] = array();

        while ($area_list = $area->fetch_assoc()) {

            $response = new stdClass();
            foreach ($area_list as $key => $value) {
                $response->$key = $value;
            }
            array_push($data["response"], $response);
        }

    } else {
        $data['result'] = false;
        $data['message'] = "Data not found";

    }
    echoResponse(200, $data);
});

/* area list
 *  param:
 *  method:post
 */
$app->post('/get_product_category', function () use ($app) {

    //verifyRequiredParams(array(''));
    $service_type = $app->request->post('service_type');

    $db = new DbOperation();
    $data = array();


    if($service_type=="Complaint")
    {
        $area = $db->get_product_category();
    }
    else
    {
        $area = $db->get_led_product_category();
    }
    
    if ($area->num_rows > 0) {


        $data['result'] = true;
        $data['message'] = "";
        $data["response"] = array();

        while ($area_list = $area->fetch_assoc()) {


            $response = new stdClass();
            foreach ($area_list as $key => $value) {
                $response->$key = $value;
            }
            array_push($data["response"], $response);
        }


    } else {
        $data['result'] = false;
        $data['message'] = "Data not found";

    }


    echoResponse(200, $data);
});

/* city
 *  param:
 *  method:post
 */
$app->get('/city_list', function () use ($app) {

    //verifyRequiredParams(array(''));
    $db = new DbOperation();
    $data = array();

    $city = $db->city_list();
    if ($city->num_rows > 0) {


        $data['result'] = true;
        $data['message'] = "";
        $data["response"] = array();

        while ($city_list = $city->fetch_assoc()) {

            $response = new stdClass();
            foreach ($city_list as $key => $value) {
                $response->$key = $value;
            }
            array_push($data["response"], $response);
        }

    } else {
        $data['result'] = false;
        $data['message'] = "Data not found";

    }
    echoResponse(200, $data);
});


// get privacy policy
$app->get('/get_privacy', function () use ($app) {
    // get_faq
    $db = new DbOperation();
    $result = $db->get_privacy();
    $data['result'] = true;
    $data['message'] = "";
    $data["response"] = array();
    while ($row = $result->fetch_assoc()) {
        $temp = array();
        foreach ($row as $key => $value) {
            $temp[$key] = $value;
        }
        $temp = array_map('utf8_encode', $temp);
        array_push($data['response'], $temp);
    }
    echoResponse(200, $data);
});



/* service_center
 *  param:
 *  method:post
 */
$app->post('/product_service', function () use ($app) {

    $product_category = $app->request->post('product_category');
    $db = new DbOperation();
    $data = array();

    $area = $db->product_service($product_category);
    if ($area->num_rows > 0) {


        $data['result'] = true;
        $data['message'] = "";
        $data["response"] = array();

        while ($area_list = $area->fetch_assoc()) {

            $response = new stdClass();
            foreach ($area_list as $key => $value) {
                $response->$key = $value;
            }
            array_push($data["response"], $response);
        }

    } else {
        $data['result'] = false;
        $data['message'] = "Data not found";

    }
    echoResponse(200, $data);
});

function send_notification($data, $reg_ids)
{
    //$reg_id[0]="c9beC3MaCzE:APA91bEytaqMetycls1bkCtEV1cLuiXfypk8SrT3mlJWEpfYh8FMzBQw6dl4eKqMUtB3drOOdSfn4J8udzqg8WTEqdxiYcIjg5g6T1ZUjVpSSgXumhDvvqXn-KpemjmBCMrfDHIQntlX";
    //print_r( $reg_ids);
    $url = 'https://fcm.googleapis.com/fcm/send';
    $api_key = 'AAAAdlMKxlc:APA91bGTsTmJMO1EAicBqWTRdZEQOpxJmn_4VRtd7GrVEaJMrZCO-XGKTfzQdk5DGFfmE6ZAbyvRNLbN7Iao13qaSRgv6ia6KdLziszNSj4-oiuc9p-K1IXPJ9Unxdj0FEpVFkpJ0g2n';
    /*$msg = array(
        'title' =>"MyCt Store3",
        'icon' => 'myicon',
        'sound' => 'mySound',
        'data' => urlencode($data)
    );*/

    $fields = array(
        'registration_ids' => $reg_ids,
        'data' => $data,
        'notification' => $data
    );

    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $api_key
    );

    echo json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        // die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);

    //  echo $result;
    return $result;
}

// fcm notification code
function send_notification_ios($data, $reg_ids, $title, $body)
{
    //$reg_ids[0]="esR5GsVCeEBljF0hszij-k:APA91bEq7A2QCl6Rrt8-__t7OlUemcQOIy_KRe0Zm6h50b8ffZcciHDdnT8f9poGAiW6gcqywi438TWt_aOLN0yk7YKgbOakkvrmTlvVUEtr98aiz69BsgoACxfHXztRmFx-0HprNxLy";
    $url = 'https://fcm.googleapis.com/fcm/send';
    $api_key = 'AAAAdlMKxlc:APA91bGTsTmJMO1EAicBqWTRdZEQOpxJmn_4VRtd7GrVEaJMrZCO-XGKTfzQdk5DGFfmE6ZAbyvRNLbN7Iao13qaSRgv6ia6KdLziszNSj4-oiuc9p-K1IXPJ9Unxdj0FEpVFkpJ0g2n';

    $msg = array(
        'title' => $title,
        'body' => $body,
        'icon' => 'myicon',
        'sound' => 'custom_notification.mp3',
        'data' => $data
    );
    $fields = array(
        'registration_ids' => $reg_ids,

        'notification' => $msg
    );
//print_r($fields);
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $api_key
    );

    // echo json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        //   die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);

    //  echo $result;
    return $result;
}

// fcm notificaton for android
function send_notification_android($data, $reg_ids_android)
{
   
    $url = 'https://fcm.googleapis.com/fcm/send';
    $api_key = 'AAAAgUDcGS0:APA91bG4c6rKug1mW0DFxGKbfYsv4623pBPsm38063lhToU75fRKGQ-2N7GjvNWxObvCSzXvKj6d1GcIZPieBRa6zeVI1nnnEMa0BudHkpOHQHZFwbcpOJFxapomkOwKyDbLFRvzX51a';
    $msg = array(
        /*'title' => $title,
        'body' => $body,*/
        'icon' => 'myicon',
        //'sound' => 'custom_notification.mp3',
        'data' => $data
    );

    $fields = array(
        'registration_ids' => $reg_ids_android,
        'data' => $data,

    );
   // print_r($fields);
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key=' . $api_key
    );

    // echo json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        //die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);

    //  echo $result;
    return $result;
}

function echoResponse($status_code, $response)
{
    $app = Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}


function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $app = Slim::getInstance();
        $response["error"] = true;
        $response["error_code"] = 99;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}

function verifyRequiredParams_json($required_fields,$data)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
   

    foreach ($required_fields as $field) {
        
        if(!in_array($field,$data))
        {
            $error = true;
            $error_fields .= $field . ', ';
        }

        
    }

    if ($error) {
        $response = array();
        $app = Slim::getInstance();
        $response["error"] = true;
        $response["error_code"] = 99;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}




$app->run();


?>