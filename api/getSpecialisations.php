<?php
if (isset($_POST['course']) && isset($_POST['year'])){
    //call the function or execute the code
	require '../config.php';
	$course = $_POST['course'];
	$year = $_POST['year'];
	$studyplantableName = "studyplans";
	
	$sql = "SELECT * FROM $studyplantableName WHERE Course = '". $course ."' AND FileYear ='".$year."' GROUP by Specialisation";
	$query = $conn->prepare($sql);
	$query->execute();
	$result = $query;
	
	$array = array();
	foreach ($result as $row){
		$array[] = $row["Specialisation"];
	}
	echo json_encode($array);
}
?>