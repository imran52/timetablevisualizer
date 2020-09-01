<?php
if (isset($_POST['specialisation'])){
    //call the function or execute the code
	require '../config.php';
	$year = $_POST['year'];
	$course = $_POST['course'];
	$specialisation = $_POST['specialisation'];
	
	$sql = "SELECT * FROM $studyplans_table_name WHERE Specialisation = '". $specialisation ."' AND Course = '". $course ."' AND FileYear ='".$year."' GROUP by Intake";
	$query = $conn->prepare($sql);
	$query->execute();
	$result = $query;
	//echo "Anything";
	$array = array();
	
	$array = array();
	foreach ($result as $row){
		$array[] = $row["Intake"];
//		$array["Year"] = $row["Year"];
//		$array["Semester"] = $row["Semester"];
	}
	echo json_encode($array);
	//echo $array;
}
?>