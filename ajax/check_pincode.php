<?php
	include "../db_connect.php";
	$obj = new DB_Connect();
    $pincode=$_REQUEST["pincode"];
    $stmt = $obj->con1->prepare("SELECT count(*) as tot FROM `area_pincode` WHERE pincode=?");
    $stmt->bind_param("s", $pincode);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    echo $data["tot"];
    $stmt->close();
?>