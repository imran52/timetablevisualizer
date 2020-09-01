<?php 
if (isset($_POST["getUnitFiles"])){
require_once "../config.php";

$sql = "SELECT * FROM $units_table_name GROUP by FileYear ORDER BY FileYear DESC";
$query = $conn->prepare($sql);
$query->execute();
$result = $query;

$array = array();
$i=0;
foreach ($result as $row){
	$array[$i]["FileYear"] = $row["FileYear"];
	$array[$i]["FileName"] = $row["FileName"];
	$array[$i]["DateUploaded"] = $row["DateUploaded"];
	$i++;
}
echo json_encode($array);
}

?>