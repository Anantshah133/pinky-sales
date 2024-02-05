<?php

ob_start();
//include("db_connect.php");
require '../admin/db_connect.php';
error_reporting(0);

$obj=new DB_Connect;
date_default_timezone_set("US/Pacific");
$date=date("m-d-Y");
$order_current_date=date("m/d/Y");
$time=date("h:i:sa");
$datetime=date("m/d/Y h:i A");
$last24hr=date('m/d/Y h:i A',strtotime("-1 days"));
session_start(); 
//require_once('../PushNotification.php');
//echo $datenew=date("Y-m-d H:i:s");
//require '../sms/twilio-php-master/Twilio/autoload.php';
//require_once('Mail/PHPMailer_v5.1/class.phpmailer.php'); 

// Use the REST API Client to make requests to the Twilio REST API
//use Twilio\Rest\Client;

 //Your Account SID and Auth Token from twilio.com/console
/* $sid = 'AC477abc326f1215fc91bb056b233cf18a';
$token = '12964ff8a2240e62a321b30f626b2926';*/

//$client = new Client($sid, $token);




//echo $message->status."<br/>";
require_once('../Mail/PHPMailer_v5.1/class.phpmailer.php');
 //include("../MPDF/mpdf.php");

 function smtpmailer($to, $from, $from_name, $subject, $body,$filename1, $is_gmail = true)
    {
		
   
        global $error;
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true; 
        if($is_gmail)
        {
		
           // $mail->SMTPSecure = 'ssl'; 
			$mail->SMTPKeepAlive = true;
			$mail->Mailer = "smtp";
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;   
			$mail->SMTPSecure = 'ssl';   
            $mail->Username = 'pragmatestmail@gmail.com';
            $mail->Password = 'Pragma@jay';   
        }
        else
        {
	
           $mail->Host = 'smtp.gmail.com';
             $mail->Username = 'pragmatestmail@gmail.com';
            $mail->Password = 'Pragma@jay';   
        }

        $mail->IsHTML(true);		
		$mail->SMTPDebug = false; 
        $mail->From="pragmatestmail@gmail.com";
        $mail->FromName=$from_name;
        $mail->Sender=$from; // indicates ReturnPath header
        $mail->AddReplyTo($from, $from_name); // indicates ReplyTo headers
//        $mail->AddCC('cc@site.com.com', 'CC: to site.com');
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);
		if($filename1!="")
		{
			
			$mail->addAttachment($filename1);
		}
    
        $mail->Timeout = 20;

        if(!$mail->Send())
        {			
            $error = 'Mail error: '.$mail->ErrorInfo;
				
         // echo $error;
		  
        }
        else
        {
            $error = 'Message sent!';
		//	echo $error;
           
        }
    }

	
	
	function smtpforgot($to, $from, $from_name, $subject, $body, $is_gmail = true)
    {
		
   
        global $error;
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true; 
        if($is_gmail)
        {
		
           // $mail->SMTPSecure = 'ssl'; 
			$mail->SMTPKeepAlive = true;
			$mail->Mailer = "smtp";
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;   
			$mail->SMTPSecure = 'ssl';   
            $mail->Username = 'pragmatestmail@gmail.com';  
            $mail->Password = 'Pragma@jay';   
        }
        else
        {
	
           $mail->Host = 'smtp.gmail.com';
             $mail->Username = 'pragmatestmail@gmail.com';  
            $mail->Password = 'Pragma@jay';   
        }

        $mail->IsHTML(true);		
		$mail->SMTPDebug = false; 
        $mail->From="pragmatestmail@gmail.com";
        $mail->FromName=$from_name;
        $mail->Sender=$from; // indicates ReturnPath header
        $mail->AddReplyTo($from, $from_name); // indicates ReplyTo headers
//        $mail->AddCC('cc@site.com.com', 'CC: to site.com');
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);
		    
        $mail->Timeout = 60;

        if(!$mail->Send())
        {			
            $error = 'Mail error: '.$mail->ErrorInfo;
				
          return 0;
		  
        }
        else
        {
            $error = 'Message sent!';
			return 1;
           
        }
    }
    if($_REQUEST['action']=="todos")
{

//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";

	getjson("SELECT * FROM vendor_reg");

}
if($_REQUEST['action']=="login")
{

//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";
 $qry="select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."' ";
	getlogin($qry,$_REQUEST['username'],$_REQUEST['password'],$_REQUEST["id"],$_REQUEST["tokenid"],$_REQUEST["type"]);

}

else  if($_REQUEST['action']=="registration")
{

//echo "insert into vendor_reg (name,business_name,username,email,password,contact1,address) values ('".$_REQUEST['name']."','".$_REQUEST['business_name']."','".$_REQUEST['username']."','".$_REQUEST['email']."','".$_REQUEST['password']."','".$_REQUEST['contactno']."','".$_REQUEST['address']."')";
	getregjson("insert into vendor_reg (name,business_name,username,email,password,contact1,address,area,stats) values ('".$_REQUEST['name']."','".$_REQUEST['business_name']."','".$_REQUEST['username']."','".$_REQUEST['email']."','".$_REQUEST['password']."','+".$_REQUEST['contactno']."','".$_REQUEST['address']."','".$_REQUEST['area']."','Disable')",$_REQUEST['contactno']);

  //echo "insert into vendor_reg (name,business_name,username,email,password,contact1,address,area,stats) values ('".$_REQUEST['name']."','".$_REQUEST['business_name']."','".$_REQUEST['username']."','".$_REQUEST['email']."','".$_REQUEST['password']."','+".$_REQUEST['contactno']."','".$_REQUEST['address']."','".$_REQUEST['area']."','Disable')";

}

else  if($_REQUEST['action']=="profile")
{

//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";
	getjson("select *,c.area_name as area from vendor_reg v,area c where v.area=c.id and v.id = '".$_REQUEST['id']."'");
	

}
else  if($_REQUEST['action']=="update_profile")
{

//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";
	getinsertjson("update vendor_reg set name ='".$_REQUEST['name']."',password ='".$_REQUEST['password']."',email ='".$_REQUEST['email']."',area ='".$_REQUEST['area']."',contact1='".$_REQUEST['contact1']."',address='".$_REQUEST['address']."' where id ='".$_REQUEST['id']."'");

}
else if($_REQUEST['action']=="update_password")
{
	update_password($_REQUEST["old_pass"],$_REQUEST["new_pass"],$_REQUEST["uid"]);
}
else  if($_REQUEST['action']=="logout")
{

//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";
	getinsertjson("delete from vendor_device where vid='".$_REQUEST['id']."' and token='".$_REQUEST['tokenid']."'");
	
}
else  if($_REQUEST['action']=="area")
{

//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";
	getjson("select * from area order by area_name");

}
else  if($_REQUEST['action']=="homepage")
{
	$today=date("m/d/Y");
	if($_REQUEST["date"]!="")
	{
		$order_current_date=$_REQUEST["date"];
	}
   

getjson("SELECT count(CASE WHEN o1.stats ='Confirmed' and str_to_date(o1.collect_date,'%m/%d/%Y')= str_to_date('".$order_current_date."','%m/%d/%Y')THEN 1 END) As confirmed, count(CASE WHEN o1.stats ='Delivered' and str_to_date(o1.collect_date,'%m/%d/%Y')= str_to_date('".$order_current_date."','%m/%d/%Y') THEN 1 END) As delivered,count(CASE WHEN o1.stats ='Dispatched' and str_to_date(o1.collect_date,'%m/%d/%Y')= str_to_date('".$order_current_date."','%m/%d/%Y')  THEN 1 END) As dispatched ,count(CASE WHEN o1.stats ='Pending' and str_to_date(o1.collect_date,'%m/%d/%Y')= str_to_date('".$order_current_date."','%m/%d/%Y') THEN 1 END) As pending ,count(CASE WHEN (o1.stats ='Cancelled' or o1.stats='Rejected')  and str_to_date(o1.collect_date,'%m/%d/%Y')= str_to_date('".$order_current_date."','%m/%d/%Y')THEN 1 END) As cancelled,count(CASE WHEN (o1.stats!='Dispatched' or o1.stats!='Delivered') and str_to_date(o1.collect_date,'%m/%d/%Y')>str_to_date('".$today."','%m/%d/%Y')THEN 1 END) as upcoming ,v1.isopen as shop_status FROM ordr o1,customer_reg c1 ,vendor_reg v1 WHERE o1.customer_id=c1.id and o1.v_id=v1.id and  o1.v_id= '".$_REQUEST['id']."'");


}

