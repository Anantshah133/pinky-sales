<?php
	include "../db_connect.php";
	$obj = new DB_Connect();
    $name = $_REQUEST["name"];
    $id=$_REQUEST['sid'];
    $stmt = $obj->con1->prepare("SELECT count(*) as tot FROM  `service_type` WHERE soundex(name)=soundex(?) AND id!=?");
    $stmt->bind_param("si", $name,$id);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    echo $data["tot"];
    $stmt->close();
?>