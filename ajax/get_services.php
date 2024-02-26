<?php 
	include "../db_connect.php";
	$obj = new DB_Connect();
    $pid = $_REQUEST["pid"];
    $stmt = $obj->con1->prepare("SELECT ps1.*,p1.name as product,s1.name as service FROM `product_service` ps1, `product_category` p1,`service_type` s1 WHERE ps1.pid=p1.id AND ps1.sid=s1.id AND p1.id=?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $Resp = $stmt->get_result();
?>
<option value="">Choose Service Type</option>
<?php 
while ($row = mysqli_fetch_assoc($Resp)) { 
?>
    <option value="<?php echo $row["srno"]; ?>"><?php echo $row["service"]; ?></option>
<?php
    }
?>