else  if($_REQUEST['action']=="historypage")
{
   
//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";
	getjson("SELECT count(CASE WHEN stats ='Confirmed' THEN 1 END) As confirmed, count(CASE WHEN stats ='Delivered' THEN 1 END) As delivered,count(CASE WHEN stats ='enable' THEN 1 END) As enable ,count(CASE WHEN stats ='Pending' THEN 1 END) As pending ,count(CASE WHEN stats ='Cancelled' THEN 1 END) As cancelled FROM `ordr` WHERE v_id= '".$_REQUEST['id']."'  ");

}
//get category
else  if($_REQUEST['action']=="getcategory")
{
   
//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";
	getjson("SELECT * from food_category WHERE v_id= '".$_REQUEST['id']."'  ");

}
else  if($_REQUEST['action']=="get_category_products_list")
{
   
//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";
	getjson("SELECT * from menu WHERE v_id= '".$_REQUEST['id']."'  and fc_id='".$_REQUEST["cat_id"]."'");

}
//get product
else  if($_REQUEST['action']=="get_product")
{
   
//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";
	getjson("SELECT * from menu WHERE id= '".$_REQUEST['id']."' and v_id='".$_REQUEST["v_id"]."'");

}
//update category
else if($_REQUEST['action']=="update_category")
{
    $response=array();
    $response["data"]= array();
    $cat_id=$_REQUEST["cat_id"];
    $stat=$_REQUEST["status"];
    $vendor_id=$_REQUEST["vendor_id"];
    $data = json_decode(base64_decode($_REQUEST["data"]));
    $updatesqls="update food_category set stats='".$stat."',added_by='".$vendor_id."',operation='Updated',user_type='service_center' where id='".$cat_id."' and v_id='".$vendor_id."'";
    $res_sqls=$obj->update($updatesqls);
    $Rowuser_s=mysqli_fetch_array($res_sqls);
    $RowCount_s=mysqli_affected_rows($obj->con1);

    //print_r($data->data[0]);

   foreach ($data->Data as $i=>$value) {
        
     $updatesqls="update menu set stats='".$value->status."',operation='Updated',user_type='service_center' where id='".$value->id."'";
    $res_sqls=$obj->update($updatesqls);
    $productCount_s=mysqli_affected_rows($obj->con1);
}
    if($RowCount_s>0 || $productCount_s>0)
    {
        $status['value']="valid";
        $status['msg']="Updated";
    }
    else{
        $status['value']="invalid"; 
        $status['msg']="Fail";
        
    }
    
    



     array_push($response["data"],$status);
      //ob_clean();
   echo str_replace('\/','/',json_encode($response));

}
//update product
else  if($_REQUEST['action']=="update_product")
{
   $menu=$_REQUEST["id"];
   $vendor=$_REQUEST["v_id"];
  // $sizeItem=$_REQUEST["sizeItem"];
  // $sizePrice=$_REQUEST["sizePrice"];
 //  $mandatorySize=$_REQUEST["mandatorySize"];
    /*$updatesqls="update menu set stats='".$_REQUEST["status"]."',main_price='".$_REQUEST["main_price"]."',operation='Updated',user_type='service_center' where id='".$menu."'";*/
    $updatesqls="update menu set stats='".$_REQUEST["status"]."',operation='Updated',user_type='service_center' where id='".$menu."'";
	$res_sqls=$obj->update($updatesqls);
	$Rowuser_s=mysqli_fetch_array($res_sqls);
	$RowCount_s=mysqli_affected_rows($obj->con1);

	$response=array();
	$response["data"]= array();

	if($RowCount_s>0)
	{
		$status['value']="valid";
	 	/*$update_size_pro_qry = "CALL size_pro_disable('" . $menu . "','" . $service_center . "')";
                $res_size_pro = $obj->update($update_size_pro_qry);
                $update_extra_addone_qry = "CALL extra_addone_disable('" . $service_center . "','" . $menu . "')";
                $res_extra_Addone = $obj->update($update_extra_addone_qry);
				$insertSizeProc = "CALL new_sizepro('" . $service_center . "','" . $menu . "','" . base64_encode($sizeItem) . "','" . $sizePrice . "','Enabled','" . $datetime . "','" . $service_center . "','Saved','" . $mandatorySize . "')";
				$resultInsertSizeProc = $obj->insert($insertSizeProc);*/
			
	}
	else{
		$status['value']="invalid";	
		
		
	}
	
	 array_push($response["data"],$status);
	  ob_clean();
   echo str_replace('\/','/',json_encode($response));

}
//update store status
else if($_REQUEST['action']=="update_store_status")
{
    $response=array();
    $response["data"]= array();
    $v_id=$_REQUEST["v_id"];
    $stat=$_REQUEST["status"];
   
    $updatesqls="update vendor_reg set isopen='".$stat."' where  id='".$v_id."'";
    $res_sqls=$obj->update($updatesqls);
    $Rowuser_s=mysqli_fetch_array($res_sqls);
    $RowCount_s=mysqli_affected_rows($obj->con1);
    if($RowCount_s>0)
    {
        $status['value']="valid";
        $status['msg']="Updated";
    }
    else{
        $status['value']="invalid"; 
        $status['msg']="Fail";
        
    }
    
     array_push($response["data"],$status);
      //ob_clean();
   echo str_replace('\/','/',json_encode($response));

}
else  if($_REQUEST['action']=="vieworder")
{

//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";


    $dynamic="";
    
    $dynamic="and year(str_to_date('".$order_current_date."','%m/%d/%Y')) = year(STR_TO_DATE(collect_date,'%m/%d/%Y')) and month(str_to_date('".$order_current_date."','%m/%d/%Y')) = month(STR_TO_DATE(collect_date,'%m/%d/%Y'))";
    
    
    
    if($_REQUEST['start_date']!="" || $_REQUEST['end_date']!="")
    {
    	
    	$dynamic="and STR_TO_DATE(o1.collect_date , '%m/%d/%Y' )  BETWEEN STR_TO_DATE( '".$_REQUEST['start_date']."', '%m/%d/%Y' ) AND STR_TO_DATE( '".$_REQUEST['end_date']."', '%m/%d/%Y' )";
    }
    if($_REQUEST['check']!="")
    {
    	$check = $_REQUEST['check'];
    	$check = rtrim($check, ',');
    	$dynamic.=" and FIND_IN_SET(o1.stats,'".$check."' ) > 0 ";
    		
    }



//	getjsonorder("select o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.date_time,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,od1.detail,od1.quantity,m1.name,c1.firstname as cname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as menu_detail,m1.main_price as menu_price,ea1.topping_name as ea_toppingname,ea1.size_name as ea_size,o1.date_time as datetime1 from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1,extra_addone ea1 where  v1.id='".$_REQUEST['id']."' and c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and m1.id=od1.m_id ".$dynamic." and v1.id=fc1.v_id group by o1.id ORDER BY o1.id DESC");



//echo "select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,od1.detail,od1.quantity,m1.name,c1.firstname as cname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as menu_detail,m1.main_price as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as datetime1,o1.delivery_instruction from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1 where c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and m1.id=od1.m_id and m1.fc_id=fc1.id and fc1.v_id=v1.id and  v1.id='".$_REQUEST['id']."' ".$dynamic." order by o1.id DESC";


	getjsonorder("select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,od1.detail,od1.quantity,m1.name,c1.firstname as cname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as menu_detail,m1.main_price as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as datetime1,o1.delivery_instruction from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1 where c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and m1.id=od1.m_id and m1.fc_id=fc1.id and fc1.v_id=v1.id and  v1.id='".$_REQUEST['id']."' ".$dynamic." order by o1.id DESC");


}
else  if($_REQUEST['action']=="current_vieworder")
{

//echo "select * from vendor_reg where username = '".$_REQUEST['username']."' and 	password =  '".$_REQUEST['password']."'";



//	$dynamic.=" and STR_TO_DATE(SUBSTRING(o1.collect_date,1,10), '%m/%d/%Y' ) = STR_TO_DATE( '".$order_current_date."', '%m/%d/%Y' )";

if($_REQUEST['date']!="" && strtolower($_REQUEST["check"])!="upcoming")
        {
            $order_current_date=$_REQUEST["date"];
            $dynamic="and  str_to_date(collect_date,'%m/%d/%Y') = str_to_date('".$order_current_date."','%m/%d/%Y')";
            
        }
        else
        {
            $dynamic="and  str_to_date(collect_date,'%m/%d/%Y') > str_to_date('".$order_current_date."','%m/%d/%Y')";
        }



if($_REQUEST['check']!="")
{
    if(strtolower($_REQUEST['check'])=="rejected")
    {
        $check ="Cancelled,Rejected";
    }
	

    else if(strtolower($_REQUEST["check"])=="upcoming")
    {
        $check = "Pending,Confirmed,Rejected,Cancelled";
    }
    else 
    {
        $check=$_REQUEST["check"];
    }
	$check = rtrim($check, ',');
	$dynamic.=" and FIND_IN_SET(o1.stats,'".$check."' ) > 0 ";
		
}


if($_REQUEST['check']=="Confirmed")
{
//order by collect date asc
$dynamic.="order by str_to_date(o1.collect_date,'%m/%d/%Y'),str_to_date(o1.delivery_collection_time,'%h:%i %p')";
}
else
{
//order by id
	$dynamic.="order by o1.id DESC";
}


//	getjsonorder("select o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.date_time,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,od1.detail,od1.quantity,m1.name,c1.firstname as cname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as menu_detail,m1.main_price as menu_price,ea1.topping_name as ea_toppingname,ea1.size_name as ea_size,o1.date_time as datetime1 from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1,extra_addone ea1 WHERE v1.id= '".$_REQUEST['id']."' and c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and m1.id=od1.m_id ".$dynamic." and v1.id=fc1.v_id group by o1.id ORDER BY o1.id DESC");

//	getjsonorder("select o1.id as orderid,o1.date_time,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,c1.firstname as cname,c1.email as customer_email,o1.total_price as tprice,v1.name as vname,v1.image as vimage,o1.delivery_address,od1.id ,od1.detail from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1 where c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and  v1.id='".$_REQUEST['id']."' ".$dynamic." ");
	getjsonorder("select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date as date_time,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,od1.detail,od1.quantity,m1.name,c1.firstname as cname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as menu_detail,m1.main_price as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as datetime1,o1.delivery_instruction,o1.date_time as order_time, v1.address as takeaway_address  from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1 where c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and m1.id=od1.m_id and m1.fc_id=fc1.id and fc1.v_id=v1.id and  v1.id='".$_REQUEST['id']."' ".$dynamic);	



}


