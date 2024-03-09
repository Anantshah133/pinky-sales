<?php
ob_start();
include "../db_connect.php";
$obj = new DB_Connect();
error_reporting(0);
session_start();

if ($_REQUEST['action'] == "get_notification") {
    $center_id = isset($_SESSION['type_center']);
    $stmt = $obj->con1->prepare("SELECT * FROM `notification` WHERE admin_status='1' ORDER BY id DESC");
    $stmt->execute();
    $Resp = $stmt->get_result();
}
?>
