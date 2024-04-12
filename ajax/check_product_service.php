<?php
	include "../db_connect.php";
	$obj = new DB_Connect();
    $product_id = $_REQUEST["product_id"];
    $service_id = $_REQUEST["service_id"];
    $pid = $_REQUEST["pid"];

    $stmt = $obj->con1->prepare("SELECT count(*) as count FROM product_service where pid=? and sid=? and srno!=?");
    $stmt->bind_param("iii", $product_id, $service_id, $pid);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    echo $data["count"];
    $stmt->close();
?>