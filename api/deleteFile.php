<?php 
if (isset($_POST["deleteFile"])){
require_once "../config.php";

$filename = $_POST["file"];
$year = $_POST["year"];
$date = $_POST["date"];
$type = $_POST["type"];
if ($type == "studyplan"){
	$tablename = $studyplans_table_name;
}else if ($type == "timetable"){
	$tablename = $units_table_name;
}else{
	die("Invalid file type to delete.");
}
$sql = "DELETE FROM $tablename WHERE FileYear = '".$year."' AND FileName='".$filename."'";
$q = $conn->prepare($sql);
$response = $q->execute();    
if ($response){

	$target_dir = "../uploads/";		
	$target_file_old = $target_dir .$filename;	
	$target_file_new = $target_dir ."deletedfiles/".$filename;	
	if(rename($target_file_old,$target_file_new)){
		echo "File Deleted Successfully!".PHP_EOL;		
	}else{
		echo "Removed from database, failed to move from server.";
	}
	
	//echo $target_file;
}else{
	echo "There was an error during the process!";
}
/*
$query = $conn->prepare($sql);
$query->execute();
$result = $query;
*/
//echo $sql;
}

?>