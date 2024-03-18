<?php 
    include "../db_connect.php";
	$obj = new DB_Connect();
    $date = date("Y-m-d", strtotime($_REQUEST['date']));
    $barcode = $_REQUEST['barcode'];
    $service_type = $_REQUEST['service_type'];
    $product_category = $_REQUEST['product_category'];

    if(trim($barcode) == "") {
        exit();
    };

    $stmt = $obj->con1->prepare("SELECT * FROM customer_reg WHERE barcode=? AND service_type=? AND product_category=?");
    $stmt->bind_param("sii", $barcode, $service_type, $product_category);
    $stmt->execute();
    $Res = $stmt->get_result();
    $data = $Res->fetch_assoc();
    $stmt->close();

    if(isset($data['date'])){
        $start_date = new DateTime($date);
        $end_date = new DateTime($data['date']);
        $diff = $start_date->diff($end_date);
        $days_difference = $diff->days;
        echo $days_difference;
    } else {
        echo "new-entry";
    }
?>