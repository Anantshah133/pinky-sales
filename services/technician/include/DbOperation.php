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
    //generate a unique api key
    function generateApiKey(){
        return md5(uniqid(rand(), true));
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

  public function insert_technician_device($vid,$token,$type,$api_key)
    {
       

       
         
        $stmt = $this->con->prepare("INSERT INTO `technician_device`(`uid`, `token`, `type`,`api_key`) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $vid, $token, $type,$api_key);
       
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
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.ctnm as service_area, s2.name as service_type_name , t1.name as technician_name FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,service_type s2,city a1, technician t1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.srno and s2.id = c1.service_type and t1.id = c2.technician and c2.status='pending' and c2.technician=?");
            $stmt->bind_param("i",$technician_id);
        }
        else if($type=="allocated")
        {
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.ctnm as service_area, s2.name as service_type_name , t1.name as technician_name FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,service_type s2,city a1, technician t1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.srno and s2.id = c1.service_type and t1.id = c2.technician and c2.status='allocated'  and c2.technician=? ");
            
            $stmt->bind_param("i", $technician_id);
        }
        else if($type=="closed")
        {
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.ctnm as service_area, s2.name as service_type_name , t1.name as technician_name FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,service_type s2,city a1, technician t1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.srno and s2.id = c1.service_type and t1.id = c2.technician and c2.status=?   and c2.allocation_date >='".date("Y-m-01")."' and c2.allocation_date <='".date("Y-m-t")."' and c2.technician=?");
            $stmt->bind_param("si", $type,$technician_id);
        }
        else if($type=="cancelled")
        {
            
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.ctnm as service_area FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,city a1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.srno and  c2.status=?   and c2.allocation_date >='".date("Y-m-01")."' and c2.allocation_date <='".date("Y-m-t")."' and c2.technician=?");
            $stmt->bind_param("si", $type,$technician_id);
        }
        else
        {
            
            $stmt = $this->con->prepare("SELECT c1.*,c2.*,s1.name as service_center_name,pc1.name as product_category_name,s1.address as service_center_address,s1.contact as service_contact,a1.ctnm as service_area, s2.name as service_type_name , t1.name as technician_name FROM customer_reg c1,call_allocation c2,service_center s1,product_category pc1,service_type s2,city a1, technician t1 WHERE c2.complaint_no=c1.complaint_no and c2.service_center_id=s1.id and c1.product_category=pc1.id and s1.area=a1.srno and s2.id = c1.service_type and t1.id = c2.technician and c2.allocation_date <= '".$date."' and c2.technician=? and c2.complaint_no not in (select complaint_no from call_history )");
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

    //Checking the admin is valid or not by api key
    public function isValidTechnician($api_key) {
        $stmt = $this->con->prepare("SELECT uid from technician_device WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }




}
    
