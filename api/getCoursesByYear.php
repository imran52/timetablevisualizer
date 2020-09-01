<?php 
if (isset($_POST["getCoursesByYear"])){
require_once "../config.php";
$year = $_POST['givenYear'];
$sql = "SELECT * FROM $studyplans_table_name WHERE FileYear = '".$year."' GROUP by Course,FileYear ORDER BY Course ASC";
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