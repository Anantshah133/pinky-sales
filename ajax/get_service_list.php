<?php
	include "../db_connect.php";
	$obj = new DB_Connect();
    $product_id = $_REQUEST["product_id"];

    $html="";

    $stmt = $obj->con1->prepare("SELECT * FROM `service_type` WHERE status='enable' and id not in (select sid from product_service where pid=?)");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $Resp = $stmt->get_result();
    $stmt->close();

    $html.='<option value="">Choose Service</option>';
    while($result = mysqli_fetch_array($Resp)){
        $html.='<option value="'.$result['id'].'">'.$result['name'].'</option>';
    }

    echo $html;
?>