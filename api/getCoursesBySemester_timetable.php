<?php 
if (isset($_POST["getCoursesByYear"])){
require_once "../config.php";
$semester = $_POST['semester'];
$sql = "SELECT * FROM $studyplans_table_name WHERE Semester='".$semester."' GROUP by Course ORDER BY Course ASC";
$query = $conn->prepare($sql);
$query->execute();
$result = $query;

$array = array();
foreach ($result as $row){
	$array[] = $row["Course"];
}
echo json_encode($array);
}

?>