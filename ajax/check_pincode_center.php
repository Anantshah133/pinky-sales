<?php 
    include "../db_connect.php";
    $obj = new DB_Connect();
    $pincode = $_REQUEST["pincode"];
    $city_id = $_REQUEST["cityId"];
    $stmt = $obj->con1->prepare("SELECT COUNT(*) AS num FROM `area_pincode` WHERE pincode=? AND city_id=?");
    $stmt->bind_param("si", $pincode, $city_id);
    $stmt->execute();
    $Res = $stmt->get_result();
    $data = $Res->fetch_assoc();
    echo $data["num"];
    $stmt->close();
?>