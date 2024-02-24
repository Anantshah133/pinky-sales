<?php 
	include "../db_connect.php";
	$obj = new DB_Connect();
    $center_id = $_REQUEST["scid"];
    $tech_id = $_REQUEST["tid"];
    $stmt = $obj->con1->prepare("SELECT * FROM technician WHERE service_center=?");
    $stmt->bind_param("i", $center_id);
    $stmt->execute();
    $Resp = $stmt->get_result();
?>

<option value="">Choose Technician</option>

<?php 
while ($row = mysqli_fetch_assoc($Resp)) { 
?>
    <option value="<?php echo $row["id"]; ?>" <?php echo isset($tech_id) && $tech_id == $row["id"] ? 'selected' : '' ?>><?php echo $row["name"]; ?></option>
<?php
    }
?>
