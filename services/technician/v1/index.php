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


/*
technician reg
method:post
*/

$app->post('/technician_reg', function () use ($app) {

    $data = array();

    $response = new stdClass();

    $req_data = json_decode($app->request->post('data'));
    $keys = array_keys(json_decode($app->request->post('data'), true));   

    verifyRequiredParams_json(array('name','email','contact','userid','password','service_center'),$keys);

    $name = $req_data->name;
    $email = $req_data->email;
    $contact = $req_data->contact;
    $userid = $req_data->userid;
    $password = $req_data->password;

    $service_center = $req_data->service_center;
    $status='Enable';

   
    $db = new DbOperation();
    if ($_FILES["id_proof"]["name"] != "") {

        $i = 0;
        $FileName = $_FILES["id_proof"]["name"];
        $Arr = explode('.', $FileName);
        $MainFileName = $userid . "_" . $Arr[0] . "." . $Arr[1];


        if (move_uploaded_file($_FILES["id_proof"]["tmp_name"], "../../../images/technician_idproof/" . $MainFileName)) {
            $response->image_upload = "success";
        } else {
            $response->image_upload = "fail";
        }
    }
    else
    {
        $MainFileName="";
    }
    
    $res = $db->techinician($name, $email, $contact, $service_center, $status,$userid,$password,$MainFileName);


    if ($res == 0) {
       
     
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

/*
*   login
* param: userid,password
* method: post
*/
$app->post('/login', function () use ($app) {

   // verifyRequiredParams(array('userid', 'password'));
    $req_data = json_decode($app->request->post('data'));

    $keys = array_keys(json_decode($app->request->post('data'), true));   

    verifyRequiredParams_json(array('userid','password'),$keys);
    $userid = $req_data->userid;
    $password = $req_data->password;
    $tokenid = $req_data->fcm_key;
    $type = $req_data->type;
    //$type='android';

    $db = new DbOperation();
    $data = array();

    $response = new stdClass();

    if ($db->Login($userid, $password)) {
        $user = $db->get_technician($userid);

        if (strtolower($user['status']) == 'enable') {

            //generate api key
            $api_key=$db->generateApiKey();


            $data['result'] = true;
            $data['message'] = "";
            $response->id = $user['id'];
            $response->name = $user['name'];            
            $response->email = $user['email'];
            $response->contact = $user['contact'];
            $response->api_key=$api_key;
            $insert_device = $db->insert_technician_device($user["id"],$tokenid,$type,$api_key);
        } else {
            $data['result'] = false;
            $data['message'] = "You are disabled";
        }

    } else {
        $data['result'] = false;
        $data['message'] = "Invalid userid or password";
    }
    //array_push($data["response"], $response);
    $data["response"] = $response;
    echoResponse(200, $data);
});


/*

 * logout
 * Parameters:id,tokenid
 * Method: post
*/
$app->post('/logout', function () use ($app) {
    
    $response = array();

    $req_data = json_decode($app->request->post('data'));
    $keys = array_keys(json_decode($app->request->post('data'), true));   

    verifyRequiredParams_json(array('id','fcm_token'),$keys);

    $uid = $req_data->id;
    $tokenid = $req_data->fcm_token;
    

    $db = new DbOperation();
    $res = $db->logout($uid,$tokenid);

    if($res==1){

        $response['result'] =true;
        $response['message'] = "Logged out";
        echoResponse(201, $response);
    }
    else{

        $response['result']= false;


        $response['message'] = "Please try again";
        echoResponse(201, $response);
    }

});

//technician list
$app->post('/technician_list', 'authenticateUser', function () use ($app) {

    //verifyRequiredParams(array(''));
    //data={service_center_id:}
    $req_data = json_decode($app->request->post('data'));
    $keys = array_keys(json_decode($app->request->post('data'), true));   

    verifyRequiredParams_json(array('service_center_id'),$keys);

    $service_center_id = $req_data->service_center_id;
    $db = new DbOperation();
    $data = array();

    $techinician = $db->techinician_list($service_center_id);
    if ($techinician->num_rows > 0) {


        $data['result'] = true;
        $data['message'] = "";
        $data["response"] = array();

        while ($techinician_listi = $techinician->fetch_assoc()) {

            $response = new stdClass();
            foreach ($techinician_listi as $key => $value) {
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

$app->post('/homepage', 'authenticateUser', function () use ($app) {

   $data = array();

    $response = new stdClass();

    $req_data = json_decode($app->request->post('data'));

    $keys = array_keys(json_decode($app->request->post('data'), true));   

    verifyRequiredParams_json(array('date','technician_id'),$keys);
    $date = $req_data->date;
    $technician_id = $req_data->technician_id;
    
    

    $db = new DbOperation();
    $homepage_resp = $db->homepage($date,$technician_id);
    if ($homepage_resp->num_rows > 0) {


        $data['result'] = true;
        $data['message'] = "";
        $data["response"] = array();

        while ($call_list = $homepage_resp->fetch_assoc()) {

            $response = new stdClass();
            foreach ($call_list as $key => $value) {
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

$app->post('/call_list', 'authenticateUser', function () use ($app) {

    $data = array();

    $response = new stdClass();

    $req_data = json_decode($app->request->post('data'));

    $keys = array_keys(json_decode($app->request->post('data'), true));   

    verifyRequiredParams_json(array('date','technician_id','type'),$keys);
    $date = $req_data->date;
    $type = $req_data->type;
    $technician_id = ($req_data->technician_id!="")?$req_data->technician_id:"1";
     
    $db = new DbOperation();
    
    $res = $db->call_list($date, $type,$technician_id);

if ($res->num_rows > 0) {

        $data['result'] = true;
        $data['message'] = "";
        $data["response"] = array();

        while ($call_list = $res->fetch_assoc()) {

            $response = new stdClass();
            $contact="";
            foreach ($call_list as $key => $value) {
                $response->$key = strval($value); 
                if($key=="fname")
                {
                    $name=$value;
                } 
                if($key=="lname")
                {
                    $name.=" ".$value;
                }    
                if($key=="contact")
                {
                    $contact=" ".$value;
                } 
                if($key=="address")
                {
                    $response->$key=$name." ".$value." - ".$contact;
                }         

            }
            //$response->service_center_address = "123,Orpel Service Center.";
            // call history
                $response_call = array();
                $res_call_history=$db->call_history($call_list["complaint_no"]);
                while ($call_hisotry = $res_call_history->fetch_assoc()) {

                    $response_call_history = new stdClass();
                    foreach ($call_hisotry as $key => $value) {
                        $response_call_history->$key = $value;
                    }

                    array_push($response_call, $response_call_history);
                }
            $response->call_history=$response_call;
            $response->call_type=$type;
            array_push($data["response"], $response);
        }

    } else {
        $data['result'] = false;
        $data['message'] = "Data not found";

    }

echoResponse(200, $data);
});

$app->post('/call_allocation_add', 'authenticateUser', function () use ($app) {

   $data = array();

    $response = new stdClass();

    $req_data = json_decode($app->request->post('data'));
    $keys = array_keys(json_decode($app->request->post('data'), true));   

    verifyRequiredParams_json(array('service_center_id','complaint_no','product_serial_no','product_model','purchase_date','technician','allocation_date','allocation_time'),$keys);

    $service_center_id = $req_data->service_center_id;
    $complaint_no = $req_data->complaint_no;
    $product_serial_no = $req_data->product_serial_no;
    $product_model = $req_data->product_model;
    $purchase_date = $req_data->purchase_date;
    $technician = $req_data->technician;
    $allocation_date = $req_data->allocation_date;
    $allocation_time = $req_data->allocation_time;
    $allocation_time_in_24  = date("H:i:s", strtotime($allocation_time));  
    $parts_used = isset($req_data->parts_used)?$req_data->parts_used:"";
    $call_type = isset($req_data->call_type)?$req_data->call_type:"";
    $service_charge = isset($req_data->service_charge)?$req_data->service_charge:"";
    $parts_charge = isset($req_data->parts_charge)?$req_data->parts_charge:"";
    $history_status = isset($req_data->history_status)?$req_data->history_status:""; 
    $reason = isset($req_data->reason)?$req_data->reason:"";     
    $status=(isset($req_data->history_status) && $req_data->history_status!="") ?$req_data->history_status:'allocated';
  
    $db = new DbOperation();
    $noti_resp="";

    // check current status
    $call_data=$db->get_call_allocation($complaint_no);


        if($history_status!="")
        {
            // insert into call history tbl
            //$comp=$db->add_complaint($complaint_no,$service_center_id,$technician);
            $comp=$db->add_history($complaint_no,$service_center_id,$technician,$parts_used,$call_type,$service_charge,$parts_charge,$history_status,$reason);
        }
    

    if (isset($_FILES["serial_no_img"]["name"] )) {

        $i = 0;
        $FileName = $_FILES["serial_no_img"]["name"];
        $Arr = explode('.', $FileName);
        $serial_no_img = $complaint_no . "_" . $Arr[0] . "." . $Arr[1];


        if (move_uploaded_file($_FILES["serial_no_img"]["tmp_name"], "../../../images/serial_no_img/" . $serial_no_img)) {
            $response->serial_no_img = "success";
        } else {
            $response->serial_no_img = "fail";
        }
    }
    else
    {
        $serial_no_img="";
    }
    //product model img
    if (isset($_FILES["product_model_img"]["name"] )) {

        $i = 0;
        $FileName = $_FILES["product_model_img"]["name"];
        $Arr = explode('.', $FileName);
        $product_model_img = $complaint_no . "_" . $Arr[0] . "." . $Arr[1];


        if (move_uploaded_file($_FILES["product_model_img"]["tmp_name"], "../../../images/product_model_img/" . $product_model_img)) {
            $response->product_model_img = "success";
        } else {
            $response->product_model_img = "fail";
        }
    }
    else
    {
        $product_model_img="";
    }
    // purchase date img
    if (isset($_FILES["purchase_date_img"]["name"] )) {

        $i = 0;
        $FileName = $_FILES["purchase_date_img"]["name"];
        $Arr = explode('.', $FileName);
        $purchase_date_img = $complaint_no . "_" . $Arr[0] . "." . $Arr[1];


        if (move_uploaded_file($_FILES["purchase_date_img"]["tmp_name"], "../../../images/purchase_date_img/" . $purchase_date_img)) {
            $response->purchase_date_img = "success";
        } else {
            $response->purchase_date_img = "fail";
        }
    }
    else
    {
        $purchase_date_img="";
    }
    
    $res = $db->call_allocation_add($complaint_no,$service_center_id, $product_serial_no, $product_model, $purchase_date, $technician,$allocation_date,$allocation_time_in_24,$status,$serial_no_img,$product_model_img,$purchase_date_img,$reason);


    if ($res == 0) {      


        if($status=="closed" || $status=="cancelled")
         {

         
            // send notifications to user

            $res_token_android = $db->get_user_device_android($complaint_no);        
            $inc = 0;
            $reg_ids = array();
            $reg_ids_android = array();
            $count_array = array();
            $not = new stdClass();
            $title = "#" .$complaint_no;
            $body = "Your complaint has been " .$status;        
            $not->message = "Your complaint has been " .$status;
            $not->title = "#" . $complaint_no;
            $not->complaint_no = $complaint_no;        
            $not->body = "Your complaint has been " .$status;
            $inc2 = 0;
            while ($token_android = mysqli_fetch_array($res_token_android)) {
               
                $reg_ids_android[$inc2++] = $token_android['token'];
            }

           $noti_resp= send_notification_android($not, $reg_ids_android);

           // send sms to user
           if($status=="closed")
             {
                // get customer contact no
                $customer_data=$db->get_customer($complaint_no);
                $api_key = '461583564CE48B';
                $contacts=$customer_data["contact"];
                $from = 'DPRFCT';
                $smsstring='Dear customer, your complaint has been completed. PERFECT DISTRIBUTORS';
                $sms_text = urlencode($smsstring);
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_URL, "http://sms.autobysms.com/app/smsapi/index.php");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "key=".$api_key."&campaign=0&routeid=9&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text."&template_id=1707164321962255287");
                 $res = curl_exec($ch);
                curl_close($ch);
             }
      } 
     
        $data['result'] = true;
        $data['message'] = "Call allocated successfully";
        $data['response'] = $response;
        $data['notification_response'] =$noti_resp;

        echoResponse(201, $data);
    } else if ($res == 1) {

        $data['result'] = false;
        $data['message'] = "Oops! An error occurred while allocating";
        $data['response'] = $response;

        echoResponse(200, $data);
    }


});



//product list
$app->get('/product_category_list', 'authenticateUser', function () use ($app) {

   

   
    $db = new DbOperation();
    $data = array();

    $product_category_list = $db->product_category_list();
    if ($product_category_list->num_rows > 0) {


        $data['result'] = true;
        $data['message'] = "";
        $data["response"] = array();

        while ($product_category = $product_category_list->fetch_assoc()) {

            $response = new stdClass();
            foreach ($product_category as $key => $value) {
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

function authenticateUser(\Slim\Route $route)
{
    $headers = apache_request_headers();
    $data = array();
    $app = \Slim\Slim::getInstance();
   // print_r($headers);
    if (isset($headers['Apikey'])) {
        
        $db = new DbOperation();
        $api_key = $headers['Apikey'];
       
            if (!$db->isValidTechnician($api_key)) {
            $data["success"] = false;
            $data["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $data);
            $app->stop();

       }
        
    } else {
        $data["success"] = false;
        $data["message"] = "Api key is misssing";
        echoResponse(400, $data);
        $app->stop();
    }
}
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
   // print_r($required_fields);

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
