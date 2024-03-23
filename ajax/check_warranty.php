<?php 
    include "../db_connect.php";
	$obj = new DB_Connect();
    $selected_date = date("Y-m-d", strtotime($_REQUEST['date']));
    $barcode = $_REQUEST['barcode'];
    // $service_type = $_REQUEST['service_type'];
    $service_type = 23;
    $product_category = $_REQUEST['product_category'];

    if(trim($barcode) == "" || trim($product_category) == "") {
        exit();
    };

    $stmt = $obj->con1->prepare("SELECT * FROM customer_reg WHERE barcode=? AND service_type=? AND product_category=?");
    $stmt->bind_param("sii", $barcode, $service_type, $product_category);
    $stmt->execute();
    $Res = $stmt->get_result();
    $data = $Res->fetch_assoc();    
    $stmt->close();

    if($Res->num_rows >= 1){
        $pr_category = $data['product_category'];
        $stmt = $obj->con1->prepare("SELECT * FROM product_category WHERE id=?");
        $stmt->bind_param("i", $pr_category);
        $stmt->execute();
        $Res = $stmt->get_result();
        $pr_data = $Res->fetch_assoc();
        $stmt->close();

        $old_date = strtotime($data['date']);
        $check_date = strtotime($selected_date);
        $warranty_period = $pr_data['warranty_period'];

        $difference = $check_date - $old_date;
        $warranty_duration = $warranty_period * 30 * 24 * 60 * 60;

        if($difference <= $warranty_duration){
            echo "in-warranty";
        } else {
            echo "not-in-warranty";
        }
    } else {
        echo "new-entry";
    }
?>