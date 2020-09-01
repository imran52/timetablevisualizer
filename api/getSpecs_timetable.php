<?php
if (isset($_POST['selectedYear']) && isset($_POST['selectedCourse']) && isset($_POST['selectedSemester'])){
	require '../config.php';
	$selectedYear = $_POST["selectedYear"];
	$selectedCourse = $_POST["selectedCourse"];
	$selectedSemester = $_POST["selectedSemester"]; //this is actually intake.
	
	$sql = "SELECT id,Specialisation FROM $studyplans_table_name WHERE 
	Course = '". $selectedCourse ."' AND 
	Semester = '".$selectedSemester."' 
	GROUP BY Specialisation 
	ORDER BY Specialisation ASC";
	//BY Year,Semester,Intake,id ASC
	$query = $conn->prepare($sql);
	$query->execute();
	$result = $query;
	$array = array();
	
	//echo $sql.PHP_EOL;
	$i=0;
	foreach ($result as $row){
		$specialisation = $row["Specialisation"];
		$array[$i]["id"] = $row["id"];
		$array[$i]["Specialisation"] = $specialisation;

		$acronym = "";
		$arr = preg_split("/[\- ]+/", $specialisation);
		foreach ($arr as $w) {
			if (ctype_upper($w[0])){
				$acronym .= $w[0];
			}
		}		

		$array[$i]["Initials"] = $acronym;		
		//$array[$i]["Status"] ="000";
		$hasClash = false;

		$sql0 = "SELECT id,Course,Specialisation,Year,Intake,Semester,FileYear FROM $studyplans_table_name WHERE 
		Course = '".$selectedCourse."'
		AND Semester = '".$selectedSemester."'
		AND Specialisation = '".$specialisation."'
		GROUP BY Year,FileYear,Intake ORDER BY FileYear DESC";
		$query0 = $conn->prepare($sql0);
		$query0->execute();
		$result0 =$query0;
		
		foreach($result0 as $row0){
			$year = $row0["Year"];
			$year--;
			$fileyear = $row0["FileYear"];
			if (($year + $fileyear) == $selectedYear){
				$sql2 = "SELECT * FROM $studyplans_table_name WHERE 
				Course = '".$row0["Course"]."'
				AND Semester = '".$selectedSemester."'
				AND Specialisation = '".$specialisation."'
				AND Intake = '".$row0["Intake"]."'
				AND Year = '".$row0["Year"]."'
				AND FileYear = '".$fileyear."'
				ORDER BY FileYear DESC";
				//echo $sql2.PHP_EOL;
				$query2 = $conn->prepare($sql2);
				$query2->execute();
				$k = 0;
				$lectures = array();
				foreach($query2 as $r2){
					
					$unitTitle = $r2["UnitTitle"];
					$unitCode = $r2["UnitCode"];
					//Now get the timetable of each units' lecture
					$sql_unit_timetable = "SELECT StartDay,StartTime,Duration FROM $units_table_name WHERE FileYear='".$selectedYear."' AND Semester='".$selectedSemester."' AND UnitCode LIKE '%".$unitCode."%' AND ActivityCode LIKE '%/LE1/01%'";
					if ($r2["Specialisation"] == 'Mobile and Cloud Computing'){
						//echo $r2["Intake"]." ".$sql_unit_timetable.PHP_EOL;
					}
					$ttquery = $conn->prepare($sql_unit_timetable);
					$ttquery->execute();
					$ttquery = $ttquery->fetch();
					$r3 = $ttquery;
					if ($r3){
						//lecture(s) found for this unit
						//echo $unitTitle." ".$r3["StartTime"]."<br>";
						if (count($lectures) > 0){
							foreach ($lectures as $lecture){
								if ($lecture["StartDay"] == $r3["StartDay"]){
									//same day
									//echo "Same day ".$lecture["UnitTitle"]." AND ".$r2["UnitTitle"];
									$time1 = DateTime::createFromFormat('H:i', $r3["StartTime"]);
									$time1_end = DateTime::createFromFormat('H:i', $r3["StartTime"]);
									$time1_end->add(new DateInterval('PT'.$r3["Duration"].'M'));
									
									$time2 = DateTime::createFromFormat('H:i', $lecture["StartTime"]);
									$time2_end = DateTime::createFromFormat('H:i', $lecture["StartTime"]);
									$time2_end->add(new DateInterval('PT'.$lecture["Duration"].'M'));
									
									//echo $time1->format('H:i')." - ".$time1_end->format('H:i');
									
									if ($time2 < $time1_end && $time2_end > $time1){
										//CLASH case	
										$hasClash = true;
										//echo "CLASH ".$lecture["UnitTitle"]." AND ".$r2["UnitTitle"];
										break;
									}								
								}
							}
						}else{
							$lectures[$k]["UnitTitle"] = $unitTitle;
							$lectures[$k]["UnitCode"] = $unitCode;
							$lectures[$k]["StartDay"] = $r3["StartDay"];
							$lectures[$k]["StartTime"] = $r3["StartTime"];
							$lectures[$k]["Duration"] = $r3["Duration"];
						}
					}
				}
				//echo "<br>";
			}
		}
		//echo $hasClash;		
		$array[$i]["Status"] = $hasClash;
		/*
		if ($hasClash){
			$array[$i]["Status"] ="One or more clashes";
		}else{
			$array[$i]["Status"] ="No clash detected";
		}
		*/
		$i++;
	}
	echo json_encode($array);
}
?>