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


     public function getUser($contact)
    {
        $stmt = $this->con->prepare("SELECT * FROM customer_reg WHERE contact=?");
        $stmt->bind_param("s", $contact);
        $stmt->execute();
        $faculty = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $faculty;
    }

    public function getmaxcustomer()
    {

        $stmt = $this->con->prepare("select IFNULL(max(id)+1,1) as customer_id from customer_reg");
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }
    public function get_dailycounter()
    {

      
        $stmt = $this->con->prepare("select IFNULL(count(id)+1,1) as customer_id from customer_reg where  DATE(date) ='".date("Y-m-d")."'");
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }
    
    public function do_reg_customer($fname, $lname,$email, $contact,$alternate_contact, $area,$zipcode,$address,$service_type,$product_category,$dealer_name,$complaint_no,$description,$barcode,$source,$map_location)
    {
            $date=date("Y-m-d");
            $time=date("h:i A");

            //if (!$this->isContactExists($contact)) {

     

                $stmt = $this->con->prepare("INSERT INTO `customer_reg`(`fname`, `lname`, `email`,`contact`,`alternate_contact`, `area`,`map_location`, `address`,`zipcode`, `complaint_no`, `service_type`, `product_category`,  `dealer_name`,`description`,`barcode`,`source`,`date`,`time`)  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param("sssssisssssissssss", $fname, $lname,$email, $contact,$alternate_contact, $area,$map_location,$address,$zipcode,$complaint_no,$service_type,$product_category,$dealer_name,$description,$barcode,$source,$date,$time);
                $result = $stmt->execute();
                $stmt->close();
                if ($result) {
                    return 0;
                } else {
                    return 1;
                }
            /*} else {
                // check if external user
                $res_phone = $this->fetch_contact($contact);
                $user=$res_phone->fetch_assoc();
                if($user["type"]=="external user")
                {
                    $date_time = date("m/d/Y h:i A");
                    $operation = 'Updated';
                    


        $stmt = $this->con->prepare("UPDATE `customer_reg` set `firstname` = ? , `lastname` = ?,`email`=? , `date_time` = ? , `operation` = ?  where `id` = ? ");
        $stmt->bind_param("sssssi", $fname, $lname,$email,  $date_time, $operation,  $user["id"]);
        $result = $stmt->execute();
        $stmt->close();
                }
                else
                {
                    return 3;
                }

                
            }*/

    }

    // get service center
    public function get_service_center($area)
    {
       
        $stmt = $this->con->prepare("select * from service_center where area=?");
        $stmt->bind_param("i",$area);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $results;
    }

    // get service center
    public function get_area($zipcode)
    {

        $stmt = $this->con->prepare("select * from area_pincode where pincode=? ");
        $stmt->bind_param("s",$zipcode);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }


    

    // track complaint
    public function track_complaint($complaint_no)
    {
       
        $stmt = $this->con->prepare("SELECT c1.*,s1.name as service_type_name,p1.name as product_category_name FROM `customer_reg` c1,service_type s1,product_category p1 where c1.service_type=s1.id and c1.product_category=p1.id and ( c1.contact=? or c1.alternate_contact=? or c1.complaint_no=?) order by id desc");
        $stmt->bind_param("sss",$complaint_no,$complaint_no,$complaint_no);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }
    // get call_details
    public function call_detail($complaint_no)
    {

        
        $stmt = $this->con->prepare("select c1.*,t1.name as technician_name,t1.contact as technician_contact from call_allocation c1,technician t1 where c1.technician=t1.id and c1.complaint_no=?");
        $stmt->bind_param("s",$complaint_no);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $results;
    }

    

    public function get_product_category()
    {

        $stmt = $this->con->prepare("select * from product_category");

        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }
    

    public function get_privacy()
    {
        $stmt = $this->con->prepare("select * from privacy_policy where `type`='User'");
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }

    // call allocation add
    public function call_allocation_add($complaint_no,$service_center_id, $product_serial_no, $product_model, $purchase_date, $techinician,$allocation_date,$allocation_time,$status)
    {

       
        $stmt = $this->con->prepare("INSERT INTO `call_allocation`( `complaint_no`, `service_center_id`, `product_serial_no`, `product_model`, `purchase_date`, `technician`, `allocation_date`, `allocation_time`, `status`) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sisssisss",$complaint_no,$service_center_id, $product_serial_no, $product_model, $purchase_date, $techinician,$allocation_date,$allocation_time,$status);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
            

    }
    public function product_service($product_category)
    {

        $stmt = $this->con->prepare("select p1.*,p2.name as product_category,s1.name as service_type from product_service p1,product_category p2,service_type s1 where p1.pid=p2.id and p1.sid=s1.id and p1.pid=? and p1.status='enable' ");
        $stmt->bind_param("i",$product_category);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
        return $results;
    }

    public function send_mail($complaint_no)
    {


        $stmt = $this->con->prepare("INSERT INTO `send_mail`(`complaint_no`) VALUES  (?)");
        $stmt->bind_param("s",$complaint_no);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return 0;
        } else {
            return 1;
        }
            

    }

    public function get_service_center_device_android($service_center)
    {

        
        $stmt = $this->con->prepare("select * from service_center_device where uid=? and type='android' group by token");
        $stmt->bind_param("i", $service_center);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }

    public function get_admin_device_android()
    {

        
        $stmt = $this->con->prepare("select * from admin_device where  type='android' group by token");
        
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
    public function check_user_device($contact,$type)
    {

        
        $stmt = $this->con->prepare("select * from user_device where user_contact=? and type=? ");
        $stmt->bind_param("ss",$contact,$type);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
        return $states;
    }
    public function update_device($contact,$device_token,$type)
    {

        
        $stmt = $this->con->prepare("UPDATE `user_device` SET `token`=? ");
        $stmt->bind_param("i", $service_center);
        $stmt->execute();
        
        $stmt->close();
        //return $states;
    }
    public function insert_device($contact,$device_token,$type)
    {

        $datetime=date("Y-m-d H:i:s");
        $stmt = $this->con->prepare("INSERT INTO `user_device`( `user_contact`, `token`, `date_time`, `type`) values (?,?,?,?)");
        $stmt->bind_param("ssss",$contact,$device_token,$datetime,$type);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
      //  return $states;
    }
    public function add_notification($complaint_no,$typ,$msg)
    {
        $status=1;
        $datetime=date("Y-m-d H:i:s");
        $stmt = $this->con->prepare("INSERT INTO `notification`(`complaint_no`, `type`,`msg`, `admin_status`, `service_status`) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss",$complaint_no,$typ,$msg,$status,$status);
        $stmt->execute();
        $states = $stmt->get_result();
        $stmt->close();
    }

   

}
	
