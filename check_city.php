<?php
	include "db_connect.php";
	$obj = new DB_Connect();
    $city_name = $_REQUEST["city_name"];
    $state_id = $_REQUEST["state_id"];
    $stmt = $obj->con1->prepare("SELECT count(*) as tot FROM `city` WHERE soundex(ctnm)=soundex(?) And state_id=?");
    $stmt->bind_param("ss",$city_name, $state_id );
    $stmt->execute();
    $Resp = $stmt->get_result();
    $data = $Resp->fetch_assoc();
    echo $data["tot"];
   $stmt->close();
?>