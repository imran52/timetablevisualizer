<?php
if (isset($_POST['getSemestersByYearCourse'])){
	require '../config.php';
	
	$sql = "SELECT * FROM $studyplans_table_name
	GROUP by Semester";
	//BY Year,Semester,Intake,id ASC
	$query = $conn->prepare($sql);
	$query->execute();
	$result = $query;
	//echo "Anything";
	$array = array();
	
	$i=0;
	foreach ($result as $row){
		$array[]["Semester"] = $row["Semester"];
		$i++;
	}
	echo json_encode($array);

}
?>