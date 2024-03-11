<?php
// ob_start();
// include "../db_connect.php";
// $obj = new DB_Connect();
// error_reporting(0);
// session_start();
// echo "--------";
// if ($_REQUEST['action'] == "get_notification") {
    // $center_id = isset($_SESSION['type_center']);
//     $stmt = $obj->con1->prepare("SELECT * FROM `notification` WHERE admin_status='1' ORDER BY id DESC");
//     $stmt->execute();
//     $Resp = $stmt->get_result();
//     $num = mysqli_num_rows($Resp);

//     $id = 0;
//     $noti_obj = "";

//     while($data = mysqli_fetch_array($Resp)){
//         $id++;
//         $noti_obj .= "{id: $id, message: '".$data['complaint_no']. " - " .$data['msg']."},";
//     }

//     echo $noti_obj;
// }
?>

<?php
ob_start();
include "../db_connect.php";
$obj = new DB_Connect();
error_reporting(0);
session_start();

if ($_REQUEST['action'] == "get_notification") {
    $stmt = $obj->con1->prepare("SELECT * FROM `notification` WHERE admin_status='1' ORDER BY id DESC");
    $stmt->execute();
    $Resp = $stmt->get_result();
    $num = mysqli_num_rows($Resp);

    $notifications = array();

    while($data = mysqli_fetch_array($Resp)){
        $notification = array(
            "id" => $data['id'],
            "message" => $data['complaint_no'] . " - " . $data['msg']
        );
        array_push($notifications, $notification);
    }

    echo json_encode($notifications);
}
?>