else if($_REQUEST["action"]=="single_order")
{
	getjsonorder("select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,od1.detail,od1.quantity,m1.name,c1.firstname as cname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as menu_detail,m1.main_price as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as datetime1,o1.delivery_instruction,o1.date_time as order_time from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1 where c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and m1.id=od1.m_id and m1.fc_id=fc1.id and fc1.v_id=v1.id and v1.id='".$_REQUEST['id']."' and o1.id='".$_REQUEST['orderid']."' order by o1.id DESC");
}
else if($_REQUEST['action']=="order_detail")
{
	getjson("select o1.id,o1.collect_date as date_time,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date as date_time,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,c1.firstname as cname,c1.email as customer_email,o1.total_price as tprice,v1.name as vname,v1.image as vimage,o1.delivery_address from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1 where 
c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and o1.id='".$_REQUEST['id']."'  group by o1.id");
}

else if($_REQUEST['action']=="get_history_total_new")
{
	if($_REQUEST['start_date']!="" || $_REQUEST['end_date']!="")
	{
		
		$dynamic="and STR_TO_DATE(ordr.collect_date , '%m/%d/%Y' )  BETWEEN STR_TO_DATE( '".$_REQUEST['start_date']."', '%m/%d/%Y' ) AND STR_TO_DATE( '".$_REQUEST['end_date']."', '%m/%d/%Y' )";
	}
	else
	{
		//current month 1st date('01/m/Y');
		
		$dynamic="and STR_TO_DATE(ordr.collect_date , '%m/%d/%Y' )  BETWEEN STR_TO_DATE( '".date('01/m/Y')."', '%m/%d/%Y' ) AND STR_TO_DATE( '".$order_current_date."', '%m/%d/%Y' )";
	}
	
getjson("SELECT (SELECT IFNULL(sum( cast(ordr.total_price as decimal(11,2))),0) FROM `ordr` WHERE stats='Pending'  and v_id ='".$_REQUEST['v_id']."' ".$dynamic.") as total_pending, (SELECT IFNULL(sum( cast(ordr.total_price as decimal(11,2))),0) FROM `ordr` WHERE stats='Cancelled' and v_id ='".$_REQUEST['v_id']."' ".$dynamic.") as total_cancelled,(SELECT IFNULL(sum( cast(ordr.total_price as decimal(11,2))),0) FROM `ordr` WHERE stats='Confirmed' and v_id ='".$_REQUEST['v_id']."' ".$dynamic.") as total_confirmed ,(SELECT IFNULL(sum( cast(ordr.total_price as decimal(11,2))),0) FROM `ordr` WHERE stats='Delivered'and v_id ='".$_REQUEST['v_id']."' ".$dynamic.") as total_delivered,(SELECT count(*) as total_orders FROM `ordr` WHERE stats='Pending' and v_id ='".$_REQUEST['v_id']."' ".$dynamic.") as orders_penidng,(SELECT count(*) as total_orders FROM `ordr` WHERE stats='Cancelled' and v_id ='".$_REQUEST['v_id']."' ".$dynamic.") as orders_cancelled,(SELECT count(*) as total_orders FROM `ordr` WHERE stats='Confirmed' and v_id ='".$_REQUEST['v_id']."' ".$dynamic.") as orders_confrimed,(SELECT count(*) as total_orders FROM `ordr` WHERE stats='Delivered' and v_id ='".$_REQUEST['v_id']."' ".$dynamic.") as orders_delivered FROM `ordr`where v_id ='".$_REQUEST['v_id']."'".$dynamic ."limit 1");
}
else if($_REQUEST['action']=="forgetpassword")
{

	//forgotpass("select  * from vendor_reg where username='".$_REQUEST['username']."'", $client);
//select f.type as usertype,TIMESTAMPDIFF(MINUTE,f.date_time,NOW())as min_diff from vendor_reg v,forgotcount f where f.userid=v.id and v.username='".$_REQUEST['username']."' ;	
//SELECT TIMESTAMPDIFF(MINUTE,date_time,NOW())as min_diff from forgotcount
	forgotpass("select  * from vendor_reg where username='".$_REQUEST['username']."'", $client,$_REQUEST['username']);
}
//


else if($_REQUEST['action']=="check_otp")
{	
	$response=array();
	$response["data"]= array();

    $cont = "+".$_REQUEST['mono'];
    
	$LoginQuery="CALL otp_login('". $cont."','".$_REQUEST["otp"]."')";
							$Result=$obj->selectProc($LoginQuery);
	//echo "hello";
	$Row=mysqli_fetch_array($Result);
	
	$RowCount=mysqli_num_rows($Result);
	

	if($RowCount>0)
	{
	        $status['value']="valid";	
			$Userid = $Row["id"];
			$status['value']="valid";	
			$status['id']=$Userid;	
			$status['name']=$Row["name"];
			$status['email']=$Row["email"];

	}
	else
	{	

	    $status['value']="invalid";	
			
	}
		   array_push($response["data"],$status);
		   ob_clean();
	       echo str_replace('\/','/',json_encode($response));
}
else if($_REQUEST['action']=="check_otp_vendor")
{	
	$response=array();
	$response["data"]= array();
	$tokenid=$_REQUEST["tokenid"];
	$type=$_REQUEST["type"];
    $cont = "+".$_REQUEST['mono'];
    
	$LoginQuery="CALL otp_login_vendor('". $cont."','".$_REQUEST["otp"]."')";
							$Result=$obj->selectProc($LoginQuery);
	//echo "hello";
	$Row=mysqli_fetch_array($Result);
	
	$RowCount=mysqli_num_rows($Result);
	

	if($RowCount>0)
	{
	        
			
		if($Row["stats"] == 'Enable')
		  {
		  	$status['value']="valid";	
			$Userid = $Row["id"];
			$status['value']="valid";	
			$status['id']=$Userid;	
			$status['name']=$Row["name"];
			$status['email']=$Row["email"];
			
			 $del_qry="delete from vendor_device where vid='".$Row["id"]."' and token='".$tokenid."'";
			$obj->delete($del_qry);
			
			 $qry_insert="insert into vendor_device values ('','".$Row["id"]."','".$tokenid."','','".$type."','0')";
		
			$res=$obj->insert($qry_insert);
		  }else if($Row["stats"] == 'Disable')
		  {
			  $status['value']="disable";	
			  
		  }
		  else{ 
		  $status['value']="invalid";	
			$status['id']="0";	
			  
		  }
	
	}
	else
	{	

	    $status['value']="invalid";	
			
	}
		   array_push($response["data"],$status);
		    ob_clean();
	       echo str_replace('\/','/',json_encode($response));
}
else  if($_REQUEST['action']=="change_order_status")
{

    $response=array();
    $response["data"]= array();
	if($_REQUEST["datetime"]!="")
	{
		$datetm=explode(":",$_REQUEST["datetime"]);
		$hr=(strlen($datetm[0])<2)?"0".$datetm[0]:$datetm[0];
		$min=(strlen($datetm[1])<2)?"0".$datetm[1]:$datetm[1];
		$newdatetime=$hr.":".$min;
	}

    $select_order="CALL `fetch_order_status`('".$_REQUEST['id']."');";
    $select_order_r=$obj->selectProc($select_order);
    $fetch_order= mysqli_fetch_array($select_order_r);
   
   if($fetch_order["stats"]!="Cancelled")
   {

   
            //$updatesqls="CALL change_order_status('".$_REQUEST['id']."','".$_REQUEST['order_status']."')";
			if($_REQUEST['order_status']=="Delivered")
			{
				$updatesqls="update ordr set stats='".$_REQUEST['order_status']."',reason='".$_REQUEST['reason']."',payment_status='PAID' where id='".$_REQUEST['id']."'";
			}
			else if($_REQUEST['order_status']=="Cancelled")
			{
				$updatesqls="update ordr set stats='".$_REQUEST['order_status']."',reason='".$_REQUEST['reason']."',payment_status='UNPAID' where id='".$_REQUEST['id']."'";
			}
			else if($_REQUEST['order_status']=="Confirmed")
			{
				 $updatesqls="update ordr set stats='".$_REQUEST['order_status']."',reason='".$_REQUEST['reason']."',delivery_collection_time='".$newdatetime."' where id='".$_REQUEST['id']."'";
			}
			else
			{
				$updatesqls="update ordr set stats='".$_REQUEST['order_status']."',reason='".$_REQUEST['reason']."' where id='".$_REQUEST['id']."'";
			}
          // echo $updatesqls;
	        $res_sqls=$obj->update($updatesqls);
			$Rowuser_s=mysqli_fetch_array($res_sqls);
			$RowCount_s=mysqli_num_rows($res_sqls);
			$r_afcted=mysqli_affected_rows($obj->con1);
			
		
		
			if($r_afcted>0)
			{
                
				$status['value']="valid";
				
							
				$selectSql="select o1.id,m1.name,od1.detail,od1.quantity,od1.price from ordr o1,order_detail od1,menu m1 where 
                 o1.id=od1.o_id and m1.id=od1.m_id and o1.id='".$_REQUEST['id']."'";

                	$res_grid = $obj->select($selectSql);
                 	$selectSql1 = "select o1.id,o1.collect_date as date_time,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,c1.id as customer_id,c1.firstname as cname,c1.email as customer_email,o1.total_price as tprice,v1.name as vname,v1.image as vimage,o1.delivery_address,v1.business_name,v1.id as vendor_id,o1.delivery_instruction,o1.ip_address,v1.email as vendor_email,c1.contact1 as c_contact,v1.address as v_address,v1.contact1 as v_contact from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1 where 
                c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and o1.id='".$_REQUEST['id']."'  group by o1.id;";
                	$res_grid1 = $obj->select($selectSql1);
                	$row1 = mysqli_fetch_array($res_grid1);
                	$cemail=$row1["customer_email"];
                	$vendormail=$row1["vendor_email"];
                	/*$selectSqlvendor="select * from vendor_reg where id='".$_REQUEST["vid"]."' ";
                	$ad_data=$obj->selectProc($selectSqlvendor);
                	$row_vendor = mysqli_fetch_array($ad_data);*/
                	$my_status = $_REQUEST['order_status'];
    	
                	if($_REQUEST['order_status']=="Confirmed" || $_REQUEST['order_status']=="Cancelled")
                	{
                		
                		 $insert_mail_qry="insert into domail values('','".$row1["customer_id"]."','".$row1["vendor_id"]."','".$row1["id"]."','".$my_status."','Enabled','".$datetime."')";
                		$res=$obj->insert($insert_mail_qry);
                		
                	
                	
                	}
				
		
        			$contact = str_replace(' ','',$row1['c_contact']);
                    $totalamt = str_replace(' ','',$row1['tprice']);
                    $order_id = str_replace(' ','',$row1['order_id']);
                    $my_status = str_replace(' ','',$_REQUEST['order_status']);
                    $tamt=number_format($totalamt, 2, '.', '');
            
                    if($my_status=="Confirmed" || $my_status=="Cancelled")
                    {
                        

                            if($my_status=="Confirmed")
                            {
                                $select_orderconfirmed_sms="CALL `fetch_sms_per_type`('Order Confirm');";
                                $select_sms_per_orderconfirmed_type=$obj->selectProc($select_orderconfirmed_sms);
                                $fetch_orderconfirmed_sms= mysqli_fetch_array($select_sms_per_orderconfirmed_type);
                                $vendorinfo_qry="select v.business_name,v.contact1,v.contact2 from vendor_reg v,ordr o where o.v_id=v.id and o.id='".$_REQUEST['id']."'";
                                $res_vendor=$obj->select($vendorinfo_qry);
                                $vendor_info=mysqli_fetch_array($res_vendor);
                                 $delivery_infoqry="select id,order_id,delivery_collection_time from ordr where id='".$_REQUEST['id']."'";
                                $res_delivery=$obj->select($delivery_infoqry);
                                $delivery_info=mysqli_fetch_array($res_delivery);
                                $msg=str_replace('[contact]',$vendor_info['contact1'],str_replace('[business_name]',$vendor_info["business_name"],str_replace('[order_id]',$order_id, $fetch_orderconfirmed_sms["msg"])));

                                //$msg=str_replace('[order_id]',$order_id, $fetch_orderconfirmed_sms["msg"]);
                                //$message=str_replace('[total]',"$".$tamt, $msg);
                                $message=str_replace("[delivery_time]",$delivery_info['delivery_collection_time'],str_replace('[total]',$tamt." EUR", $msg));
                                $message=str_replace('[line]',"\n", $message);
                                //$message = utf8_encode ('$').$message;
                                //$message = substr($message,5);
                                $notification_msg="your order has been Confirmed";
                            }
                            else if($my_status=="Cancelled"){
                            	   $select_orderconfirmed_sms="CALL `fetch_sms_per_type`('Order Cancle');";
                                    $select_sms_per_orderconfirmed_type=$obj->selectProc($select_orderconfirmed_sms);
                                    $fetch_orderconfirmed_sms= mysqli_fetch_array($select_sms_per_orderconfirmed_type);
                                    //echo "CALL `fetch_sms_per_type`('Order Confirm');";
                                    $msg=str_replace('[order_id]',$order_id, $fetch_orderconfirmed_sms["msg"]);
                                    //$message=str_replace('[total]',"$".$totalamt, $msg);
                                    $message=str_replace('[total]',$tamt." EUR", $msg);
                                    $message=str_replace('[line]',"\n", $message);
                                    //$message = utf8_encode ('$').$message;
                                    //$message = substr($message,5).$my_status;
                                    $notification_msg="your order has been cancelled";
                            }
                             try {
                            /*$res = $client->messages->create(
                                // the number you'd like to send the message to
                                $contact,
                                array(
                                    // A Twilio phone number you purchased at twilio.com/console
                                    'from' =>$from_number,
                                    // the body of the text message you'd like to send
                                    'body' => $message
                                )
                            );*/
                            /*
                            $message = $client
                                ->messages($res->sid)
                                ->fetch();
                            echo $contact."<br/>";
                            echo $message->status."<br/>";
                            echo $res->sid;*/

                            }
                            catch (Exception $e) {
                              //      echo ( $e->getCode() . ' : ' . $e->getMessage() );
                                }

//echo "<br/>".$res->status;


//notification...


                        $tokenqry = "select * from customer_devices where cid='" . $row1["customer_id"] . "' and type='android'";
                        $res_token = $obj->select($tokenqry);
                        if(mysqli_num_rows($res_token)>0)
                        {
                               $inc = 0;
                                $ids_android = array();
                                $ids_ios= array();
                                $count_array=array();
                                while ($token = mysqli_fetch_array($res_token)) {
                            
                                    if($token["type"]=="android")
                                    {
                                        $ids_android[$inc++] = $token["token"];
                                    }
                            
                                }
                              
                                            define('API_ACCESS_KEY', 'AAAAj-em-4E:APA91bGOTAv7LCsYm1l-D7mdyDcO5K0SnmaD3CVnMhZVZ1FSHV4Ccmp87e-qllWumeTiQuRs3aD43pfzhUrtWG1MtqI9a6DRXRk_K7OOszU8lM2y7N6UqnCsmaPLnXMWJHVMhjYkbOQ9');


                                            $msg = array
                                            (
                                                'data' => '{"customer_id":"' .$row1["customer_id"]. '","message":"'.$notification_msg.'"}',
                                                'title' => "$notification_msg",
                                                'icon' => 'myicon',
                                                'sound' => 'mySound'
                                            );
                                            $fields = array
                                            (
                                                'registration_ids' => $ids_android,
                                                'data' => $msg,
                                                "priority" => "high",

                                            );


                                            $headers = array
                                            (
                                                'Authorization: key=' . API_ACCESS_KEY,
                                                'Content-Type: application/json'
                                            );
                                                     $ch = curl_init();
                                                    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                                                    curl_setopt($ch, CURLOPT_POST, true);
                                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                                                    $result = curl_exec($ch);
                                                    curl_close($ch);
                        }
 
    
                        $tokenqry_ios = "select * from customer_devices where cid='" . $row1["customer_id"] . "' and type='ios' ";
                        $res_token_ios = $obj->select($tokenqry_ios);
                        if(mysqli_num_rows($res_token_ios)>0)
                        {
                            $inc=0;
                        $msg_title="";
                        while ($token_ios = mysqli_fetch_array($res_token_ios)) {


                            $ids_ios[$inc] = $token_ios["token"];
                            $inc++;
                        }
                          PushNotifications::iOS_customer($row1["customer_id"] ,$ids_ios,$notification_msg);
                        }
    
  
    


//notification for ios


                }
              
			}
			else{
				
          
           $status['value']="invalid";  
			}
        }
		else{
		 $status['value']="invalid";  
		}
			$select_order="CALL `fetch_order_status`('".$_REQUEST['id']."');";
			$select_order_r=$obj->selectProc($select_order);
			$fetch_order= mysqli_fetch_array($select_order_r);	
			$status['status']=$fetch_order['stats'];
			 array_push($response["data"],$status);
			//  ob_clean();
	       echo str_replace('\/','/',json_encode($response));
}
else  if($_REQUEST['action']=="change_payment_status")
{
	$updatesqls="CALL change_payment_status('".$_REQUEST['o_id']."','".$_REQUEST['payment_status']."')";
	        $res_sqls=$obj->update($updatesqls);
			$Rowuser_s=mysqli_fetch_array($res_sqls);
			$RowCount_s=mysqli_affected_rows($obj->con1);
		
			$response=array();
			$response["data"]= array();
		
			if($RowCount_s>0)
			{
				$status['value']="valid";
				
				$select_order="CALL `fetch_order_status`('".$_REQUEST['o_id']."');";
				$select_order_r=$obj->selectProc($select_order);
                $fetch_order= mysqli_fetch_array($select_order_r);
				
				$status['status']=$fetch_order['payment_status'];
				
			}
			else{
				$status['value']="invalid";	
				$select_order="CALL `fetch_order_status`('".$_REQUEST['o_id']."');";
				$select_order_r=$obj->selectProc($select_order);
                $fetch_order= mysqli_fetch_array($select_order_r);
				
				$status['status']=$fetch_order['payment_status'];
				
			}
			
			 array_push($response["data"],$status);
			  ob_clean();
	       echo str_replace('\/','/',json_encode($response));
}
else if ($_REQUEST["action"] == 'send_otp') {
    
   	$mono = "+".str_replace(' ','',$_REQUEST['mono']);
	$otp = rand(1000,9999);

$select_otp_sms="CALL `fetch_sms_per_type`('OTP');";

$select_sms_per_otp_type=$obj->selectProc($select_otp_sms);
$fetch_otp_sms= mysqli_fetch_array($select_sms_per_otp_type);

$message=str_replace('[OTP]',$otp, $fetch_otp_sms["msg"]);
$message=str_replace('[line]',"\n", $message);

//$message ="Yummy Order Quick Food.\n Your One Time Password is :".$otp."\n Do Not disclose with anyone.";
$selectuser="CALL check_mobile('".$mono."')";
							$res_user=$obj->selectProc($selectuser);
							$Rowuser=mysqli_fetch_array($res_user);
							$RowCount=mysqli_num_rows($res_user);
							
						//	echo $Rowuser['id'];
								 $response=array();
	                             $response["data"]= array();

							//echo $selectuser;
							if($RowCount>0)
							{

								/*$url = "http://login.onlinebusinessbazaar.in/vendorsms/pushsms.aspx?user=pragmainfotech&password=sms123&msisdn=".$mono."&sid=SMSTST&msg=".urlencode($message)."&fl=0&gwid=2";
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_HEADER, 0);
								$result = curl_exec($ch);
								curl_close($ch);*/
								


								$selectuserotp="CALL select_otp('".$Rowuser['id']."')";
							$res_otp=$obj->selectProc($selectuserotp);
								$OtpCount=mysqli_num_rows($res_otp);
								
								if($OtpCount)
								{
									$updatesql="CALL update_otp('".$Rowuser['id']."','".$otp."','verified')";
	                                $res_sql1=$obj->update($updatesql);
								}else{
									$insertsql="CALL new_otp('".$Rowuser['id']."','".$otp."','verified')";
		                            $res_sql=$obj->insert($insertsql);
								}
								
							
	  
	
	                       		$status['value']="valid";	
			

	
	
						    
							
							}
							else
							{
							    	 $status['value']="invalid";
								
							}
								   array_push($response["data"],$status);
								    ob_clean();
	                            echo str_replace('\/','/',json_encode($response));

}
else if ($_REQUEST["action"] == 'send_otp_vendor') {
    
   	$mono = "+".str_replace(' ','',$_REQUEST['mono']);
	$otp = rand(1000,9999);

$select_otp_sms="CALL `fetch_sms_per_type`('OTP');";

$select_sms_per_otp_type=$obj->selectProc($select_otp_sms);
$fetch_otp_sms= mysqli_fetch_array($select_sms_per_otp_type);

$message=str_replace('[OTP]',$otp, $fetch_otp_sms["msg"]);
$message=str_replace('[line]',"\n",$message);
//$message ="Yummy Order Quick Food.\n Your One Time Password is :".$otp."\n Do Not disclose with anyone.";
$selectvendor="CALL check_mobile_vendor('".$mono."')";
							$res_vendor=$obj->selectProc($selectvendor);
							$Rowvendor=mysqli_fetch_array($res_vendor);
							$RowCount=mysqli_num_rows($res_vendor);
							
						//	echo $Rowuser['id'];
								 $response=array();
	                             $response["data"]= array();

							//echo $selectuser;
							if($RowCount>0 && $Rowvendor["stats"] == 'Enable')
							{
							/*	$url = "http://login.onlinebusinessbazaar.in/vendorsms/pushsms.aspx?user=pragmainfotech&password=sms123&msisdn=".$mono."&sid=SMSTST&msg=".urlencode($message)."&fl=0&gwid=2";
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_HEADER, 0);
								$result = curl_exec($ch);
								curl_close($ch);*/

								$selectuserotp="CALL select_otp_vendor('".$Rowvendor['id']."')";
							$res_otp=$obj->selectProc($selectuserotp);
								$OtpCount=mysqli_num_rows($res_otp);
								
								if($OtpCount>0)
								{
									$updatesql="CALL update_otp_vendor('".$Rowvendor['id']."','".$otp."','verified')";
	                                $res_sql1=$obj->update($updatesql);
								}else{
									$insertsql="CALL new_otp_vendor('".$Rowvendor['id']."','".$otp."','verified')";
		                            $res_sql=$obj->insert($insertsql);
								}
								
							
	  
	
	                       		$status['value']="valid";	
			

	
	
						    
							
							}
							else if($Rowvendor["stats"] == 'Disable')
		  {
			  $status['value']="disable";	
			  
		  }
							else
							{
							    	 $status['value']="invalid";
								//echo "@@@1@@@";
								
								//header("location:index.php?msg=invalidotp");
							}
								   array_push($response["data"],$status);
								    ob_clean();
	                            echo str_replace('\/','/',json_encode($response));

}

else if ($_REQUEST["action"] == 'resend_profile__otp') {
	$mono = "+".str_replace(' ','',$_REQUEST['mono']);
$otp = $_REQUEST['otp'];

$select_otp_sms="CALL `fetch_sms_per_type`('OTP');";
$select_sms_per_otp_type=$obj->selectProc($select_otp_sms);
$fetch_otp_sms= mysqli_fetch_array($select_sms_per_otp_type);

$message=str_replace('[OTP]',$otp, $fetch_otp_sms["msg"]);
$message=str_replace('[line]',"\n",$message);

								 $response=array();
	                             $response["data"]= array();
								
								/* $url = "http://login.onlinebusinessbazaar.in/vendorsms/pushsms.aspx?user=pragmainfotech&password=sms123&msisdn=".$mono."&sid=SMSTST&msg=".urlencode($message)."&fl=0&gwid=2";
								 $ch = curl_init();
								 curl_setopt($ch, CURLOPT_URL, $url);
								 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								 curl_setopt($ch, CURLOPT_HEADER, 0);
								 $result = curl_exec($ch);
								 curl_close($ch);*/

									$updatesqlp="CALL update_otp('".$_SESSION["userid"]."','".$otp."','verified')";
	                                $res_sql1p=$obj->update($updatesqlp);
	                                	$status['value']="valid";	
			
							  array_push($response["data"],$status);
							   ob_clean();
	                            echo str_replace('\/','/',json_encode($response));
							
				
}
else if ($_REQUEST["action"] == 'send_otp_with_register') {
	
	
	 $response=array();
	                             $response["data"]= array();
	
$contact = "+".str_replace(' ','',$_REQUEST['contact']);
$pin = $_REQUEST['pin'];
$fname = str_replace(' ','',$_REQUEST['fname']);
$lname = str_replace(' ','',$_REQUEST['lname']);
$password = str_replace(' ','',$_REQUEST['password']);
$email = str_replace(' ','',$_REQUEST['email']);

$select_regotp_sms="CALL `fetch_sms_per_type`('OTP');";
$select_sms_per_regotp_type=$obj->selectProc($select_regotp_sms);
$fetch_regotp_sms= mysqli_fetch_array($select_sms_per_regotp_type);

$message=str_replace('[OTP]',$pin, $fetch_regotp_sms["msg"]);
$message=str_replace('[line]',"\n",$message);
//$message ="Yummy Order Quick Food.\n Your One Time Password is :".$pin."\n Do Not disclose with anyone.";

$insertsql=$obj->selectProcWithParams("CALL new_register('".$fname."','".$lname."','','".$contact."','','','','','".$email."','".$password."','".$datetime."','Added',@out_param,'Normal','')", "SELECT @out_param",'Pending');
		
		$res_sql = mysqli_fetch_array($insertsql);
		$id = $res_sql[0];
	
		$insertaddr="CALL new_otp('".$id."','".$pin."','unverified')";
		
	    $res_sql1=$obj->insert($insertaddr);
        if($res_sql1>0)
        {
			/*$url = "http://login.onlinebusinessbazaar.in/vendorsms/pushsms.aspx?user=pragmainfotech&password=sms123&msisdn=".$contact."&sid=SMSTST&msg=".urlencode($message)."&fl=0&gwid=2";
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_HEADER, 0);
								$result = curl_exec($ch);
								curl_close($ch);*/
			// redirect("location:index.php")->with('success', 'You have registered successfully.'); 
   	$status['value']="valid";	
        }
        else
        {
				 $status['value']="invalid";

           //header("location:index.php?msg1=fail");
        }
		  array_push($response["data"],$status);
		   ob_clean();
	                            echo str_replace('\/','/',json_encode($response));
}
    
 // HITI
    
    // http://192.168.1.35:81/yummy/Mobile_Services/webservices.php?action=get_conf_comp_order_list&v_id=12&o_status=Delivered
    // http://192.168.1.35:81/yummy/Mobile_Services/webservices.php?action=get_conf_comp_order_list&v_id=12&o_status=Confirmed
    
    else if($_REQUEST['action']=="get_conf_comp_order_list")
    {
        getjson("SELECT or1.*,cr.firstname,cr.lastname,cr.contact1  FROM ordr or1,  customer_reg cr  where  or1.stats = '".$_REQUEST['o_status']."' and or1.v_id = '".$_REQUEST['v_id']."' and or1.customer_id = cr.id");
        
    }
    else if($_REQUEST['action']=="get_total_order_month")
    {

         if($_REQUEST['date']!="" && strtolower($_REQUEST["check"])!="upcoming")
        {
            $order_current_date=$_REQUEST["date"];
            $dynamic=" and str_to_date(collect_date,'%m/%d/%Y') = str_to_date('".$order_current_date."','%m/%d/%Y')";
        }
        else
        {
             $dynamic=" and str_to_date(collect_date,'%m/%d/%Y') > str_to_date('".$order_current_date."','%m/%d/%Y')";
        }
        if($_REQUEST['check']!="")
        {

            if(strtolower($_REQUEST['check'])=="rejected")
            {
                $check ="Cancelled,Rejected";
            }
            

            else if(strtolower($_REQUEST["check"])=="upcoming")
            {
                $check = "Pending,Confirmed";
            }
            else 
            {
                $check=$_REQUEST["check"];
            }
        	
        	$check = rtrim($check, ',');
        	$dynamic.=" and FIND_IN_SET(stats,'".$check."' ) > 0 ";
        		
        }



        /*getjson("select sum( cast(ordr.total_price as decimal(11,2))) as total_amount ,count(*) as total_orders from ordr where v_id ='".$_REQUEST['v_id']."' and year(str_to_date('".$order_current_date."','%m/%d/%Y')) = year(STR_TO_DATE(collect_date,'%m/%d/%Y')) and month(str_to_date('".$order_current_date."','%m/%d/%Y')) = month(STR_TO_DATE(collect_date,'%m/%d/%Y')) ".$dynamic);*/
		
		getjson("select  IFNULL(sum( cast(ordr.total_price as decimal(11,2))),0) as total_amount ,count(*) as total_orders from ordr where v_id ='".$_REQUEST['v_id']."'".$dynamic);

        
    }
    else if($_REQUEST['action']=="get_history_total")
    {
		
		$dynamic="";
		$dynamic="and year(str_to_date('".$order_current_date."','%m/%d/%Y')) = year(STR_TO_DATE(collect_date,'%m/%d/%Y')) and month(str_to_date('".$order_current_date."','%m/%d/%Y')) = month(STR_TO_DATE(collect_date,'%m/%d/%Y'))";
		if($_REQUEST['start_date']!="" || $_REQUEST['end_date']!="")
		{															
			
			$dynamic="and STR_TO_DATE(ordr.collect_date , '%m/%d/%Y' )  BETWEEN STR_TO_DATE( '".$_REQUEST['start_date']."', '%m/%d/%Y' ) AND STR_TO_DATE( '".$_REQUEST['end_date']."', '%m/%d/%Y' )";
		}		
		
        if($_REQUEST['check']!="")
        {
        	$check = $_REQUEST['check'];
        	$check = rtrim($check, ',');
        	$dynamic.=" and FIND_IN_SET(stats,'".$check."' ) > 0 ";
        		
        }


       
		
		getjson("select  IFNULL(sum( cast(ordr.total_price as decimal(11,2))),0) as total_amount ,count(*) as total_orders from ordr where v_id ='".$_REQUEST['v_id']."'".$dynamic);


        
    }
    
    else if($_REQUEST['action']=="get_conf_comp_order_Detail")
    {
        getjson("SELECT *  FROM order_detail  where  o_id =  '".$_REQUEST['o_id']."' ) ");
    }
    else if($_REQUEST['action']=="gettoppingname")
    {
      //gettoppingname("SELECT o1.id,o1.detail  FROM order_detail  o1,menu m1 where o1.m_id=m1.id  and find_in_set(m1.id,'".$_REQUEST["id"]."') and o_id='".$_REQUEST["oid"]."'");
       // getjson("SELECT m1.id as menuid,GROUP_CONCAT(e1.topping_name) as toppingname FROM extra_addone  e1,menu m1 where e1.m_id=m1.id  and find_in_set(m1.id,'".$_REQUEST["id"]."') group by m1.id");
       getjson("sELECT e1.id as topping_id, e1.topping_name,e1.amount FROM extra_addone e1,menu m1 where e1.m_id=m1.id and find_in_set(e1.id,'".$_REQUEST["ids"]."')");
    }
else  if($_REQUEST['action']=="decrease_count")
{
		$obj=new DB_Connect;
		$response=array();
		$response["data"]= array();
		$status=array();
		$token=$_REQUEST["tokenid"];
	
	$select_count_qry="select * from vendor_device where token='".$token."'";
	$res_count2=$obj->select($select_count_qry);
	$count=mysqli_fetch_array($res_count2);

			$update_count_qry="update vendor_device set count='".($count['count']-1)."' where token='".$token."'";
			$res_update_count=$obj->update($update_count_qry);
			$row_affected=mysqli_affected_rows($obj->con1);
			if($row_affected>0)
			{
			 
				$status['value']="valid";	

				
			}
			else
			{
					 
				$status['value']="invalid";
				

			
			}
			array_push($response["data"],$status);
			echo str_replace('\/','/',json_encode($response));

	
	
}
else  if($_REQUEST['action']=="clearall_count")
{
		$obj=new DB_Connect;
		$response=array();
		$response["data"]= array();
		$status=array();
		$token=$_REQUEST["tokenid"];
	
	$select_count_qry="select * from vendor_device where token='".$token."'";
	$res_count2=$obj->select($select_count_qry);
	$count=mysqli_fetch_array($res_count2);

			$update_count_qry="update vendor_device set count='0' where token='".$token."'";
			$res_update_count=$obj->update($update_count_qry);
			$row_affected=mysqli_affected_rows($obj->con1);
			if($row_affected>0)
			{
			 
				$status['value']="valid";	

				
			}
			else
			{
					 
				$status['value']="invalid";
				

			
			}
			array_push($response["data"],$status);
			echo str_replace('\/','/',json_encode($response));

	
	
}
function update_password($old_pass,$new_pass,$uid)
{
	$obj=new DB_Connect;
	$select_user="select password from vendor_reg where id='".$uid."' and password='".$old_pass."'";
	$res_select=$obj->select($select_user);
	 $num=mysqli_num_rows($res_select);

	$response=array();
	   $response["data"]= array();
	   $status=array();

if($num>0)
{
	$status['value']="valid";	
	$update_pass="update vendor_reg set password='".$new_pass."' where id='".$uid."'";
	$res_update=$obj->update($update_pass);
	$row_affected=mysqli_affected_rows($obj->con1);
			if($row_affected>0)
			{
			 
				$status['value']="valid";	
				$status['msg']="password updated successfully";	
				
			}
			else
			{
					 
				$status['value']="fail";
				$status['msg']="Try again";	

			
			}
}
else{
	$status['value']="invalid";	
	$status['msg']="old password is wrong";	

}

	   array_push($response["data"],$status);
			echo str_replace('\/','/',json_encode($response));
}
function forgotpass($query2, $client,$username)
  {
	  $obj=new DB_Connect;
//echo $query;
	$result=$obj->select($query2);
	$num=mysqli_num_rows($result);
	
	  $res_row=mysqli_fetch_array($result);
	  $response=array();
	   $response["data"]= array();
	   $status=array();
	  
	   if($num>0)
	   {
	   
		$count_qry="select f.type as usertype,TIMESTAMPDIFF(MINUTE,f.date_time,NOW())as min_diff from vendor_reg v,forgotcount f where f.userid=v.id and v.username='".$username."'";
		$res_count=$obj->select($count_qry);
		$count=mysqli_num_rows($res_count);
		$count_data=mysqli_fetch_array($res_count);
		$password=$res_row['password'];
		$vendor_email=$res_row['email'];
		$name=$res_row['name'];
		$contact=$res_row['contact1'];
  		
		  if($count==0)
		  {
		 
			$count_insert="insert into forgotcount values('','".$res_row['id']."','service_center',now())";
			$res_insert=$obj->insert($count_insert);
			$mail_msg="<html><p>Dear ".str_replace("*","'",$name).",</p></br>
	<div>This e-mail is in response to your recent request to recover your forgotten password.<br>
	Your  password is:".$password."</div><br><div>Regards,<br> Pragma Delivery</div></html>";
			  $msg="Dear ".str_replace("*","'",$name).",
			  This sms is in response to your recent request to recover your forgotten password.
	Your  password is:".$password;
			
			// Use the client to do fun stuff like send text messages!
	 

			$resp=smtpforgot($vendor_email,"pragmatestmail@gmail.com","Pragma Delivery", "Forgot Password", $mail_msg);
		 ob_clean();
		
			if($resp=="1")
			{
			 
				$status['value']="valid";	

				
			}
			else
			{
					
				$status['value']="fail";

			
			}
			array_push($response["data"],$status);
			echo str_replace('\/','/',json_encode($response));
		  }
	 
		  else if($res_row["min_diff"]<=30)
		  {
		 
			$status['value']="disable";
			array_push($response["data"],$status);
		
		  
			echo str_replace('\/','/',json_encode($response));
		  }
		else
		{
			
			
			$count_insert="insert into forgotcount values('','".$pass_data['id']."','service_center',now())";
			$res_insert=$obj->insert($count_insert);
			$mail_msg="<html><p>Dear ".str_replace("*","'",$name).",</p></br>
	<div>This e-mail is in response to your recent request to recover a forgotten password.<br>
	Your  password is:".$password."</div><br><div>Regards Pragma Delivery</div></html>";
			  $msg="Dear ".str_replace("*","'",$name).",
			  This sms is in response to your recent request to recover a forgotten password.
	Your  password is:".$password;
			
			// Use the client to do fun stuff like send text messages!
	 

			$resp=smtpforgot($vendor_email,"pragmatestmail@gmail.com","Pragma Delivery", "Forgot Password", $msg);
		 ob_clean();
		 //valid,disable,invalid
			// "rfertretet".$res;
			if($resp=="1")
			{
			 
				$status['value']="valid";	

				
			}
			else
			{
					 
				$status['value']="invalid";
				

			
			}
			array_push($response["data"],$status);
			echo str_replace('\/','/',json_encode($response));
		}				
	
	}
	   else
	   {
	    
			$status['value']="invalid";
			array_push($response["data"],$status);
			echo str_replace('\/','/',json_encode($response));
	   }
		 
                
	   
	
	    //ob_clean();
	
  }
function gettoppingname($query)
  {


      $response=array();
	  $product = array();
	  $response["data"]= array();
	  $obj=new DB_Connect;
	  $result=$obj->select($query);
	  $fields_num = mysqli_num_fields($result);
    
	  while($row=mysqli_fetch_array($result))
	  {
		 $detail= json_decode($row["detail"]);
	echo $detail[0]."<br/><br/>";
		  for($i=0;$i<$fields_num;$i++)
		  {
			 
				// echo $fieldName = mysqli_fetch_field_direct($result, $i)->name."<br/>";
			  $product[mysqli_fetch_field_direct($result, $i)->name]= str_replace("*","'",addslashes($row[$i]));
			 
		  }
		   
		  array_push($response["data"], $product);
	  }
	  
 ob_clean();
	echo str_replace('\/','/',json_encode($response));
  }

function getjson($query)
  {
       // echo $query;
      $response=array();
	  $product = array();
	  $response["data"]= array();
	  $obj=new DB_Connect;
	  $result=$obj->select($query);
	  
	
	  $fields_num = mysqli_num_fields($result);
	
	
	
	  while($row=mysqli_fetch_array($result))
	  {
		  for($i=0;$i<$fields_num;$i++)
		  {
			 
				// echo $fieldName = mysqli_fetch_field_direct($result, $i)->name."<br/>";
			  $product[mysqli_fetch_field_direct($result, $i)->name]= str_replace("*","'",addslashes($row[$i]));
			 
		  }
		   
		  array_push($response["data"], $product);
	  }
	  ob_clean();
	echo str_replace('\/','/',json_encode($response));
  }
  
  function getjsonorder($query)
  {

  
	
      $response=array();
	  $product = array();
	  $response["data"]= array();
	  $obj=new DB_Connect;
	  $result=$obj->select($query);
	  $fields_num = mysqli_num_fields($result);
	    
	    $index=0;
	    $pr_price=0;
	  while($row=mysqli_fetch_array($result))
	  { 
		
		if($row['orderid']==$tempid)
		{   $detail=array();
		  $array_temp1 = json_decode(str_replace("\'","'",$row["detail"]));
		  if(count($array_temp1)>0)
		  {
		     $detail = ($array_temp1[0]);
		     //echo"<br/>".$row['name']."<br/>";
	            for($k=0;$k<count($array_temp1);$k++)
    		               {
    		                    if($array_temp1[$k]->toppingPrice==null)
    							{
    								$toptotal="";
    								$array_temp1[$k]->toppingPrice="";
    							}
    							else
    							{
        		                      $toptotal+=$array_temp1[$k]->toppingPrice;
    							}
    					$array_temp1[$k]->name=	str_replace("&quot;","''",$array_temp1[$k]->name);
    		               }
    		            //     $orderdata["sub_total"]=$toptotal;
    		             $pr_price=$toptotal+$product["menu_price"];
    		              $product["sub_total"]=$pr_price;
		    array_push($product["detail"],$detail);
		  
		    // $product["sub_total"]=$toptotal;
		  }
		  else
		  {
		      $product["sub_total"]=$pr_price;
		  }
		    // $product[mysqli_fetch_field_direct($result, $i)->name]=$product["detail"];
		}
		else
		{
		   
		    if($index!=0)
		    {
		        array_push($response["data"], $product);
		        //  echo "<br/>".$product["orderid"]."---".$sub+=$pr_price."<br/>";
		    }
		    
    		for($i=0;$i<$fields_num;$i++)
    		{
    		    
    		    if( mysqli_fetch_field_direct($result, $i)->name=="detail")
    		    {
    		           $orderdata=array();
    		         
    		           $array_temp1 = json_decode(str_replace("\'","'",$row["detail"]));
    		            $toptotal=0;
    		           
    		           if(count($array_temp1)>0)
    		           {
    		               for($k=0;$k<count($array_temp1);$k++)
    		               {
    		                    if($array_temp1[$k]->toppingPrice==null)
    							{
    								$toptotal="";
    								$array_temp1[$k]->toppingPrice="";
    							}
    							else
    							{
        		                      $toptotal+=$array_temp1[$k]->toppingPrice;
        		                     
    							}    
    		                  	$array_temp1[$k]->name=	str_replace("&quot;","''",$array_temp1[$k]->name);
    		               }
    		            
    		            $pr_price=$toptotal+$product["menu_price"];
    		            
    		              
    		          
    		                array_push($orderdata,($array_temp1[0]));
    		               // $subtotal=$toptotal+$product["menu_price"];
    		      
    		           }
    		           
    		          
    		         
    		         
    		            $product["sub_total"]=$pr_price;
    		            $product["sub_total1"]=0;
    		          $product[mysqli_fetch_field_direct($result, $i)->name]=$orderdata;
    		    }
				
    		    else
    		    {
    	            //$product[mysqli_fetch_field_direct($result, $i)->name]= str_replace("*","'",addslashes($row[$i]));
    	            $product[mysqli_fetch_field_direct($result, $i)->name]= str_replace("*","'",str_replace("&quot;","''",$row[$i]));
    		    }
    		}
    		
		}
		$index++;
		
			    $tempid = $row['orderid'];    
		
		
	/*	  for($i=0;$i<$fields_num;$i++)
		  {
		      if(mysqli_fetch_field_direct($result, $i)->name=="id")
		      {
		           $od_id=$row[$i]."<br/>";
		          echo "orderid=".$od_id;
		           $detail=array();
		          if($od_id==$tempid)
		          {
		             array_push($detail,$row[$i]);
		          }
		      }
		      
		      if(mysqli_fetch_field_direct($result, $i)->name=="detail")
		      {
		          
		          
		         
		         // echo mysqli_fetch_field_direct($result, $i)->name."<br/>";
		        // echo $row[$i]."<br/>hfhhfghgjj.<br/>";
		       //   $orderdata=array();
		         
		          //$orderdata=explode("#####",$row[$i]);
		          for($j=0;$j<sizeof($orderdata);$j++)
		          {
		              array_push($detail,$orderdata);
		          }
		          $tempid=$row[$i];
		          echo "tempid==".$tempid."<br/>";
		          $product[mysqli_fetch_field_direct($result, $i)->name]=$detail;
		      }
		      else
		      {
			 
				// echo $fieldName = mysqli_fetch_field_direct($result, $i)->name."<br/>";
			  $product[mysqli_fetch_field_direct($result, $i)->name]= str_replace("*","'",addslashes($row[$i]));
		      }
			 
		  }*/
		   
		 
	  }
	  
    if($index>0)
    {
        array_push($response["data"], $product);
    }
    ob_clean();
	echo str_replace('\/','/',json_encode($response));
  }
  
function getlogin($query,$user,$pass,$id,$tokenid,$type)
  {
	//	echo $query;

  $response=array();
	  $product = array();
	  $response["data"]= array();
	  $obj=new DB_Connect;
	  $result=$obj->select($query);
	


	  if( $row=mysqli_fetch_array($result))
	  {
		 
	
		  if(strtolower($row["username"]) == strtolower($user) && $row["password"] == $pass && $row["stats"] == 'Enable')
		  {
		  	$status['value']="valid";	
			$status['id']=$row["id"];	
			$status['name']=$row["name"];
			$status['email']=$row["email"];
			
			 $del_qry="delete from vendor_device where vid='".$row["id"]."' and token='".$tokenid."'";
			$obj->delete($del_qry);
			
			 $qry_insert="insert into vendor_device values ('','".$row["id"]."','".$tokenid."','','".$type."','0')";
		
			$res=$obj->insert($qry_insert);
		  }else if($row["stats"] == 'Disable')
		  {
			  $status['value']="disable";	
			  
		  }
		  else{ 
		  $status['value']="invalid";	
			$status['id']="0";	
			  
		  }
	  }
	  else{
	      //echo "else";
		  $status['value']="invalid";	
			$status['id']="0";	
	  }
	 
	   array_push($response["data"],$status);
	   // ob_clean();
	echo str_replace('\/','/',json_encode($response));
	  
	
	
  }
  
  function getinsertIdjson($query)
  {
	 
	  //include("../www/nail art/db_connect.php");
	
	  $response=array();
	   $response["data"]= array();
	  $obj=new DB_Connect;
  		
	  
	
	  if($result=$obj->insert_id($query))
		{
	  		$status['value']="valid";	
			$status['id']=$result;				
		}
		else
		{
						
			 $status['value']="invalid";

		
		}
	   array_push($response["data"],$status);
	    ob_clean();
	echo str_replace('\/','/',json_encode($response));
	return $result;
  }
  
   function getinsertjson($query)
  {
	 
	  //include("../www/nail art/db_connect.php");
	
	  $response=array();
	   $response["data"]= array();
	  $obj=new DB_Connect;
  		
	  $result=$obj->insert($query);
	
	  if($result)
		{
	  $status['value']="valid";	

			
		}
		else
		{
						
			 $status['value']="invalid";

		
		}
	   array_push($response["data"],$status);
	    ob_clean();
	echo str_replace('\/','/',json_encode($response));
  }
  function getregjson($query,$phone)
  {
	 // echo $query;
	//$sid='ACd0417c37c50e3afe8fec5525a3fe8651';
//token='7d18ac9be6b649e5711336ccedf8d5fc';
//$client = new Client($sid, $token);

	  $phone = "+".str_replace(' ','',$phone);
	 
	

	  //include("../www/nail art/db_connect.php");
	
	  $response=array();
	   $response["data"]= array();
	  $obj=new DB_Connect;
  		
	 $id=$obj->insert_id($query);
		
	
		/*
		
		
		$otp = rand(1000,9999);

$select_otp_sms="CALL `fetch_sms_per_type`('OTP');";

$select_sms_per_otp_type=$obj->selectProc($select_otp_sms);
$fetch_otp_sms= mysqli_fetch_array($select_sms_per_otp_type);

$message=str_replace('[OTP]',$otp, $fetch_otp_sms["msg"]);
		
		$insertaddr="CALL new_otp('".$id."','".$otp."','unverified')";
		
	    $res_sql1=$obj->insert($insertaddr);
		
		*/
		
        if($id!='')
        {
		/*	$client->messages->create(
    // the number you'd like to send the message to
    $phone,
    array(
        // A Twilio phone number you purchased at twilio.com/console
        'from' =>$from_number,
        // the body of the text message you'd like to send
        'body' => $message
    )
);
*/
			// redirect("location:index.php")->with('success', 'You have registered successfully.'); 
   	$status['value']="valid";	
        }
        else
        {
				 $status['value']="invalid";

           //header("location:index.php?msg1=fail");
        }
		  array_push($response["data"],$status);
	                           
	
	
	// ob_clean();
	echo str_replace('\/','/',json_encode($response));
  }
 function getlogoutjson($query)
  {
	 
	  //include("../www/nail art/db_connect.php");
	
	  $response=array();
	   $response["data"]= array();
	  $obj=new DB_Connect;
  		
	  $result=$obj->insert($query);
	
	  if($result)
		{
	  $status['value']="valid";	

			
		}
		else
		{
						
			 $status['value']="invalid";

		
		}
	   array_push($response["data"],$status);
	    ob_clean();
	echo str_replace('\/','/',json_encode($response));
  }
  
  
  
  
function executedata($query)
  {
	 
	  

	  $obj=new DB_Connect;
  		
	  $result=$obj->insert($query);

  }


    function update($query)
  {

	  //include("../www/nail art/db_connect.php");

	  $response=array();
	   $response["data"]= array();
	  $obj=new DB_Connect;

	  $result=$obj->insert($query);



  }



   function getinsertjson_returnid($query)
  {

	  //include("../www/nail art/db_connect.php");

	  $response=array();
	   $response["data"]= array();
	  $obj=new DB_Connect;

	  $result=$obj->insert_id($query);


			 $status['value']=$result;



	   array_push($response["data"],$status);
	    ob_clean();
	echo str_replace('\/','/',json_encode($response));
  }



function sendpush1($title,$description,$query)
{

          $regId= array();
	  $obj=new DB_Connect;
	  $result=$obj->select($query);
	
	  while($row=mysql_fetch_array($result))
	  {
		 $t = $row['tokenid'];
		   
		  array_push($regId, $t);
	  }
	  



    //  require_once('PushNotification.php');
	// Message payload
	$msg_payload = array (
		'mtitle' => $title,
		'mdesc' => $description,
	);
	
	
	// Replace the above variable values
	
	
    	//PushNotifications::android($msg_payload, $regId);
    	
    //	PushNotifications::WP8($msg_payload, $uri);
    	
     PushNotifications::iOS($msg_payload, $regId);
}

function sendpush_android($title,$description,$query)
{

          $regId= array();
	  $obj=new DB_Connect;
	  $result=$obj->select($query);
	
	  while($row=mysql_fetch_array($result))
	  {
		 $t = $row['tokenid'];
		   
		  array_push($regId, $t);
	  }
	  



     // require_once('PushNotification.php');
	// Message payload
	$msg_payload = array (
		'mtitle' => $title,
		'mdesc' => $description,
	);
	
	
	// Replace the above variable values
	
			
					
					$fields = array
					(
						'registration_ids'  => $regId,
						'data'              => $msg_payload 
					);
					
					$headers = array
					(
						'Authorization: key=AAAAYypnLZQ:APA91bHYvlt-g3zLvhj25DKBO4oOFpENnJkqJDL98sQDZtu7mRPcpnxnsjcnOpbjsd6_hRWW7KJX_giSFQS8ZW4U5GWpAxZYQLrtojR41-FUEMgh-HfJP1sbFL2rksTqFGjw86v9yyZ9v4i_D0nTGyqU_QPbt1G86Q',
						'Content-Type: application/json'
					);
					
//print_r($regId);
					$ch = curl_init();
					curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
					curl_setopt( $ch,CURLOPT_POST, true );
					curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
					curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
					curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
					curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );

					curl_close( $ch );
				//	print_r( $result);

    	//PushNotifications::android($msg_payload, $regId);
    
}
 /*function smtpmailer($to, $from, $from_name = 'Split Bill Easy Contact Us', $subject, $body ,$filePath)
{
		global $error;
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth = true; 
	//	$mail->SMTPSecure = 'ssl'; 
		$mail->SMTPKeepAlive = true;
		$mail->Mailer = "smtp";
		$mail->Host = 'mail.arrowtechllc.com';
		$mail->Port = 25;  
		$mail->Username = 'splitbilleasy@pragmanxt.com';  
		$mail->Password = 'Pragma@jay';   
		$mail->IsHTML(true);		
		$mail->SMTPDebug = 1; 
		$mail->From="splitbilleasy@pragmanxt.com";
		$mail->FromName=$from_name ;
		$mail->Sender=$from; // indicates ReturnPath header
		$mail->AddReplyTo($to, $from_name); // indicates ReplyTo headers
		$mail->Subject = $subject;
		$mail->Body = $body;
                if($filePath!="")
                {
		    $mail->AddAttachment($filePath);
                }
		$mail->AddAddress($to);
		if(!$mail->Send())
		{			
			$error = 'Mail error: '.$mail->ErrorInfo;
			//echo $error;
			return false;
		}
		else
		{
			$error = 'Message sent!';
			echo '<script>alert("Message sent Successfully !")</script>';
			return true;
		}
} */
?>
