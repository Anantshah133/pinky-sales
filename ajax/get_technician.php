<?php 
	include "../db_connect.php";
    $center_id = $_REQUEST["scid"];
    
    $stmt = $obj->con1->prepare("SELECT * FROM technician WHERE service_center=?");
    $stmt->bind_param("i", $center_id);
    $stmt->execute();
    $Resp = $stmt->get_result();
?>

<option value="">Choose Technician</option>

<?php 
while ($row = mysqli_fetch_assoc($Resp)) { 
?>
    <option value="<?php echo $row["id"]; ?>"  >
		<?php echo $row["name"]; ?>
	</option>
<?php
    }
?>


<?php 
// echo isset($ctid) && $ctid == $row['id'] ? 'selected' : '' 
?>