<?php
ob_start();
include "../db_connect.php";
$obj = new DB_Connect();
error_reporting(0);
session_start();

if ($_REQUEST['action'] == "get_notification") {
    if(isset($_SESSION['type_admin'])){
        $stmt = $obj->con1->prepare("SELECT n1.*, CONCAT(c1.date, ' ', c1.time) AS date_time, ca1.id AS ca_id FROM notification n1, customer_reg c1, call_allocation ca1 WHERE n1.admin_status='1' AND n1.complaint_no=c1.complaint_no AND n1.complaint_no=ca1.complaint_no ORDER BY id DESC");
        $stmt->execute();
        $Resp = $stmt->get_result();
        $stmt->close();
    } else {
        $center_id = $_SESSION['scid'];
        $stmt = $obj->con1->prepare("SELECT n1.*, CONCAT(c1.date, ' ', c1.time) AS date_time, ca1.id AS ca_id FROM notification n1, customer_reg c1, call_allocation ca1 WHERE n1.service_status='1' AND n1.complaint_no=c1.complaint_no AND n1.complaint_no=ca1.complaint_no and ca1.service_center_id=? ORDER BY id DESC");
        $stmt->bind_param("i", $center_id);
        $stmt->execute();
        $Resp = $stmt->get_result();
        $stmt->close();
    }

    $num = mysqli_num_rows($Resp);
    $notifications = array();

    while ($data = mysqli_fetch_array($Resp)) {
        $notification = array(
            "id" =>  $data['id'],
            "message" => "<a href='javascript:viewNotificationRecord(" . trim($data['ca_id']) .", " . trim($data['id']) .", `add_call_allocation.php`)'>" . "<strong>" . $data['complaint_no'] . "</strong>" . " - " . $data['msg'] . "for " . $data['type'] . " " . date("d-m-y h:i A", strtotime($data['date_time'])) . "</a>"
        );
        array_push($notifications, $notification);
    }

    echo json_encode($notifications);
}
if ($_REQUEST['action'] == "play_noti_sound") {
    if (isset($_SESSION['type_admin'])) {
        $stmt = $obj->con1->prepare("SELECT * FROM `notification` WHERE admin_play_status='1' ORDER BY id DESC");
        $stmt->execute();
        $Resp = $stmt->get_result();
        $stmt->close();
    } else {
        $center_id = $_SESSION['scid'];
        $stmt = $obj->con1->prepare("SELECT n1.* FROM notification n1,call_allocation c1 where n1.complaint_no=c1.complaint_no and c1.service_center_id=? and n1.service_play_status='1' order by n1.id desc");
        $stmt->bind_param("i", $center_id);
        $stmt->execute();
        $Resp = $stmt->get_result();
        $stmt->close();
    }
    
    $ids = "";
    $num = mysqli_num_rows($Resp);
    
    while ($row = mysqli_fetch_array($Resp)) {
        $ids .= $row['id'] . ",";
    }
    echo $num . "@@@" . rtrim($ids, ",");
}
if ($_REQUEST['action'] == "remove_noti_sound") {
    $ids = explode(",", $_REQUEST['ids']);
    if (isset($_SESSION['type_admin'])) {
        for ($i = 0; $i < sizeof($ids); $i++) {
            $stmt = $obj->con1->prepare("UPDATE `notification` SET admin_play_status=0 WHERE id=?");
            $stmt->bind_param("i", $ids[$i]);
            $Res = $stmt->execute();
            $stmt->close();
        }
    } else {
        for ($i = 0; $i < sizeof($ids); $i++) {
            $stmt = $obj->con1->prepare("UPDATE `notification` SET service_play_status=0 WHERE id=?");
            $stmt->bind_param("i", $ids[$i]);
            $Res = $stmt->execute();
            $stmt->close();
        }
    }
    echo $Res ? "Ok" : "Error";
}
if($_REQUEST['action'] == "remove_notification"){
    $id = $_REQUEST['id'];
    if(isset($_SESSION['type_admin'])){
        $stmt = $obj->con1->prepare("UPDATE notification SET admin_status=0, admin_play_status=0 WHERE id=?");
        $stmt->bind_param("i", $id);
        $Res = $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $obj->con1->prepare("UPDATE notification SET service_status=0, service_play_status=0 WHERE id=?");
        $stmt->bind_param("i", $id);
        $Res = $stmt->execute();
        $stmt->close();
    }
    if($Res){
        echo 1;
    } else {
        echo 0;
    }
}
if($_REQUEST['action'] == "read_all_notification"){
    $scid = $_REQUEST['centerId'];
    if(isset($_SESSION['type_admin'])){
        $stmt = $obj->con1->prepare("UPDATE notification SET admin_status=0, admin_play_status=0");
        $stmt->bind_param("i", $id);
        $Res = $stmt->execute();
        $stmt->close();
    } else {
        $scid = $_SESSION['scid'];
        $stmt = $obj->con1->prepare("UPDATE notification n1, call_allocation c1 SET service_status=0, service_play_status=0 WHERE n1.complaint_no=c1.complaint_no AND c1.service_center_id=?");
        $stmt->bind_param("i", $scid);
        $Res = $stmt->execute();
        $stmt->close();
    }
    if($Res){
        echo 1;
    } else {
        echo 0;
    }
}
?>