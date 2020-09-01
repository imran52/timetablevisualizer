<?php
if (isset($_POST['getCalYears'])){
    //call the function or execute the code
	require '../config.php';
	$studyplantableName = "studyplans";
	$year = $_POST['getCalYears'];
	
	$sql = "SELECT * FROM $studyplantableName GROUP by FileYear ORDER BY FileYear DESC";
	$query = $conn->prepare($sql);
	$query->execute();
	$result = $query;
	//echo "Anything";
	$array = array();
	
	foreach ($result as $row){
		$array[] = $row["FileYear"];
		//echo $row["Year"];
	}
	echo json_encode($array);
	
}

?>