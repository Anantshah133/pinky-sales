<?php
date_default_timezone_set("Asia/Kolkata");

class DbOperation
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }
  
    // techinician reg
    public function techinician($name, $email, $contact, $service_center, $status,$userid,$password,$MainFileName)
    {


        $stmt = $this->con->prepare("INSERT INTO `technician`( `name`, `email`, `contact`, `service_center`, `status`,`userid`,`password`,`id_proof`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssissss", $name, $email, $contact, $service_center, $status,$userid,$password,$MainFileName);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
            

    }


//technician login
     public function Login($userid, $pass)
    {

        //$password = md5($pass);
        $stmt = $this->con->prepare("SELECT * FROM technician WHERE userid=? and password =? ");
        $stmt->bind_param("ss", $userid, $pass);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;

    }

    // get service center data 
    public function get_technician($userid)
    {
        $stmt = $this->con->prepare("SELECT * FROM technician WHERE userid=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $data;
    }


    // insert service device

  public function insert_technician_device($vid,$token,$type)
    {
       

       
         
        $stmt = $this->con->prepare("INSERT INTO `technician_device`(`uid`, `token`, `type`) VALUES (?,?,?)");
        $stmt->bind_param("iss", $vid, $token, $type);
       
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }



    // logout

     public function logout($id,$tokenid)
    {

         $stmt = $this->con->prepare("delete from technician_device where uid=? and token=?");
        
        $stmt->bind_param("is", $id,$tokenid);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

     // technicain_list
    public function techinician_list($service_center)
    {
        $stmt = $this->con->prepare("SELECT * FROM technician where status='Enable' and service_center=?");
        $stmt->bind_param("i",$service_center);
        $stmt->execute();
        $data = $stmt->get_result();
        $stmt->close();
        return $data;
    }

    public function product_category_list()
    {
        $stmt = $this->con->prepare("SELECT * FROM product_category");        
        $stmt->execute();
        $data = $stmt->get_result();
        $stmt->close();
        return $data;
    }



    

    public function fetch_contact($phone_number)
    {

        $stmt = $this->con->prepare("SELECT contact1 FROM vendor_reg where REPLACE(contact1, ' ', '')=?");
        $stmt->bind_param("s", $phone_number);
        $result = $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;

    }


    // get user device
    public function get_user_device_android($complaint_no)
    {

        
        $stmt = $this->con->prepare("select u1.* from user_device u1,customer_reg c1 where u1.user_contact=c1.contact and c1.complaint_no=? and u1.type='android'");
        $stmt->bind_param("s", $complaint_no);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

// fetch order detail (for domail cronjob)


public function fetch_orderdata($oid)
    {
        $stmt = $this->con->prepare("select o1.id,o1.collect_date as date_time,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,c1.id as customer_id,c1.firstname as cname,c1.email as customer_email,o1.total_price as tprice,v1.name as vname,v1.image as vimage,o1.delivery_address,v1.business_name,v1.id as vendor_id,o1.delivery_instruction,o1.ip_address,v1.email as vendor_email,c1.contact1 as c_contact,v1.address as v_address,v1.contact1 as v_contact from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1 where 
                c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and o1.id=?  group by o1.id;");
        $stmt->bind_param("i", $oid);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $order;
    }

//insert into domail

public function insertinto_domail($cid,$vid,$oid,$order_status,$status)
    {
        $datetime=date("m/d/Y h:i A");
        $stmt = $this->con->prepare("insert into domail (`customerid`, `vendorid`, `orderid`, `type`, `status`, `datetime`) values(?,?,?,?,?,?)");
        $stmt->bind_param("iiisss",$cid,$vid,$oid,$order_status,$status,$datetime);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    }

    // insert notifications for user
    public function add_notification($oid,$order_status,$cid,$vid)
    {
        $datetime=date("m/d/Y h:i A");
        $msg='Your order has been '.$order_status;
        $sender_type='vendor';
        $adminstatus=0;
        $adminplaystatus=0;
        $status=1;
        $playstatus=0;
        $type='order_'.$order_status;

        $stmt = $this->con->prepare("INSERT INTO `notification`(`order_id`, `type`, `v_id`, `user_id`, `msg`, `sender_type`, `status`, `playstatus`, `adminstatus`, `adminplaystatus`, `datetime`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("isiissiiiis",$oid,$type,$vid,$cid,$msg,$sender_type,$status,$playstatus,$adminstatus,$adminplaystatus,$datetime);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    }

// current view order (status wise orders)

    public function current_vieworder($id,$check,$date,$shipper)
    {
        $today=date("m/d/Y");
        $order_current_date=date("m/d/Y");
        if($date!="")
        {
            $order_current_date=$date;
        }


        if(strtolower($check)!="upcoming")
        {
            $dynamic="and  str_to_date(collect_date,'%m/%d/%Y') = str_to_date('".$order_current_date."','%m/%d/%Y')";
            
        }
        else
        {
            $dynamic="and  str_to_date(collect_date,'%m/%d/%Y') > str_to_date('".$today."','%m/%d/%Y')";
        }

        if($check!="")
        {
            if(strtolower($check)=="rejected")
                {
                    $check ="Cancelled,Rejected";
                }
               else if(strtolower($check)=="upcoming")
                {
                    if($shipper=='yes')
                    {
                        $check = "Pending,Confirmed,Rejected,Cancelled,Dispatched";
                    }
                    else
                    {
                        $check = "Pending,Confirmed,Rejected,Cancelled";
                    }
                    
                }
                else 
                {
                    $check=$check;
                }
                $check = rtrim($check, ',');
                $dynamic.=" and FIND_IN_SET(o1.stats,'".$check."' ) > 0 ";


        }

        //order by id
           // $dynamic.=" order by o1.id DESC";
        
         $order_qry="select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date as date_time,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,od1.detail,o1.detail as order_detail,od1.quantity,m1.name,c1.firstname as cname,c1.lastname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as menu_detail,m1.main_price as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as datetime1,o1.delivery_instruction,o1.date_time as order_time, v1.address as takeaway_address,o1.tip_per,o1.tip_amount,o1.extra_charge_per,o1.extra_charge_amount,o1.extra_flag,o1.extra_charge_remark,o1.couponcode,o1.coupondiscount,o1.coupon_discount_per,o1.max_discount,o1.min_amount,o1.self_order,o1.sub_total,o1.paid_by,o1.distance,o1.delivery_boy_amount,o1.user_delivery_charge,o1.vendor_delivery_amount,o1.total_tax  from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1 where c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and m1.id=od1.m_id and m1.fc_id=fc1.id and fc1.v_id=v1.id and o1.payment_status!='INPROCESS' and o1.payment_status!='FAILED' and  v1.id='".$id."' ".$dynamic."   UNION
            select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason  as myreason,o1.ex_charge,o1.dis_price,o1.collect_date as date_time,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,'NA' as detail,o1.detail as order_detail,0 as quantity,'NA' as menuname,c1.firstname as cname,c1.lastname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,0 as foodcid,'NA' as fname,0 as menu_id,'NA' as  menu_detail,0 as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as datetime1,o1.delivery_instruction,o1.date_time as order_time, v1.address as
            takeaway_address,o1.tip_per,o1.tip_amount,o1.extra_charge_per,o1.extra_charge_amount,o1.extra_flag,o1.extra_charge_remark,o1.couponcode,o1.coupondiscount,o1.coupon_discount_per,o1.max_discount,o1.min_amount,o1.self_order,o1.sub_total,o1.paid_by,o1.distance,o1.delivery_boy_amount,o1.user_delivery_charge,o1.vendor_delivery_amount,o1.total_tax from ordr o1,customer_reg c1,vendor_reg v1 where c1.id=o1.customer_id and v1.id=o1.v_id  and
            o1.payment_status!='INPROCESS' and o1.payment_status!='FAILED' and v1.id='".$id."' ".$dynamic." and o1.self_order='true' order by orderid desc";

       // $order_qry="select tb.*,od1.detail,od1.quantity from (select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date as            date_time,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,o1.detail as         order_detail,m1.name,c1.firstname as cname,c1.lastname,c1.contact1 as contact_num,o1.total_price as          tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as        menu_detail,m1.main_price as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as           datetime1,o1.delivery_instruction,o1.date_time as order_time, v1.address as   takeaway_address,o1.tip_per,o1.tip_amount,o1.extra_charge_per,o1.extra_charge_amount,o1.extra_flag,o1.extra_charge_remark,o1.couponcode,o1.coupondiscount,o1.coupon_discount_per,o1.max_discount,o1.min_amount,o1.self_order from ordr o1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1 where c1.id=o1.customer_id  and v1.id=o1.v_id and m1.fc_id=fc1.id and fc1.v_id=v1.id and  o1.payment_status!='INPROCESS' and o1.payment_status!='FAILED' and v1.id='".$id."' ".$dynamic." ) as tb LEFT JOIN order_detail od1 on tb.orderid=od1.o_id and tb.menu_id=od1.m_id group by tb.orderid order by tb.orderid  DESC";


            $resp_order=mysqli_query($this->con,$order_qry);
            return $resp_order;


    }

    public function get_customer($complaint_no)
    {
        $stmt = $this->con->prepare("SELECT * FROM customer_reg where complaint_no=?");
        $stmt->bind_param("s",$complaint_no);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $data;
    }



// get category

public function get_category($id)
    {
        $stmt = $this->con->prepare("SELECT * from food_category WHERE v_id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }

// get profile

    public function get_profile($id)
    {
        $stmt = $this->con->prepare("select *,c.area_name as area from vendor_reg v,area c where v.area=c.srno and v.id=? ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }    

//update password
    public function update_Password($cpass, $npass, $id)
    {

        $date_time = date("m/d/Y h:i A");
        $operation = 'Updated';
        $type = 'Normal';

        $stmt1 = $this->con->prepare("select password from vendor_reg where id=? and password=? ");
        $stmt1->bind_param("is", $id, $cpass);
        $result1 = $stmt1->execute();
        $stmt1->store_result();
        $password = $stmt1->num_rows;

        if ($password > 0) {
            $stmt = $this->con->prepare("update vendor_reg set password=? where id=? ");
            $stmt->bind_param("si", $npass, $id);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 2;
        }

    }

// get category wise product list
    public function get_category_products_list($id,$cat_id)
    {
        $stmt = $this->con->prepare("SELECT * from menu WHERE v_id=?  and fc_id=? and stats!='Deleted' order by id desc");
        $stmt->bind_param("ii", $id,$cat_id);
        $stmt->execute();
        $order = $stmt->get_result();
        $stmt->close();
        return $order;
    }

//update store status

public function update_store_status($v_id,$status)
    {

        $stmt = $this->con->prepare("update vendor_reg set isopen=? where  id= ? ");
        $stmt->bind_param("si", $status,$v_id);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    }

//update extra charge
    public function update_extracharge($o_id,$extra_charge_amount,$total_amount,$remark,$payment_status)
    {

        $stmt = $this->con->prepare("update ordr set payment_status=?,total_price=?,extra_charge_amount=?,extra_charge_remark=?,extra_flag='true' where  id= ? ");
        $stmt->bind_param("ssssi",$payment_status, $total_amount,$extra_charge_amount,$remark,$o_id);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {

            $chargestmt=$this->con->prepare("select * from stripe_charge where order_id=?");
            $chargestmt->bind_param("i",$o_id);
            $chargestmt->execute();
            $charge_result = $chargestmt->get_result()->fetch_assoc();
            $chargestmt->close();

            $odrstmt=$this->con->prepare("select * from ordr o1,customer_reg c1 where  o1.customer_id=c1.id and o1.id=?");
            $odrstmt->bind_param("i",$o_id);
            $odrstmt->execute();
            $odr_result = $odrstmt->get_result()->fetch_assoc();
            $odrstmt->close();
            $amount=($odr_result["total_price"] - $odr_result["extra_charge_amount"]) + $extra_charge_amount ;
            $tamt = $amount*100;
            // Set API key 
            \Stripe\Stripe::setApiKey(STRIPE_API_KEY); 
            try {  
                 $charge = \Stripe\Charge::retrieve($charge_result["stripe_charge_id"]);
                   $charge->capture(['amount'=>$tamt]); 

            
            $status=$charge["status"];
            $paymenintentid=$charge["id"];
            $response=$charge;
            if($status=="succeeded")
            {
                $payment="PAID";
            }
            else

            {
                $payment="UNPAID";
            }
           
            $update_stmt=$this->con->prepare("UPDATE ordr SET payment_status = ?,payment_typ='online',extra_charge_amount=?,extra_charge_remark=?,total_price=?,extra_flag='true' where id =?");
            $update_stmt->bind_param("sdssi",$payment,$extra_charge_amount,$remark,$amount,$o_id);
            $update_stmt->execute();  
            $rows =$update_stmt->affected_rows;         
            $update_stmt->close();
           

              $gatewayname = "card";
              $status=$charge["status"];
              $paymenintentid=$charge["id"];
              $response=$charge;
              $userid = $odr_result["customer_id"];
              $vid=$odr_result["v_id"];
              $trsan_stmt=$this->add_transaction($o_id, $amount, $gatewayname,$paymenintentid,$response,$status,$userid);
              if($trsan_stmt)
              {

                $domail=$this->insert_domail($userid,$vid,$o_id,'payment_release','Enabled');
                return 1;
              }
              else
              {
                return 2;
              }
            
            
            }catch(Exception $e) {  
        $api_error = $e->getMessage();
       return 3;  
    } 
        } else {
            return 0;
        }
    }



// home page
    public function homepage($date,$technician_id)
    {
      

        $stmt = $this->con->prepare("select pending,allocated,closed,cancelled  from(SELECT count(CASE WHEN c2.status='pending' THEN 1 END) as pending,count(CASE WHEN c2.status='allocated'  THEN 1 END) as allocated,count(CASE WHEN  c2.status='cancelled' and c2.allocation_date >='".date("Y-m-01")."' and c2.allocation_date <='".date("Y-m-t")."' THEN 1 END) as cancelled,count(CASE WHEN  c2.status='closed' and c2.allocation_date >='".date("Y-m-01")."' and c2.allocation_date <='".date("Y-m-t")."' THEN 1 END) as closed  FROM customer_reg c1,call_allocation c2 WHERE c2.complaint_no=c1.complaint_no  and c2.technician=?)as tbl1");
        $stmt->bind_param("i", $technician_id);
        
        $stmt->execute();
        $data = $stmt->get_result();
        $stmt->close();
        return $data;
    }


// call list
    public function call_list($date,$type,$technician_id)
    {
        if($type=="pending")
        {
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.name as service_area FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,service_area a1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.id and c2.status='pending'  and c2.technician=?");
            $stmt->bind_param("i",$technician_id);
        }
        else if($type=="allocated")
        {
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.name as service_area FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,service_area a1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.id and c2.status='allocated'  and c2.technician=? ");
            
            $stmt->bind_param("i", $technician_id);
        }
        else if($type=="closed")
        {
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.name as service_area FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,service_area a1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.id and c2.status=?   and c2.allocation_date >='".date("Y-m-01")."' and c2.allocation_date <='".date("Y-m-t")."' and c2.technician=?");
            $stmt->bind_param("si", $type,$technician_id);
        }
        else if($type=="cancelled")
        {
            
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.name as service_area FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,service_area a1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.id and  c2.status=?   and c2.allocation_date >='".date("Y-m-01")."' and c2.allocation_date <='".date("Y-m-t")."' and c2.technician=?");
            $stmt->bind_param("si", $type,$technician_id);
        }
        else
        {
            
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.name as service_area FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,service_area a1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.id and c2.allocation_date <= '".$date."' and c2.technician=? and c2.complaint_no not in (select complaint_no from call_history )");
            $stmt->bind_param("i", $technician_id);
            
        }
        $stmt->execute();
        $data = $stmt->get_result();
        $stmt->close();
        return $data;
    }

    // call history
     public function call_history($complaint_no)
    {
        
        $stmt = $this->con->prepare("SELECT * FROM call_history where  complaint_no=?");
        $stmt->bind_param("s",$complaint_no);
        $stmt->execute();
        $data = $stmt->get_result();
        $stmt->close();
        return $data;
    }


public function add_complaint($complaint_no,$service_center,$techinician)
    {
        $status="new";
       
//echo "INSERT INTO `complaint`( `complaint_no`, `service_center`,`techinician`,  `status`) VALUES ($complaint_no,$service_center,$techinician,$status)";
        $stmt = $this->con->prepare("INSERT INTO `call_history`( `complaint_no`, `service_center`,`technician`,  `status`) VALUES (?,?,?,?)");
        $stmt->bind_param("siis",$complaint_no,$service_center,$techinician,$status);
        $result=$stmt->execute();
        
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    }


    public function get_call_allocation($complaint_no)
    {
        $stmt = $this->con->prepare("SELECT * FROM call_allocation where complaint_no=?");
        $stmt->bind_param("s",$complaint_no);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $data;
    }

    //add call history
    public function add_history($complaint_no,$service_center_id,$technician,$parts_used,$call_type,$service_charge,$parts_charge,$history_status,$reason)
    {
        
       //echo "INSERT INTO `call_history`( `complaint_no`, `service_center`,`technician`,`parts_used`,`call_type`,`service_charge`,`parts_charge`,  `status`) VALUES ('$complaint_no','$service_center_id','$technician','$parts_used','$call_type','$service_charge','$parts_charge','$history_status')";

        $date=date('Y-m-d H:i:s');

        $stmt = $this->con->prepare("INSERT INTO `call_history`( `complaint_no`, `service_center`,`technician`,`parts_used`,`call_type`,`service_charge`,`parts_charge`,  `status`,`reason`,`date_time`) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("siisssssss",$complaint_no,$service_center_id,$technician,$parts_used,$call_type,$service_charge,$parts_charge,$history_status,$reason,$date);
        $result=$stmt->execute();
        
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    }

     // call allocation add
    public function call_allocation_add($complaint_no,$service_center_id, $product_serial_no, $product_model, $purchase_date, $techinician,$allocation_date,$allocation_time,$status,$serial_no_img,$product_model_img,$purchase_date_img,$reason)
    {


        /*$stmt = $this->con->prepare("INSERT INTO `call_allocation`( `complaint_no`, `service_center_id`, `product_serial_no`, `product_model`, `purchase_date`, `technician`, `allocation_date`, `allocation_time`, `status`) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sisssisss",$complaint_no,$service_center_id, $product_serial_no, $product_model, $purchase_date, $techinician,$allocation_date,$allocation_time,$status);*/
        //echo "UPDATE `call_allocation` SET product_serial_no='$product_serial_no',product_model='$product_model',purchase_date='$purchase_date',technician='$techinician',allocation_date='$allocation_date,allocation_time='$allocation_time,status='$status' where  complaint_no= '$complaint_no' ";

        $qry="";
        if($serial_no_img!="")
        {
            $qry.=",serial_no_img='".$serial_no_img."'";
        }
        if($product_model_img!="")
        {
            $qry.=",product_model_img='".$product_model_img."'";
        }
        if($purchase_date_img!="")
        {
            $qry.=",purchase_date_img='".$purchase_date_img."'";
        }

        $update_qry="UPDATE `call_allocation` SET product_serial_no='".$product_serial_no."',product_model='".$product_model."',purchase_date='".$purchase_date."',technician='".$techinician."',allocation_date='".$allocation_date."',allocation_time='".$allocation_time."',status='".$status."',reason='".$reason."'".$qry." where  complaint_no= '".$complaint_no."' ";
        $result=mysqli_query($this->con,$update_qry);
        if ($result) {
            return 0;
        } else {
            return 1;
        }
            

    }


//--------------------------------//
// get total orders

    public function get_total_order_month($v_id,$check,$date)
    {
        $today=date("m/d/Y");
        $order_current_date=date("m/d/Y");
        if($date!="")
        {
            $order_current_date=$date;
        }
        if(strtolower($check)!="upcoming")
        {

            $order_current_date=$date;
            $dynamic=" and str_to_date(collect_date,'%m/%d/%Y') = str_to_date('".$order_current_date."','%m/%d/%Y')";
            
        }
         else
        {
             $dynamic=" and str_to_date(collect_date,'%m/%d/%Y') > str_to_date('".$today."','%m/%d/%Y')";
        }

        if($check!="")
        {
           if(strtolower($check)=="rejected")
            {
                $check ="Cancelled,Rejected";
            }
            

            else if(strtolower($check)=="upcoming")
            {
                $check = "Pending,Confirmed,Rejected,Cancelled";
            }
            else 
            {
                $check=$check;
            }
            
            $check = rtrim($check, ',');
            $dynamic.=" and FIND_IN_SET(stats,'".$check."' ) > 0 ";
        }
         $order_qry="select  IFNULL(sum( cast(ordr.total_price as decimal(11,2))),0) as total_amount ,count(*) as total_orders from ordr,customer_reg c1 where ordr.customer_id=c1.id and ordr.v_id ='".$v_id."' and ordr.payment_status!='INPROCESS' and ordr.payment_status!='FAILED' ".$dynamic;
            $resp_order=mysqli_query($this->con,$order_qry);
            return $resp_order;

    }


// add item category
     public function add_item_category($vendor_id, $name,$details, $status)
    {

        $operation='Added';
        $type='vendor';
        $datetime=date("m/d/Y h:i A");
        $maxno = $this->getmaxsort_item_category();
        $sort_no=$maxno["sort_no"];
        $stmt = $this->con->prepare("INSERT INTO `food_category`(`v_id`, `name`, `details`,  `stats`, `date_time`, `added_by`, `operation`, `sort_order`, `user_type`) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("issssisis",$vendor_id,$name,$details,$status,$datetime,$vendor_id,$operation,$sort_no,$type);
        $result = $stmt->execute();
        
        $stmt->close();
        return $result;
    }

    // update item cateogry
     public function update_item_category($vendor_id, $name,$details, $status,$cat_id)
    {

        $operation='Updated';
        $type='vendor';
        $stmt = $this->con->prepare("update food_category set v_id=?,name=?,details=?,stats=?,added_by=?,operation=?,user_type=? where id=?");
        $stmt->bind_param("isssissi", $vendor_id,$name,$details,$status,$vendor_id,$operation,$type,$cat_id);
        $result = $stmt->execute();
        $rows =$stmt->affected_rows;
        $stmt->close();
        return $rows;
    }

// delete item category

    public function delete_item_category($cat_id)
    {
            
        $stmt = $this->con->prepare("DELETE FROM `food_category` WHERE id = ?");
        $stmt->bind_param("i", $cat_id);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

//update category status

     public function update_category($vendor_id,$cat_id,$status)
    {

        $operation='Updated';
        $type='vendor';
        $stmt = $this->con->prepare("update food_category set stats=?,added_by=?,operation=?,user_type=? where id=? and v_id=?");
        $stmt->bind_param("sissii", $status,$vendor_id,$operation,$type,$cat_id,$vendor_id);
        $result = $stmt->execute();
        $rows =$stmt->affected_rows;
        $stmt->close();
        return $rows;
    }



// update item
 public function update_item($status,$id,$vendor_id)
    {

        $stmt = $this->con->prepare("update menu set stats=?,operation='Updated',user_type=service_center,added_by=? where id=?");
        $stmt->bind_param("sii", $status,$vendor_id,$id);
        $result = $stmt->execute();
        $rows =$stmt->affected_rows;
        $stmt->close();
        return $rows;
    }    


public function update_item_price($price,$discount_percent,$discount_price,$vendor_id,$status,$detail,$type,$id)
    {
       
        $stmt = $this->con->prepare("update menu set main_price=?,discount_per=?,discount_price=?,added_by=?,operation='Updated',user_type='vendor',stats=?,detail=?,typ=? where id=?");
        $stmt->bind_param("sssisssi", $price,$discount_percent,$discount_price,$vendor_id,$status,$detail,$type,$id);
        /*$stmt = $this->con->prepare("update menu set main_price=?,discount_per=?,discount_price=?,added_by=?,operation='Updated',user_type='vendor',stats=? where id=?");
        $stmt->bind_param("sssisi", $price,$discount_percent,$discount_price,$vendor_id,$status,$id);*/
        $result = $stmt->execute();
        $rows =$stmt->affected_rows;
        $stmt->close();
        return $rows;
    } 

    public function update_item_size_price($price,$discount_percent,$discount_price,$status,$vendor_id,$id)
    {


        $stmt = $this->con->prepare("update size_pro set price=?,discount_per=?,discount_price=?,stats=?,added_by=?,operation='Updated' where id=?");
        $stmt->bind_param("ssssii", $price,$discount_percent,$discount_price,$status,$vendor_id,$id);
        $result = $stmt->execute();
        $rows =$stmt->affected_rows;
        $stmt->close();
        return $rows;
    } 

     public function update_item_toppings($id,$mid,$size_name,$topping_name,$amount,$status,$type,$vendor_id)
    {

        //echo "UPDATE `extra_addone` SET `v_id`=$vendor_id,`m_id`=$mid,`size_name`='$size_name',`topping_name`='$topping_name',`amount`=$amount,`stats`='$status',`operation`='updated',`mandatory`='$type' WHERE id=$id";
        $stmt = $this->con->prepare("UPDATE `extra_addone` SET `v_id`=?,`m_id`=?,`size_name`=?,`topping_name`=?,`amount`=?,`stats`=?,`operation`='updated',`mandatory`=? WHERE id=?");
        $stmt->bind_param("iisssssi", $vendor_id,$mid,$size_name,$topping_name,$amount,$status,$type,$id);
        $result = $stmt->execute();
        $rows =$stmt->affected_rows;
        $stmt->close();
        return $rows;
    } 
     //check vendor status
     public function check_vendor_status($vendor_id)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM vendor_reg WHERE id =? ");
        $stmt->bind_param("i", $vendor_id);
        $stmt->execute();
        $result=$stmt->get_result()->fetch_assoc();
        
        $stmt->close();
        return $result;

    }

    //check email
    public function check_email($email)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM vendor_reg WHERE email =? ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result=$stmt->get_result();
        $num_rows=$result->num_rows;
        $stmt->close();
        return $num_rows;

    }
    //check contact
    public function check_contact($contact)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM vendor_reg WHERE contact1 =? ");
        $stmt->bind_param("s", $contact);
        $stmt->execute();
        $result=$stmt->get_result();
        $num_rows=$result->num_rows;
        $stmt->close();
        return $num_rows;

    }

    //check business name
    public function check_business_name($business_name)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM vendor_reg WHERE business_name =? ");
        $stmt->bind_param("s", $business_name);
        $stmt->execute();
        $result=$stmt->get_result();
        $num_rows=$result->num_rows;
        $stmt->close();
        return $num_rows;

    }

    //check user name
   /* public function check_username($usename)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM vendor_reg WHERE username =? ");
        $stmt->bind_param("s", $usename);
        $stmt->execute();
        $result=$stmt->get_result();
        $num_rows=$result->num_rows();
        $stmt->close();
        return $num_rows;

    }*/
    // get customer devices for notification
    public function order_notification_to_user($cid)
    {

        $stmt = $this->con->prepare("select `token`,`type` from customer_devices where type='ios' and cid=? ");
        $stmt->bind_param("i", $cid);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
    // get customer devices for notification android
    public function order_notification_to_user_android($cid)
    {

        $stmt = $this->con->prepare("select `token`,`type` from customer_devices where type='android' and cid=? ");
        $stmt->bind_param("i", $cid);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
    // get notificaiton list
    public function get_notifications($vid)
    {
        $stmt = $this->con->prepare("select n1.*,o1.order_id,o1.id as orderid from notification n1,ordr o1,customer_reg c1 where n1.order_id=o1.id and n1.user_id=c1.id and (n1.sender_type='user' or n1.sender_type='delivery') and n1.v_id=? group by n1.order_id,n1.type order by n1.id desc limit 20");
        $stmt->bind_param("i", $vid);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }
    // get order data for notification redirection
    public function get_user_order_data($order_id)
    {

        
        $stmt = $this->con->prepare("select o1.*,v1.image as vimage,o1.order_id,o1.collect_date,o1.delivery_collection_time as ordertime,o1.stats as oreder_status,o1.total_price as tprice,c1.firstname as customer_name,c1.contact1 as customer_contact from ordr o1,vendor_reg v1,customer_reg c1 where v1.id=o1.v_id and o1.customer_id=c1.id and o1.order_id=? group by o1.id ORDER BY o1.id DESC");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    public function get_orderdetail($id)
    {
        $stmt = $this->con->prepare("select o1.id,o1.stats,od.detail,od.price  as p1 ,o1.tip_per,o1.tip_amount,o1.extra_charge_per,o1.extra_charge_amount,o1.extra_flag,o1.extra_charge_remark,o1.couponcode,o1.coupondiscount,o1.coupon_discount_per,o1.max_discount,o1.min_amount,o1.self_order from order_detail od,ordr o1 WHERE od.o_id=o1.id and od.o_id=? ");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
        
    }



    public function get_menudetail($id)
    {
        $stmt = $this->con->prepare("select name,main_price from menu WHERE id=? ");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
        //  return $states;
    }

// get vendor id
    public function getVendorid($contact)
    {
        $stmt = $this->con->prepare("SELECT * FROM vendor_reg WHERE REPLACE(contact1,' ', '')=?");
        $stmt->bind_param("s", $contact);
        $result = $stmt->execute();
        $faculty = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($result) {
            return $faculty;
        } else {
            return 0;
        }
    }
    // check if extra charge already applied
    public function check_extra_charge($o_id)
    {
        $stmt = $this->con->prepare("SELECT * from ordr WHERE id=? and extra_flag='true'");
        $stmt->bind_param("i", $o_id);
        $stmt->execute();
        $order = $stmt->get_result();
        $stmt->close();
        return $order;
    }
    // get order info
    public function get_order_info($o_id)
    {
        $stmt = $this->con->prepare("select o1.id,o1.total_price,o1.payment_status,o1.payment_typ,o1.order_type,o1.stats,o1.tip_per,o1.tip_amount,o1.extra_charge_per,o1.extra_charge_amount,o1.extra_flag,o1.extra_charge_remark,o1.couponcode,o1.coupondiscount,o1.coupon_discount_per,o1.max_discount,o1.min_amount  from ordr o1 WHERE o1.id=? ");
        $stmt->bind_param("i", $o_id);
        $result = $stmt->execute();
        $order = $stmt->get_result();
        $stmt->close();
        return $order;
    }

    // privacy , updated by jay
    public function get_privacy()
    {
        $stmt = $this->con->prepare("select * from privacy_policy where `type`='vendor'");
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }
// terms and condition , updated by jay
     public function get_terms()
    {
        $stmt = $this->con->prepare("select * from termsandcondition where `type`='vendor'");
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }

// get store type

    public function get_type_of_store()
    {

        $stmt = $this->con->prepare("SELECT `id`, `type`, `image`  FROM `type_of_food` WHERE stats='Enable' and LOWER(type)!='all' order by sort_order");
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }


// register vendor
    public function vendor_reg($name,$username, $password,$email,$business_name,$city,$area,$address,$contact,$delivery_time,$delivery_time_in,$min_order,$order_free_from,$discount,$shipper,$map_location,$percentage,$tip,$extra_charge,$user_type,$radius,$delivery_charge,$delivery_type,$image)
    {
        $datetime=date("m/d/Y h:i A");
        $status='Disable';
        $operation='Added';
        $addedby=1;
        $contact_per="";
        $vat=0;
        $isopen="open";

        //echo "INSERT INTO vendor_reg(`name`, `image`, `username`, `password`, `email`, `business_name`,`city`, `area`, `address`, `map_location`, `contact_person`, `contact1`,`radius`, `delivery_time`, `delivery_time_in`, `min_order`,`order_free_from`,`delivery_charges`,`discount`, `stats`, `added_by`, `date_time`, `operation`,`delivery_type`,`percentage`,`user_type`,`vat`,`isopen`,`shipper`,`tip`,`extra_charge`) VALUES('$name','$image','$username',',$password','$email','$business_name','$city','$area','$address','$map_location','$contact_per','$contact','$radius','$delivery_time','$delivery_time_in','$min_order','$order_free_from','$delivery_charge','$discount','$status','$addedby','$datetime','$operation','$delivery_type','$percentage','$user_type','$vat','$isopen','$shipper','$tip','$extra_charge')";
       
        $stmt = $this->con->prepare("INSERT INTO vendor_reg(`name`, `image`, `username`, `password`, `email`, `business_name`,`city`, `area`, `address`, `map_location`, `contact_person`, `contact1`,`radius`, `delivery_time`, `delivery_time_in`, `min_order`,`order_free_from`,`delivery_charges`,`discount`, `stats`, `added_by`, `date_time`, `operation`,`delivery_type`,`percentage`,`user_type`,`vat`,`isopen`,`shipper`,`tip`,`extra_charge`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param("ssssssiissssssssssssissssssssdd",$name,$image,$username, $password,$email,$business_name,$city,$area,$address,$map_location,$contact_per,$contact,$radius,$delivery_time,$delivery_time_in,$min_order,$order_free_from,$delivery_charge,$discount,$status,$addedby,$datetime,$operation,$delivery_type,$percentage,$user_type,$vat,$isopen,$shipper,$tip,$extra_charge);
                $result = $stmt->execute();   
                $lastId = mysqli_insert_id($this->con);             
                $stmt->close();
                if ($result) {
                    return $lastId;
                } else {
                    return 0;
                }
    }

// add  bussiness time
    public function add_bussiness_time($day,$open_time,$close_time,$type,$vid)
    {
        $datetime=date("m/d/Y h:i A");
        $status='Enable';
        $operation='Added';
        $addedby=1;
       

        $stmt = $this->con->prepare("insert into business_timing(v_id,day,open_time,close_time,type,stats,added_by,date_time,operation) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param("isssssiss",$vid,$day,$open_time,$close_time,$type,$status,$addedby,$datetime,$operation);
                $result = $stmt->execute();                
                $stmt->close();
                if ($result) {
                    return 1;
                } else {
                    return 0;
                }
    }
    // check city
    public function check_city($city_name)
    {

        $stmt = $this->con->prepare("select * from city where LOWER(city_name)=?");
        $stmt->bind_param("s", $city_name);
        $stmt->execute();
        $city = $stmt->get_result();
        $stmt->close();
        return $city;
    }
// insert city
public function add_city($city)
    {
        $datetime=date("m/d/Y h:i A");
        $status='Enable';
        $operation='Added';
        $addedby=1;
        $stmt = $this->con->prepare("INSERT INTO `city`(`city_name`, `stats`, `added_by`, `date_time`, `operation`) VALUES(?,?,?,?,?)");
                $stmt->bind_param("ssiss", $city,$status,$addedby,$datetime,$operation);
                $result = $stmt->execute();
                $lastId = mysqli_insert_id($this->con);
                $stmt->close();
                if ($result) {
                    return $lastId;
                } else {
                    return 0;
                }
    }

//INSERT UNIQUE AREA 
    public function insert_unique_area($zipcode,$cityid)
    {
        $datetime=date("m/d/Y h:i A");
        $status='Enable';
        $operation='Added';
        $addedby=1;
       
        $stmt = $this->con->prepare("INSERT IGNORE INTO area(`id`,`area_name`,`city`,`stats`, `added_by`, `date_time`, `operation`) VALUES(?,?,?,?,?,?,?)");
                $stmt->bind_param("ssissss",$zipcode,$zipcode,$cityid,$status,$addedby,$datetime,$operation);
                $result = $stmt->execute();
                
                $stmt->close();
                if ($result) {
                    return 1;
                } else {
                    return 0;
                }
    }

    //INSERT VENDOR STORE TYPE 
    public function insert_store_type($vid,$type)
    {
        
        $operation='Added';
        
        $stmt = $this->con->prepare("insert into vendor_food_type(type_of_food,vid,operation)values(?,?,?);");
                $stmt->bind_param("iis",$type,$vid,$operation);
                $result = $stmt->execute();
                
                $stmt->close();
                if ($result) {
                    return 1;
                } else {
                    return 0;
                }
    }

    //INSERT STORE DELIVERY AREAS 
    public function delivers_at($vid,$zipcode)
    {
        
        $stmt = $this->con->prepare("insert into vendor_area(vid,area_id)values(?,?);");
                $stmt->bind_param("is",$vid,$zipcode);
                $result = $stmt->execute();
                
                $stmt->close();
                if ($result) {
                    return 1;
                } else {
                    return 0;
                }
    }

    // check stripe charge
    public function check_stripe_charge($orderid)
    {
        //echo "select * from stripe_charge where order_id=$orderid";
        $stmt = $this->con->prepare("select * from stripe_charge where order_id=?");
        $stmt->bind_param("i", $orderid);
        $stmt->execute();
        $charge = $stmt->get_result();
        $stmt->close();
        return $charge;
    }

    // get delivery images
    public function get_delivery_images($orderid)
    {
        //echo "select d1.* from job_assign j1,delivery_images d1,ordr o1 where d1.job_id=j1.id and j1.order_id=o1.id and o1.id=$orderid";
      //  echo $orderid;
        $stmt = $this->con->prepare("select d1.* from job_assign j1,delivery_images d1,ordr o1 where d1.job_id=j1.id and j1.order_id=o1.id and o1.id=?");
        $stmt->bind_param("i", $orderid);
        $stmt->execute();
        $charge = $stmt->get_result();
        $stmt->close();
        return $charge;
    }

    // get external order data
    public function get_external_order_data($orderid)
    {

        $stmt = $this->con->prepare("select j1.* from job_assign j1,ordr o1 where j1.order_id=o1.id and job_status!='reject' and job_status!='auto_reject' and o1.id=?");
        $stmt->bind_param("i", $orderid);
        $stmt->execute();
        $charge = $stmt->get_result();
        $stmt->close();
        return $charge;
    }

    // get job data for order

    public function get_job_data($o_id)
    {

        $stmt = $this->con->prepare("select j1.*,GROUP_CONCAT(db.name) as delivery_boy_name,CASE when j1.job_status='pending' then ''  else db.contact  end as delivery_boy_contact,distance from job_assign j1,delivery_boy_reg db where j1.delivery_boy_id=db.id and order_id=? and j1.job_status!='reject' and j1.job_status!='auto_reject'  group by j1.order_id");
        $stmt->bind_param("i", $o_id);
        $result = $stmt->execute();
        $job = $stmt->get_result();
        $stmt->close();
        return $job;
        
    }

    // get delivery boy list
    public function get_delivery_boy_list($o_id)
    {
       
        $stmt = $this->con->prepare("SELECT db.id,db.name,'MyCt delivery executives' as owner_name,db.status FROM delivery_boy_reg db, subadmin s1  where db.owner_id=s1.id  and db.status='Enable' and db.owned_by='admin'
UNION

SELECT db.id,db.name,v1.business_name as owner_name,db.status FROM delivery_boy_reg db,vendor_reg v1,ordr o1 where db.owner_id=v1.id and o1.v_id=v1.id and o1.id=?
 and db.status='Enable' and db.owned_by='vendor'");
        $stmt->bind_param("i", $o_id);
        $result = $stmt->execute();
        $delivery_boy = $stmt->get_result();
        $stmt->close();
        return $delivery_boy;
        
    }
    // get current delivery boy job status
    public function get_db_job_status($dbid)
    {
       
        $stmt = $this->con->prepare("select * from job_assign where delivery_boy_id=? and job_status='dispatch'");
        $stmt->bind_param("i", $dbid);
        $result = $stmt->execute();
        $delivery_boy = $stmt->get_result();
        $stmt->close();
        return $delivery_boy;
        
    }

    // check delivery boy
     public function check_delivery_boy($id)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM delivery_boy_avalibility WHERE delivery_boy_id =? order by id desc limit 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resp=$stmt->get_result();
        
        $stmt->close();
        return $resp;

    }
    // assign job
    public function add_job($dlvry_boy,$o_id)
    {
        
        $status='pending';
        $key='123';
        $key_status='unverified';
        $datetime=date('m/d/Y h:i A');
        $stmt = $this->con->prepare("INSERT INTO `job_assign`(`delivery_boy_id`, `order_id`, `job_status`, `secret_key`, `key_status`, `date_time`) values (?,?,?,?,?,?)");
        $stmt->bind_param("iissss",$dlvry_boy,$o_id,$status,$key,$key_status,$datetime);
        $result = $stmt->execute();
        
        $stmt->close();
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    // assign external job
    public function add_external_job($dlvry_boy,$o_id)
    {
        
        $status='pending';
        $key='123';
        $key_status='unverified';
        $datetime=date('m/d/Y h:i A');
       // echo "INSERT INTO `job_assign`( `delivery_boy_id`, `order_id`, `job_status`, `distance`, `secret_key`, `key_status`, `date_time`) values (?,?,?,?,?,?,?)";
        $stmt = $this->con->prepare("INSERT INTO `job_assign`( `delivery_boy_id`, `order_id`, `job_status`,  `secret_key`, `key_status`, `date_time`) values (?,?,?,?,?,?)");
        $stmt->bind_param("iissss",$dlvry_boy,$o_id,$status,$key,$key_status,$datetime);
        $result = $stmt->execute();
        
        $stmt->close();
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }


    // get customer devices for notification
    public function order_notification_to_deliveryboy($did)
    {

        $stmt = $this->con->prepare("SELECT J.*,V.status  FROM delivery_boy_device as J INNER JOIN (SELECT * FROM delivery_boy_avalibility WHERE id IN (SELECT MAX(id) FROM delivery_boy_avalibility GROUP BY delivery_boy_id)) AS V ON J.db_id = V.delivery_boy_id and J.type='ios'  and find_in_set(db_id,?) group by J.token");
        $stmt->bind_param("s", $did);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
    // get delivery devices for notification android
    public function order_notification_to_deliveryboy_android($did)
    {

        //$stmt = $this->con->prepare("select * from delivery_boy_device where type='android' and find_in_set(db_id,?) group by token ");
        
        $stmt = $this->con->prepare("SELECT J.*,V.status  FROM delivery_boy_device as J INNER JOIN (SELECT * FROM delivery_boy_avalibility WHERE id IN (SELECT MAX(id) FROM delivery_boy_avalibility GROUP BY delivery_boy_id)) AS V ON J.db_id = V.delivery_boy_id and J.type='android'  and find_in_set(db_id,?) group by J.token");
        $stmt->bind_param("s", $did);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    //fetch delivery boy device android
    public function delivery_boy_device_android($oid)
    {

        $stmt = $this->con->prepare("SELECT * FROM job_assign j1,delivery_boy_device d1 where j1.delivery_boy_id=d1.db_id and j1.order_id=? and d1.type='android'");
        $stmt->bind_param("i", $oid);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    //fetch delivery boy device ios
    public function delivery_boy_device_ios($oid)
    {

        $stmt = $this->con->prepare("SELECT * FROM job_assign j1,delivery_boy_device d1 where j1.delivery_boy_id=d1.db_id and j1.order_id=? and d1.type='ios'");
        $stmt->bind_param("i", $oid);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }



    //fetch delivery boy device android with status
    public function delivery_boy_device_android_status($oid,$status)
    {

        $stmt = $this->con->prepare("SELECT * FROM job_assign j1,delivery_boy_device d1 where j1.delivery_boy_id=d1.db_id and j1.order_id=? and d1.type='android' and j1.job_status=?");
        $stmt->bind_param("is", $oid,$status);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    //fetch delivery boy device ios with status
    public function delivery_boy_device_ios_status($oid,$status)
    {

        $stmt = $this->con->prepare("SELECT * FROM job_assign j1,delivery_boy_device d1 where j1.delivery_boy_id=d1.db_id and j1.order_id=? and d1.type='ios' and j1.job_status=?");
        $stmt->bind_param("is", $oid,$status);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    public function order_notification_to_vendor($v_id)
    {
        
        $stmt = $this->con->prepare("select `token`,`type`,`count` from vendor_device where `type`='ios' and vid=? ");
        $stmt->bind_param("s", $v_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
    public function order_notification_to_vendor_android($v_id)
    {
        $stmt = $this->con->prepare("select `token`,`type`,`count` from vendor_device where `type`='android' and vid=? ");
        $stmt->bind_param("s", $v_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    // admin devices ios
     public function order_notification_to_admin()
    {
        
        $stmt = $this->con->prepare("select `token`,`type`,`count` from admin_device where `type`='ios' group by token");
        
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    // admin devices android
     public function order_notification_to_admin_android()
    {
        
        $stmt = $this->con->prepare("select `token`,`type`,`count` from admin_device where `type`='android' group by token");
        
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
    // update multiple job status
    public function job_update($o_id)
    {
        $stmt = $this->con->prepare("update job_assign set job_status='auto_reject' where order_id=? and job_status!='reject' and job_status!='auto_reject'");
        $stmt->bind_param("i",$o_id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        /*$stmt->store_result();
        $num_rows = $stmt->num_rows;*/
        $stmt->close();
        return $affected > 0;

    }




    public function search_order($id,$search)
    {
        $today=date("m/d/Y");
        $order_current_date=date("m/d/Y");
       /* if($date!="")
        {
            $order_current_date=$date;
        }


        if(strtolower($check)!="upcoming")
        {
            $dynamic="and  str_to_date(collect_date,'%m/%d/%Y') = str_to_date('".$order_current_date."','%m/%d/%Y')";
            
        }
        else
        {
            $dynamic="and  str_to_date(collect_date,'%m/%d/%Y') > str_to_date('".$today."','%m/%d/%Y')";
        }

        if($check!="")
        {
            if(strtolower($check)=="rejected")
                {
                    $check ="Cancelled,Rejected";
                }
               else if(strtolower($check)=="upcoming")
                {
                    if($shipper=='yes')
                    {
                        $check = "Pending,Confirmed,Rejected,Cancelled,Dispatched";
                    }
                    else
                    {
                        $check = "Pending,Confirmed,Rejected,Cancelled";
                    }
                    
                }
                else 
                {
                    $check=$check;
                }
                $check = rtrim($check, ',');
                $dynamic.=" and FIND_IN_SET(o1.stats,'".$check."' ) > 0 ";


        }

        //order by id
            $dynamic.=" order by o1.id DESC";
        
*/

         $order_qry="select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date as date_time,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,od1.detail,o1.detail as order_detail,od1.quantity,m1.name,c1.firstname as cname,c1.lastname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as menu_detail,m1.main_price as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as datetime1,o1.delivery_instruction,o1.date_time as order_time, v1.address as takeaway_address,o1.tip_per,o1.tip_amount,o1.extra_charge_per,o1.extra_charge_amount,o1.extra_flag,o1.extra_charge_remark,o1.couponcode,o1.coupondiscount,o1.coupon_discount_per,o1.max_discount,o1.min_amount,o1.self_order,o1.sub_total,o1.paid_by,o1.distance,o1.delivery_boy_amount,o1.user_delivery_charge,o1.vendor_delivery_amount   from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1 where c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and m1.id=od1.m_id and m1.fc_id=fc1.id and fc1.v_id=v1.id and o1.payment_status!='PARTIAL' and  v1.id='".$id."' and (c1.firstname LIKE '%".$search."%' or c1.lastname LIKE '%".$search."%' or o1.order_id LIKE '%".$search."%' or c1.contact1 LIKE '%".$search."%') 
        UNION

select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as
vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason
as myreason,o1.ex_charge,o1.dis_price,o1.collect_date as
date_time,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,'NA' as detail,o1.detail as
order_detail,0 as quantity,'NA' as menuname,c1.firstname as cname,c1.lastname,c1.contact1 as contact_num,o1.total_price as
tprice,v1.name as vname,v1.id as vid,0 as foodcid,'NA' as fname,0 as menu_id,'NA' as  menu_detail,0 as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as
datetime1,o1.delivery_instruction,o1.date_time as order_time, v1.address as
takeaway_address,o1.tip_per,o1.tip_amount,o1.extra_charge_per,o1.extra_charge_amount,o1.extra_flag,o1.extra_charge_remark,o1.couponcode,o1.coupondiscount,o1.coupon_discount_per,o1.max_discount,o1.min_amount,o1.self_order,o1.sub_total,o1.paid_by,o1.distance,o1.delivery_boy_amount,o1.user_delivery_charge,o1.vendor_delivery_amount
from ordr o1,customer_reg c1,vendor_reg v1 where c1.id=o1.customer_id and
 v1.id=o1.v_id and o1.payment_status!='PARTIAL' and v1.id='8' and (c1.firstname LIKE '%".$search."%' or c1.lastname LIKE '%".$search."%' or
o1.order_id LIKE '%".$search."%' or c1.contact1 LIKE '%".$search."%') and o1.self_order='true' order by orderid desc";
            $resp_order=mysqli_query($this->con,$order_qry);
            return $resp_order;
//select o1.id as orderid,o1.delivery_address,o1.id,o1.operation,SUBSTRING(o1.collect_date,1,10) as odate,v1.image as vimage,v1.discount,v1.delivery_charges,v1.min_order,o1.added_by,o1.order_id,o1.payment_typ,o1.payment_status,o1.reason as myreason,o1.ex_charge,o1.dis_price,o1.collect_date as date_time,o1.order_from,o1.order_type,o1.delivery_collection_time,o1.stats,od1.detail,o1.detail as order_detail,od1.quantity,m1.name,c1.firstname as cname,c1.lastname,c1.contact1 as contact_num,o1.total_price as tprice,v1.name as vname,v1.id as vid,fc1.id as foodcid,fc1.name as fname,m1.id as menu_id,m1.detail as menu_detail,m1.main_price as menu_price,CONCAT(o1.collect_date,' ',o1.delivery_collection_time) as datetime1,o1.delivery_instruction,o1.date_time as order_time, v1.address as takeaway_address,o1.tip_per,o1.tip_amount,o1.extra_charge_per,o1.extra_charge_amount,o1.extra_flag,o1.extra_charge_remark,o1.couponcode,o1.coupondiscount,o1.coupon_discount_per,o1.max_discount,o1.min_amount  from ordr o1,order_detail od1,customer_reg c1,vendor_reg v1,menu m1,food_category fc1 where c1.id=o1.customer_id and o1.id=od1.o_id and v1.id=o1.v_id and m1.id=od1.m_id and m1.fc_id=fc1.id and fc1.v_id=v1.id and o1.payment_status!='PARTIAL' and  v1.id='1' and (c1.firstname LIKE '%kaushal%' or c1.lastname LIKE '%kaushal%' or o1.order_id LIKE '%kaushal%' or c1.contact1 LIKE '%jay%') and  str_to_date(collect_date,'%m/%d/%Y') = str_to_date('02/17/2021','%m/%d/%Y') and FIND_IN_SET(o1.stats,'pending' ) > 0  order by o1.id DESC

    }

    //get transaction data
    public function get_transaction($order_id)
    {
        $stmt = $this->con->prepare("select * from transaction t1,ordr o1 WHERE t1.order_id=o1.order_id and o1.id=?");
        $stmt->bind_param("i", $order_id);
        $result = $stmt->execute();
        $trans = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($result) {
            return $trans;
        } else {
            return 0;
        }
    }
    
    // insert into refund
    public function insert_refund($order_id,$refund_id,$refund_response,$refund_msg)
    {
        $datetime=date("m/d/Y h:i A");
        $stmt = $this->con->prepare("insert into domail (`customerid`, `vendorid`, `orderid`, `type`, `status`, `datetime`) values(?,?,?,?,?,?)");
        $stmt->bind_param("iiisss",$cid,$vid,$oid,$order_status,$status,$datetime);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    }

    // update payment status
    public function update_payment_status($oid)
        {

            $stmt = $this->con->prepare("update ordr set payment_status='REFUND' where id = ? ");
            $stmt->bind_param("i", $oid);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                return 0;
            } else {
                return 1;
            }
        }



        // get account summary
    public function get_account_summary($v_id,$from_date,$to_date)
    {
       // echo "select (SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and payment_status='PAID') as date_range_total,(SELECT sum(total_price)as total FROM ordr where v_id=? and payment_status='PAID') as total_earning";
//echo "select (SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date('$from_date','%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date('$to_date','%m/%d/%Y') and payment_status='PAID') as date_range_total,(SELECT sum(total_price)as total FROM ordr where v_id=$v_id and payment_status='PAID') as total_earning,(SELECT sum(tip_amount) FROM ordr where v_id=$v_id and str_to_date(date_time,'%m/%d/%Y') >= str_to_date('$from_date','%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date('$to_date','%m/%d/%Y') and payment_status='PAID') as date_range_tip,(SELECT sum(tip_amount)as total FROM ordr where v_id=? and payment_status='PAID') as total_tip";
        //echo "select (SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and payment_status='PAID') as date_range_total,(SELECT sum(total_price)as total FROM ordr where v_id=? and payment_status='PAID') as total_earning,(SELECT sum(tip_amount) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and payment_status='PAID') as date_range_tip,(SELECT sum(tip_amount) FROM ordr where v_id=? and payment_status='PAID') as total_tip,(SELECT sum(ex_charge) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and payment_status='PAID') as date_delivery_charge,(SELECT sum(ex_charge) FROM ordr where v_id=? and payment_status='PAID') as total_delivery_charge";
        //echo $v_id,$from_date,$to_date;
        $stmt = $this->con->prepare("select (SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and payment_status='PAID' and self_order!='true') as date_range_total, (SELECT sum(total_price)as total FROM ordr where v_id=? and payment_status='PAID' and self_order!='true') as total_earning, (SELECT sum(tip_amount) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and payment_status='PAID') as date_range_tip, (SELECT sum(tip_amount) FROM ordr where v_id=? and payment_status='PAID') as total_tip,( SELECT sum(ex_charge) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and payment_status='PAID') as date_delivery_charge,(SELECT sum(ex_charge) FROM ordr where v_id=? and payment_status='PAID') as total_delivery_charge,(SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and payment_status='PAID' and self_order='true') as external_date_range_total, (SELECT sum(total_price)as total FROM ordr where v_id=? and payment_status='PAID' and self_order='true') as external_total_earning");
        $stmt->bind_param("issiissiissiissi", $v_id,$from_date,$to_date,$v_id,$v_id,$from_date,$to_date,$v_id,$v_id,$from_date,$to_date,$v_id,$v_id,$from_date,$to_date,$v_id);
        $result = $stmt->execute();
        $account = $stmt->get_result();
        $stmt->close();
        return $account;
        
    }

    // get received amount for date range
    public function get_received_amount($v_id,$from_date,$to_date)
    {
       
        $stmt = $this->con->prepare("select(select sum(total_price) from ordr where v_id=? and payment_typ='cod' and payment_status='PAID' and self_order='true' and paid_by='vendor' and order_type='Takeaway' and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') )as amount1,(select sum(paymentAmount) from accounting where vendor_id=? and str_to_date(paymentDate,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(paymentDate,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') )as amount2");
        $stmt->bind_param("ississ", $v_id,$from_date,$to_date,$v_id,$from_date,$to_date);
        $result = $stmt->execute();
        $account = $stmt->get_result();
        $stmt->close();
        return $account;
        
    }

    // get total received amount
    public function get_total_received($v_id)
    {
       
        $stmt = $this->con->prepare("select(select sum(total_price) from ordr where v_id=? and payment_typ='cod' and payment_status='PAID'  )as amount1,(select sum(paymentAmount) from accounting where vendor_id=? )as amount2");
        $stmt->bind_param("ii", $v_id,$v_id);
        $result = $stmt->execute();
        $account = $stmt->get_result();
        $stmt->close();
        return $account;
        
    }
    // get total delivered 
     public function get_delivery_data($v_id,$from_date,$to_date)
    {
       
        $stmt = $this->con->prepare("select (SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='delivery' and payment_status='PAID') as total,(SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='delivery' and LOWER(stats)='delivered' and payment_status='PAID' ) as completed,(SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='delivery' and LOWER(stats)='delivered' and payment_status='PAID' ) as amount");
        $stmt->bind_param("issississ", $v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date);
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }

    // get total pickup 
     public function get_pickup_data($v_id,$from_date,$to_date)
    {
       //echo "select (SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='takeaway' and payment_status='PAID' ) as total,(SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='takeaway' and LOWER(stats)='delivered' and payment_status='PAID' ) as completed,(SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='takeaway' and LOWER(stats)='delivered' and payment_status='PAID' ) as amount";
        $stmt = $this->con->prepare("select (SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='takeaway' and payment_status='PAID' ) as date_total,(SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='takeaway' and LOWER(stats)='delivered' and payment_status='PAID' ) as date_completed,(SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='takeaway' and LOWER(stats)='delivered' and payment_status='PAID' ) as date_amount,(SELECT COUNT(*) FROM ordr where v_id=?  and LOWER(order_type)='takeaway' and payment_status='PAID' ) as total_order,(SELECT COUNT(*) FROM ordr where v_id=?  and LOWER(order_type)='takeaway' and LOWER(stats)='delivered' and payment_status='PAID' ) as total_completed,(SELECT sum(total_price) FROM ordr where v_id=?  and LOWER(order_type)='takeaway' and LOWER(stats)='delivered' and payment_status='PAID' ) as total_amount");

        $stmt->bind_param("issississiii", $v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$v_id,$v_id);
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }
    // admin delivery boy data
     public function get_admin_delivery_data($v_id,$from_date,$to_date)
    {
      //echo "select (SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered' and d1.owned_by='admin' and o1.payment_status='PAID' and o1.self_order!='true') as date_total,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_completed,(SELECT sum(o1.total_price) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_amount,(SELECT sum(o1.ex_charge) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_delivery_charges,(SELECT sum(o1.tip_amount) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_tip,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=?  and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered' and d1.owned_by='admin' and o1.payment_status='PAID' and o1.self_order!='true') as total_order,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=?  and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_completed,(SELECT sum(o1.total_price) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_amount,(SELECT sum(o1.ex_charge) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_delivery_charges,(SELECT sum(o1.tip_amount) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_tip";
       
        $stmt = $this->con->prepare("select (SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered' and d1.owned_by='admin' and o1.payment_status='PAID' and o1.self_order!='true' ) as date_total,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true' ) as date_completed,(SELECT sum(o1.total_price) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true')  as date_amount,(SELECT sum(o1.ex_charge) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_delivery_charges,(SELECT sum(o1.tip_amount) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_tip,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=?  and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered' and d1.owned_by='admin' and o1.payment_status='PAID' and o1.self_order!='true' ) as total_order,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=?  and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_completed,(SELECT sum(o1.total_price) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_amount,(SELECT sum(o1.ex_charge) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_delivery_charges,(SELECT sum(o1.tip_amount) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_tip");
        $stmt->bind_param("issississississiiiii", $v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$v_id,$v_id,$v_id,$v_id);
        
 /*$stmt = $this->con->prepare("select (SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered' and d1.owned_by='admin' and o1.payment_status='PAID' ) as total,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID' ) as completed,(SELECT sum(o1.total_price) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID') as amount,(SELECT sum(o1.ex_charge) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='admin' and j1.job_status='deliver' and o1.payment_status='PAID') as total_amount ");
        $stmt->bind_param("ississississ", $v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date);*/
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }
    // vendor delivery boy data
     public function get_vendor_delivery_boy_data($v_id,$from_date,$to_date)
    {

       // echo "select (SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered' and d1.owned_by='vendor' and o1.payment_status='PAID' and o1.self_order!='true' ) as date_total,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='vendor' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true' ) as date_completed,(SELECT sum(o1.total_price) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='vendor' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true')  as date_amount,(SELECT sum(o1.ex_charge) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='vendor' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_delivery_charges,(SELECT sum(o1.tip_amount) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by='vendor' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_tip,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=?  and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered' and d1.owned_by='vendor' and o1.payment_status='PAID' and o1.self_order!='true' ) as total_order,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=?  and LOWER(o1.order_type)='delivery' and d1.owned_by='vendor' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_completed,(SELECT sum(o1.total_price) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by='vendor' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_amount,(SELECT sum(o1.ex_charge) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by='vendor' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_delivery_charges,(SELECT sum(o1.tip_amount) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by='vendor' and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_tip";
       
        $stmt = $this->con->prepare("select (SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered' and d1.owned_by=service_center and o1.payment_status='PAID' and o1.self_order!='true' ) as date_total,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by=service_center and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true' ) as date_completed,(SELECT sum(o1.total_price) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by=service_center and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true')  as date_amount,(SELECT sum(o1.ex_charge) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by=service_center and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_delivery_charges,(SELECT sum(o1.tip_amount) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and str_to_date(o1.date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(o1.date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(o1.order_type)='delivery' and d1.owned_by=service_center and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as date_tip,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=?  and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered' and d1.owned_by=service_center and o1.payment_status='PAID' and o1.self_order!='true' ) as total_order,(SELECT count(*) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=?  and LOWER(o1.order_type)='delivery' and d1.owned_by=service_center and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_completed,(SELECT sum(o1.total_price) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by=service_center and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_amount,(SELECT sum(o1.ex_charge) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by=service_center and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_delivery_charges,(SELECT sum(o1.tip_amount) FROM ordr o1,job_assign j1,delivery_boy_reg d1 where j1.order_id=o1.id and j1.delivery_boy_id=d1.id and o1.v_id=? and LOWER(o1.order_type)='delivery' and d1.owned_by=service_center and j1.job_status='deliver' and o1.payment_status='PAID' and o1.self_order!='true') as total_tip");
        $stmt->bind_param("issississississiiiii", $v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$v_id,$v_id,$v_id,$v_id);
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }

    // delivered by vendor data
     public function get_vendor_delivery_data($v_id,$from_date,$to_date)
    {
       
        $stmt = $this->con->prepare("select (SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='delivery' and LOWER(stats)='delivered' and payment_status='PAID' and id not in (select order_id from job_assign where job_status!='reject' and job_status!='auto_reject')) as total,(SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='delivery' and LOWER(stats)='delivered' and payment_status='PAID' and id not in (select order_id from job_assign where job_status!='reject' and job_status!='auto_reject')) as completed,(SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='delivery' and LOWER(stats)='delivered' and payment_status='PAID' and id not in (select order_id from job_assign where job_status!='reject' and job_status!='auto_reject')) as amount");
        $stmt->bind_param("issississ", $v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date);
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }

    //get external order data

     public function get_external_data($v_id,$from_date,$to_date)
    {
       
        $stmt = $this->con->prepare("select (SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and self_order='true' and payment_status='PAID' ) as date_total,(SELECT COUNT(*) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and self_order='true'  and LOWER(stats)='delivered' and payment_status='PAID' ) as date_completed,(SELECT sum(total_price) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and self_order='true'  and LOWER(stats)='delivered' and payment_status='PAID' ) as date_amount,(SELECT sum(ex_charge) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and self_order='true'  and LOWER(stats)='delivered' and payment_status='PAID' ) as date_delivery_charges,(SELECT sum(tip_amount) FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and self_order='true'  and LOWER(stats)='delivered' and payment_status='PAID' ) as date_tip,(SELECT COUNT(*) FROM ordr where v_id=?  and self_order='true' and payment_status='PAID' ) as total_order,(SELECT COUNT(*) FROM ordr where v_id=? and self_order='true'  and LOWER(stats)='delivered' and payment_status='PAID' ) as total_completed,(SELECT sum(total_price) FROM ordr where v_id=?  and self_order='true'  and LOWER(stats)='delivered' and payment_status='PAID' ) as total_amount,(SELECT sum(ex_charge) FROM ordr where v_id=?  and self_order='true'  and LOWER(stats)='delivered' and payment_status='PAID' ) as total_delivery_charges,(SELECT sum(tip_amount) FROM ordr where v_id=?  and self_order='true'  and LOWER(stats)='delivered' and payment_status='PAID' ) as total_tip");
        $stmt->bind_param("issississississiiiii", $v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$from_date,$to_date,$v_id,$v_id,$v_id,$v_id,$v_id);
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }



// get delivery account order
      public function get_account_order($v_id,$from_date,$to_date,$type)
    {
       
       if($type=="delivery")
       {
             $stmt = $this->con->prepare("select id,order_id,date_time,collect_date,delivery_collection_time,total_price,payment_typ,stats from ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)=? and payment_status='PAID' and stats='Delivered'");
        $stmt->bind_param("isss", $v_id,$from_date,$to_date,$type);
       }
       else
       {
         $stmt = $this->con->prepare("select id,order_id,date_time,collect_date,delivery_collection_time,total_price,payment_typ from ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and order_type='Takeaway' and payment_status='PAID'");
        $stmt->bind_param("iss", $v_id,$from_date,$to_date);
       }
       
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }

    // get account_deliverd by
      public function get_order_delivered_by_deliveryboy($order_id)
    {
       
        $stmt = $this->con->prepare("SELECT d1.owned_by,v1.business_name FROM ordr o1,job_assign j1,delivery_boy_reg d1,vendor_reg v1 where o1.v_id=v1.id and j1.order_id=o1.id and j1.delivery_boy_id=d1.id   and o1.id=? and LOWER(o1.order_type)='delivery' and LOWER(o1.stats)='delivered'  and o1.payment_status='PAID'");
        $stmt->bind_param("i", $order_id);
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }



// delivered by vendor order data
     public function get_account_order_delivered_vendor($v_id,$from_date,$to_date)
    {
       
        $stmt = $this->con->prepare("SELECT id,order_id,date_time,collect_date,delivery_collection_time,total_price,payment_typ FROM ordr where v_id=? and str_to_date(date_time,'%m/%d/%Y') >= str_to_date(?,'%m/%d/%Y') and str_to_date(date_time,'%m/%d/%Y') <= str_to_date(?,'%m/%d/%Y') and LOWER(order_type)='delivery' and LOWER(stats)='delivered' and payment_status='PAID' and id not in (select order_id from job_assign where job_status!='reject' and job_status!='auto_reject')");
        $stmt->bind_param("iss", $v_id,$from_date,$to_date);
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }

    // get total delivery orders
    /* public function get_total_delivery_orders($v_id)
    {
       
        $stmt = $this->con->prepare("SELECT sum(total_price) as total FROM `ordr` WHERE payment_status='PAID' and order_type='Delivery' and v_id=?");
        $stmt->bind_param("i", $v_id);
        $result = $stmt->execute();
        $delivery = $stmt->get_result();
        $stmt->close();
        return $delivery;
        
    }*/
    // get product size
    public function get_product_size($id)
    {

        $stmt = $this->con->prepare("SELECT * from size_pro WHERE m_id=? ");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }
    //get size toppings
    public function get_size_toppings($mid,$size)
    {
        /*echo "SELECT * from extra_addone WHERE m_id=? and size_name=?";
        echo $mid,$size;*/

        $stmt = $this->con->prepare("SELECT * from extra_addone WHERE m_id=? and size_name=? order by mandatory");
        $stmt->bind_param("is",$mid,$size);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }

    // add item

     public function add_item($v_id,$fc_id,$name,$detail,$main_price,$discount_per,$discount_price,$image,$status,$type)
    {

        $datetime=date('m/d/Y h:i A');
        $operation="Added";
        $user_type="vendor";
        $maxno = $this->getmaxsort_item();
        $sort_no=$maxno["order_id"];
       // echo "INSERT INTO `menu`(`v_id`, `fc_id`, `name`, `detail`, `main_price`, `discount_per`, `discount_price`, `image`, `stats`, `typ`, `added_by`, `date_time`, `operation`, `sort_order`, `user_type`) VALUES ('$v_id','$fc_id','$name','$detail','$main_price','$discount_per','$discount_price','$image','$status','$type','$v_id','$datetime','$operation','$sort_no','$user_type')";
        $stmt = $this->con->prepare("INSERT INTO `menu`(`v_id`, `fc_id`, `name`, `detail`, `main_price`, `discount_per`, `discount_price`, `image`, `stats`, `typ`, `added_by`, `date_time`, `operation`, `sort_order`, `user_type`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("iissssssssissis", $v_id,$fc_id,$name,$detail,$main_price,$discount_per,$discount_price,$image,$status,$type,$v_id,$datetime,$operation,$sort_no,$user_type);
        $result = $stmt->execute();
        $lastId = mysqli_insert_id($this->con);      
        $stmt->close();
        if ($result) {
            return $lastId;
        } else {
            return 0;
        }

    }

    // add item topping
    
    public function add_item_toppings($mid,$size_name,$topping_name,$topping_category,$amount,$status,$type,$vendor_id)
    {

        //echo "UPDATE `extra_addone` SET `v_id`=$vendor_id,`m_id`=$mid,`size_name`='$size_name',`topping_name`='$topping_name',`amount`=$amount,`stats`='$status',`operation`='updated',`mandatory`='$type' WHERE id=$id";
        $operation="added";

        $stmt = $this->con->prepare("INSERT INTO `extra_addone`(`v_id`, `m_id`, `size_name`, `topping_name`, `tp_category`, `amount`,  `stats`, `added_by`,  `operation`, `mandatory`) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("iissississ", $vendor_id,$mid,$size_name,$topping_name,$topping_category,$amount,$status,$vendor_id,$operation,$type);
        $result = $stmt->execute();
        //$rows =$stmt->affected_rows;
        $stmt->close();
       if ($result) {
            return 1    ;
        } else {
            return 0;
        }
    } 

    // add item sub image
    public function add_item_subImage($item_id,$SubImageName)
    {

        
        $stmt = $this->con->prepare("INSERT INTO `item_images`( `item_id`, `image_name`) VALUES (?,?)");
        $stmt->bind_param("is",$item_id,$SubImageName);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 1;
        } else {
            return 0;
        }

    }

    public function add_item_size($mid,$name,$price,$discount_percent,$discount_price,$status,$mandatory,$vendor_id)
    {

        $operation="Added";
        $stmt = $this->con->prepare("INSERT INTO `size_pro`(`v_id`, `m_id`, `size_name`, `price`, `discount_per`, `discount_price`, `stats`, `added_by`, `operation`, `mandatory`) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("iisssssiss",$vendor_id,$mid,$name,$price,$discount_percent,$discount_price,$status,$vendor_id,$operation,$mandatory);
        $result = $stmt->execute();
        
        $stmt->close();
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

     // get topping category list
    public function get_topping_category()
    {
        $stmt = $this->con->prepare("select * from topping_category where stats='Enable'");
        
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }


    // get max no of orders
    public function getmaxorder()
    {

        $stmt = $this->con->prepare("select max(id)+1 as order_id from ordr");
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }

    public function get_vendor_max($vendor_id)
    {
        

        $stmt = $this->con->prepare("select IFNULL(max(srno)+1,1) as vendor_serial from ordr where v_id=?");
        $stmt->bind_param("i",$vendor_id);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $results;
    }

     public function get_vendor_data($v_id)
    {

        $stmt = $this->con->prepare("select v1.*, c1.area_name from vendor_reg v1,area c1 where v1.id=? and v1.area=c1.srno");
        $stmt->bind_param("i", $v_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    //check if cusotmer exists
     public function check_customer($cust_contact)
    {

        $stmt = $this->con->prepare("SELECT * from customer_reg WHERE contact1 = ?");
        $stmt->bind_param("s", $cust_contact);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    //check user address
     public function check_customer_address($cust_id)
    {

        $stmt = $this->con->prepare("SELECT * from addresses WHERE c_id = ?");
        $stmt->bind_param("s", $cust_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    //add user address
     public function add_user_address($cust_id,$house,$street,$area_id,$city_id,$user_lat_long)
    {


        $stmt = $this->con->prepare("INSERT INTO `addresses`( `c_id`,`house_no`, `street`, `area_id`, `city_id`,  `map_location`)values (?,?,?,?,?,?)");
        $stmt->bind_param("issiis", $cust_id,$house,$street,$area_id,$city_id,$user_lat_long);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    //update user address
     public function update_user_address($cust_id,$house,$street,$area_id,$city_id,$user_lat_long,$address_id)
    {


        $stmt = $this->con->prepare("UPDATE `addresses` SET house_no=?,street=?,area_id=?,city_id=?,map_location=? where id=?");
        $stmt->bind_param("ssiisi",$house,$street,$area_id,$city_id,$user_lat_long, $address_id);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    //get cusotmer addresses
     public function get_customer_address($c_id)
    {
        $stmt = $this->con->prepare("SELECT a1.*,a2.area_name,a2.id as pincode,c1.city_name FROM addresses a1,area a2,customer_reg cr1,city c1 where a1.city_id=c1.id and a2.srno=a1.area_id and a1.c_id=? and a1.c_id=cr1.id  and a1.map_location!='' order by a1.id desc");
        $stmt->bind_param("i", $c_id);
        $stmt->execute();
        $cust = $stmt->get_result();
        $stmt->close();
        return $cust;
    }

    //check if area exists
     public function check_area($cust_pincode,$cust_area)
    {
        //"SELECT * from area WHERE id = ? and area_name SOUNDS LIKE ?"
        
        $stmt = $this->con->prepare("SELECT * from area WHERE  area_name SOUNDS LIKE ?");
        $stmt->bind_param("s",$cust_area);
        $stmt->execute();
        $area = $stmt->get_result();
        $stmt->close();
        return $area;
    }

    //get delivery data
     public function get_delivery_settings()
    {

        $stmt = $this->con->prepare("SELECT `id`, `minimum_delivery_kilometer`, `minimum_delivery_charge`, `per_kilometer_charges`, `percentage`as commission,max_radius,delivery_percentage   from delivery_settings");
        
        $stmt->execute();
        $delivery = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $delivery;
    }

    // add customer

     public function add_customer( $fname,  $contact1, $model,$os_version)
    {
        $date_time = date("m/d/Y h:i A");
        $operation = 'Added';        
        
        $status = 'unverified';
        $type='external user';

        $stmt = $this->con->prepare("INSERT INTO `customer_reg`(`firstname`,`contact1`,`status`,`date_time`, `operation`,`type`,`model`,`os_version`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssss",$fname,  $contact1,$status,$date_time,$operation,$type,$model,$os_version);
        $result = $stmt->execute();
        $lastId = mysqli_insert_id($this->con);
        $stmt->close();
        return $lastId;
    }

    // add area
    public function add_area( $pincode,  $area, $city_id,$vendor_id)
    {
        $date_time = date("m/d/Y h:i A");
        $operation = 'Added';        
        $stats='Enable';
        $is_verified='unverified';
       
      // echo "INSERT INTO `area`(`id`, `area_name`, `city`, `stats`, `added_by`, `date_time`, `operation`) VALUES ($pincode,  $area, $city_id,$stats,$vendor_id,$date_time,$operation)";
        $stmt = $this->con->prepare("INSERT INTO `area`(`id`, `area_name`, `city`, `stats`, `added_by`, `date_time`, `operation`,`is_verified`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssisisss",$pincode,  $area, $city_id,$stats,$vendor_id,$date_time,$operation,$is_verified);
        $result = $stmt->execute();
        $lastId = mysqli_insert_id($this->con);
        $stmt->close();
        return $lastId;
    }

    // add order
    public function new_Order($order_id, $cust_id, $vendor_id, $tot_price, $delivery_charge, $dis_price, $detail, $coll_date, $pay_type, $pay_status, $del_coll_time, $reason, $status, $order_from, $order_type, $datetime, $operation, $del_addr, $del_instr, $per, $ip_addr,$sub_total,$external_free_from,$cust_lat_long,$distance,$delivery_per,$delivery_boy_amount,$myct_delivery_amount,$vendor_delivery_amount,$user_delivery_amount,$vendor_serial_no,$myct_commission,$srno)
    {

        $tip_per="0.00";
        $tip_amount="0.00";
        $extra_charge_per="0.00";
        $extra_charge_amount="0.00";
        $couponcode="";
        $coupondiscount="0.00";
        $coupondiscount_per="0.00";
        $coupon_max_discount="0.00";
        $coupon_min_amount="0.00";
        $self_order="true";
        if($pay_status=="PAID")
        {
            $paid_by="vendor";
        }
        else
        {
            $paid_by="";
        }
        $stmt = $this->con->prepare("INSERT INTO ordr(`order_id`,`srno`,`vendor_serial_num`,`customer_id`,`v_id`, `total_price`, `ex_charge`,`dis_price`,`detail`, `collect_date`, `payment_typ`, `payment_status`, `delivery_collection_time`, `reason`, `stats`, `order_from`, `order_type`, `added_by`, `date_time`,`operation`,`delivery_address`,`delivery_instruction`,`percentage`,`ip_address`,`tip_per`, `tip_amount`, `extra_charge_per`, `extra_charge_amount`, `couponcode`, `coupondiscount`, `coupon_discount_per`, `max_discount`, `min_amount`,`self_order`,`sub_total`,`external_free_from`,`paid_by`,`map_location`,`distance`,`delivery_percentage`,`delivery_boy_amount`,`myct_delivery_amount`,`vendor_delivery_amount`,`user_delivery_charge`,`myct_commission`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sisiissssssssssssissssssddddsddddssssssssssss", $order_id,$srno,$vendor_serial_no, $cust_id, $vendor_id, $tot_price, $delivery_charge, $dis_price, $detail, $coll_date, $pay_type, $pay_status, $del_coll_time, $reason, $status, $order_from, $order_type, $cust_id, $datetime, $operation, $del_addr, $del_instr, $per, $ip_addr,$tip_per,$tip_amount,$extra_charge_per,$extra_charge_amount,$couponcode,$coupondiscount,$coupondiscount_per,$coupon_max_discount,$coupon_min_amount,$self_order,$sub_total,$external_free_from,$paid_by,$cust_lat_long,$distance,$delivery_per,$delivery_boy_amount,$myct_delivery_amount,$vendor_delivery_amount,$user_delivery_amount,$myct_commission);
        $result = $stmt->execute();
        $lastId = mysqli_insert_id($this->con);
        $stmt->close();

        if ($result) {
            return $lastId;

        } else {
            return 0;
        }
    }

    // add order ready
    public function add_order_ready($order_id,$status)
    {
        $datetime=date("m/d/Y h:i A");
        
        $stmt = $this->con->prepare("INSERT INTO `order_ready`( `order_id`, `is_order_ready`, `date_time`) VALUES (?,?,?)");
        $stmt->bind_param("iss",$order_id,$status,$datetime);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // get delivery boy list
    public function get_delivery_boy($o_id)
    {
        $stmt = $this->con->prepare("SELECT db.id,db.name,s1.name as owner_name,db.status FROM delivery_boy_reg db, subadmin s1,ordr o1,vendor_reg v1 where db.owner_id=s1.id and o1.v_id=v1.id and db.owned_by='admin' and status='Enable' and o1.id=? AND v1.city=db.city ");
        $stmt->bind_param("i",$o_id);
        $result = $stmt->execute();
        $list = $stmt->get_result();
        $stmt->close();
        return $list;
        
    }

    // get order ready status
     public function get_order_ready_status($o_id)
    {

        
        $stmt = $this->con->prepare("SELECT * FROM order_ready WHERE order_id =?  order by id desc limit 1");
        $stmt->bind_param("s", $o_id);
        $stmt->execute();
        $result=$stmt->get_result()->fetch_assoc();
        
        $stmt->close();
        return $result;

    }

   
    private function add_transaction($orderid, $txnamount, $gatewayname,$paymentIntentid,$payment_response,$status,$userid)
    {

        $txndate=date('m/d/Y');
        $stmt = $this->con->prepare("INSERT INTO `transaction`(`order_id`, `user_id`, `total_amount`, `transaction_type`,`paymentIntentid`,`payment_response`,`status`,`t_date`) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sissssss", $orderid, $userid, $txnamount, $gatewayname,$paymentIntentid,$payment_response,$status,$txndate);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }

    }
    private function insert_domail($cid,$vid,$oid,$order_status,$status)
    {
        $datetime=date("m/d/Y h:i A");
        $stmt = $this->con->prepare("insert into domail (`customerid`, `vendorid`, `orderid`, `type`, `status`, `datetime`) values(?,?,?,?,?,?)");
        $stmt->bind_param("iiisss",$cid,$vid,$oid,$order_status,$status,$datetime);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
    }

    // get max no of orders
    private function getmaxsort_item()
    {

        $stmt = $this->con->prepare("select max(sort_order)+1 as order_id from menu");
        $stmt->execute();
        $results = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $results;
    }

    // get sort no for item category
    private function getmaxsort_item_category()
    {

        $stmt = $this->con->prepare("select max(sort_order)+1 as sort_no from food_category");
        $stmt->execute();
        $results = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $results;
    }

}
    
