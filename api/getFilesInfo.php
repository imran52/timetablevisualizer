<?php 
if (isset($_POST["getAllCourses"])){
require_once "../config.php";

$sql = "SELECT * FROM $studyplans_table_name GROUP by Course,FileYear ORDER BY FileYear DESC";
$query = $conn->prepare($sql);
$query->execute();
$result = $query;

$array = array();
$i=0;
foreach ($result as $row){
	//$array[] = $row["Course"]."(".$row["FileYear"].")";
	$array[$i]["Course"] = $row["Course"];
	$array[$i]["FileYear"] = $row["FileYear"];
	$array[$i]["FileName"] = $row["FileName"];
	$array[$i]["DateUploaded"] = $row["DateUploaded"];
	$i++;
}
echo json_encode($array);
}

?>