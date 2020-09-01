<?php
if (isset($_POST['specialisation'])){
	require '../config.php';
	$year = $_POST['year'];
	$intake = $_POST['intake'];
	$course = $_POST['course'];
	$specialisation = $_POST['specialisation'];
	
	$sql = "SELECT * FROM $studyplans_table_name WHERE 
	Specialisation = '". $specialisation ."' AND 
	Course = '". $course ."' AND 
	FileYear ='".$year."' AND 
	Intake = '".$intake."' 
	ORDER BY id ASC";
	//BY Year,Semester,Intake,id ASC
	$query = $conn->prepare($sql);
	$query->execute();
	$result = $query;
	//echo "Anything";
	$array = array();
	
	$i=0;
	foreach ($result as $row){
		$unitTitle = $row["UnitTitle"];
		$unitCode = $row["UnitCode"];
		$yearno = $row["Year"];
		
		$array[$i]["UnitTitle"]= $unitTitle;
		$array[$i]["UnitCode"] = $unitCode;
		$array[$i]["YearNo"] = $yearno;
		$array[$i]["Semester"] = $row["Semester"];
		$i++;
	}
	echo json_encode($array);

}
?>