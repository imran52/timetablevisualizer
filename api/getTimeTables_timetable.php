<?php
if (isset($_POST['selectedYear']) && isset($_POST['selectedCourse']) && isset($_POST['selectedSemester']) && isset($_POST['selectedSpecialisation'])){
	require '../config.php';
	define("NODATA", "--");
	$selectedYear = $_POST["selectedYear"];
	$selectedCourse = $_POST["selectedCourse"];
	$selectedSemester = $_POST["selectedSemester"]; //this is actually intake.
	$selectedSpecialisation = $_POST["selectedSpecialisation"];
	
	$sql_semcount = "SELECT MAX(CAST(Year AS SIGNED)) as max_years FROM $studyplans_table_name WHERE 
	Course='".$selectedCourse."'
	AND Intake = '1'";
	$r_1 = $conn->query( $sql_semcount)->fetch();
	$year_count = $r_1["max_years"];
	$sem_count= $year_count * 2;
	//echo $sem_count.PHP_EOL;

	$sql = "SELECT id,Course,Specialisation,Year,Intake,Semester,FileYear FROM $studyplans_table_name WHERE 
	Course = '".$selectedCourse."'
	AND Semester = '".$selectedSemester."'
	AND Specialisation = '".$selectedSpecialisation."'
	GROUP BY Year,FileYear,Intake ORDER BY FileYear DESC, Intake DESC";
	$query = $conn->prepare($sql);
	//$query2->fetchAll(PDO::FETCH_ASSOC);
	$query->execute();
	$result =$query;
	$timetables = array();
	$i=0;
	foreach($result as $row){
		$year = $row["Year"];
		$year--;
		$fileyear = $row["FileYear"];
		if (($year + $fileyear) == $selectedYear){
		//echo $row["FileYear"]." Year ".$row["Year"].PHP_EOL."<br/>";
		$timetables[$i]["Course"] = $selectedCourse;
		$timetables[$i]["Specialisation"] = $selectedSpecialisation;
		$timetables[$i]["Year"] = $row["Year"];
		$timetables[$i]["FileYear"] = $row["FileYear"];
		$timetables[$i]["Intake"] = $row["Intake"];
		$timetables[$i]["Semester"] = $row["Semester"];
		$timetables[$i]["Units"] = [];
		
		$sql2 = "SELECT * FROM $studyplans_table_name WHERE 
		Course = '".$selectedCourse."' AND 
		FileYear = '".$fileyear."' AND 
		Intake = '".$row["Intake"]."' AND 
		Semester = '".$selectedSemester."' AND 
		Specialisation = '".$selectedSpecialisation."' AND
		Semester= '".$row["Semester"]."' AND
		Year='".$row["Year"]."'		
		ORDER BY id ASC";
		$query2 = $conn->prepare($sql2);
		$query2->execute();
		$result2 = $query2;
		$j=0;
		foreach($result2 as $row2){
			$timetables[$i]["hasUnits"] = true;
			//get unit data
			$unitTitle = str_replace(" and "," & ",$row2["UnitTitle"]);
			$unitCode = $row2["UnitCode"];
			$timetables[$i]["Units"][$j]["UnitCode"] = $unitCode;	
			$timetables[$i]["Units"][$j]["UnitTitle"] = $unitTitle;	
			$sql_unit = "SELECT * FROM $units_table_name WHERE FileYear='".$selectedYear."' AND Semester='".$row2["Semester"]."' AND UnitCode LIKE '%".$unitCode."%' AND ActivityCode LIKE '%/LE1/01%'";
			$query3 = $conn->prepare($sql_unit);
			$query3->execute();
			$result3 = $query3;
			$row3 = $query3->fetch();

			if ($row3){

				$startDay = $row3["StartDay"];
				$startTime = $row3["StartTime"];
				$duration =$row3["Duration"];
				$time = strtotime($startTime);
				//$startTime_ = date("H:i", strtotime('-'..' minutes', $time));
				$endTime_ = date("H:i", strtotime('+'.$duration.' minutes', $time));
				$endTime = strtotime('+'.$duration.' minutes', $time);
				$timeStr = $startTime."-".$endTime_;
			
				//clashes and same day different time adjustment
				$key = array_search($timeStr, array_column($timetables[$i]["Units"], 'TimeString'));				
				if ($key !== false && $key !== $j){
					//unit with same time combination exists

					if ($startDay !== $timetables[$i]["Units"][$key]["StartDay"]){
						//days are different, so no clash
						//since time matches, add the unit title in same time range to avoid adding new row
						$timetables[$i]["Units"][$key]["hasClash"] = false;
						if ($startDay == "Monday"){
							$timetables[$i]["Units"][$key]["onMonday"] = $unitCode." ".$unitTitle;			
						}
						if ($startDay == "Tuesday"){
							$timetables[$i]["Units"][$key]["onTuesday"] = $unitCode." ".$unitTitle;			
						}
						if ($startDay == "Wednesday"){
							$timetables[$i]["Units"][$key]["onWednesday"] = $unitCode." ".$unitTitle;			
						}				
						if ($startDay == "Thursday"){
							$timetables[$i]["Units"][$key]["onThursday"] = $unitCode." ".$unitTitle;			
						}	
						if ($startDay == "Friday"){
							$timetables[$i]["Units"][$key]["onFriday"] = $unitCode." ".$unitTitle;			
						}	
						unset($timetables[$i]["Units"][$j]);
					}
					if ($startDay === $timetables[$i]["Units"][$key]["StartDay"]){
						//POSSIBLE CLASH CASE
						//Note that this clash case does not check if the two time ranges cross each other.
						//This is only for case where the two time ranges are exact same.
						$timetables[$i]["Units"][$key]["hasClash"] = true;
					}
				}else{
					//echo $key;
					$timetables[$i]["Units"][$j]["hasTimetable"] = true;
					$timetables[$i]["Units"][$j]["hasClash"] = false;
					$timetables[$i]["Units"][$j]["StartDay"] = $startDay;
					$timetables[$i]["Units"][$j]["StartTime"] = $startTime;
					$timetables[$i]["Units"][$j]["TimeString"] = $timeStr;
					$timetables[$i]["Units"][$j]["Duration"] = $row3["Duration"];
					
					$timetables[$i]["Units"][$j]["onMonday"] = NODATA;	
					$timetables[$i]["Units"][$j]["onTuesday"] = NODATA;	
					$timetables[$i]["Units"][$j]["onWednesday"] = NODATA;	
					$timetables[$i]["Units"][$j]["onThursday"] = NODATA;	
					$timetables[$i]["Units"][$j]["onFriday"] = NODATA;	
					
					if ($startDay == "Monday"){
						$timetables[$i]["Units"][$j]["onMonday"] = $unitCode." ".$unitTitle;			
					}
					if ($startDay == "Tuesday"){
						$timetables[$i]["Units"][$j]["onTuesday"] = $unitCode." ".$unitTitle;			
					}
					if ($startDay == "Wednesday"){
						$timetables[$i]["Units"][$j]["onWednesday"] = $unitCode." ".$unitTitle;			
					}				
					if ($startDay == "Thursday"){
						$timetables[$i]["Units"][$j]["onThursday"] = $unitCode." ".$unitTitle;			
					}	
					if ($startDay == "Friday"){
						$timetables[$i]["Units"][$j]["onFriday"] = $unitCode." ".$unitTitle;			
					}
				}
				//Check possible clash case
				$key2 = array_search($startDay, array_column($timetables[$i]["Units"], 'StartDay'));
				if ($key2 !== false && $key2 !== $j && array_key_exists($j,$timetables[$i]["Units"])){ 
					if ($timetables[$i]["Units"][$key2]["hasTimetable"]){
						//echo $timetables[$i]["Units"][$key2]["StartTime"].PHP_EOL;
						//there is another unit on same day. 
						//Check if the start time and end time of that unit cross with this unit start and end time.
						$startTime2 = $timetables[$i]["Units"][$key2]["StartTime"];
						$duration2 = $timetables[$i]["Units"][$key2]["Duration"];
						$time2_start = strtotime($startTime2);
						//$time2_end = date("H:i", strtotime('+'.$duration2.' minutes', $time2_start));
						$time2_end = strtotime('+'.$duration2.' minutes', $time2_start);
						
						$time_1 = array('start' => $time, 'end' => $endTime);
						$time_2 = array('start' => $time2_start, 'end' => $time2_end);
						//echo $j;
						$thisunit = $timetables[$i]["Units"][$key];
						$otherunit = $timetables[$i]["Units"][$j];
						
						if (($time_2['end'] > $time_1['start']) && ($time_1['end'] > $time_2['start'])) {
							$timetables[$i]["Units"][$j]["hasClash"] = true;
							$timetables[$i]["Units"][$key]["hasClash"] = true;
							if ($startDay == "Monday"){
								$timetables[$i]["Units"][$j]["ClashOnMonday"] = true;			
								$timetables[$i]["Units"][$key]["ClashOnMonday"] = true;			
							}
							if ($startDay == "Tuesday"){
								$timetables[$i]["Units"][$j]["ClashOnTuesday"] = true;			
								$timetables[$i]["Units"][$key]["ClashOnTuesday"] = true;			
							}
							if ($startDay == "Wednesday"){
								$timetables[$i]["Units"][$j]["ClashOnWednesday"] = true;			
								$timetables[$i]["Units"][$key]["ClashOnWednesday"] = true;			
							}				
							if ($startDay == "Thursday"){
								$timetables[$i]["Units"][$j]["ClashOnThursday"] = true;			
								$timetables[$i]["Units"][$key]["ClashOnThursday"] = true;			
							}	
							if ($startDay == "Friday"){
								$timetables[$i]["Units"][$j]["ClashOnFriday"] = true;			
								$timetables[$i]["Units"][$key]["ClashOnFriday"] = true;			
							}						
							//echo 'Conflict handling'.PHP_EOL;
						}else{
							//echo "No Conflict ".$row3["StartDay"]." - ".$timetables[$i]["Units"][$key2]["StartDay"].PHP_EOL;
						}							
					}
				}
				
			}else{
				$timetables[$i]["Units"][$j]["hasTimetable"] = false;
			}

			//sort this timetable by 'hasTimetable'
			usort($timetables[$i]["Units"], function($a, $b) {
				$retval = $b['hasTimetable'] <=> $a['hasTimetable'];
				return $retval;
			});

			$j++;
		}
		//print_r($timetables[$i]);
		$i++;
		}
	}
	$timetables2 = array();
	if (count($timetables) !== $sem_count){
		$fy = $selectedYear;
		$fyo = $selectedYear; //original, since previous will vary later
		$s = $selectedSemester;
		$c = $selectedCourse;
		//Generate sequenced structure of timetables
		for ($x = 0; $x < $sem_count; $x++) {
			$timetable2[$x]["hasUnits"] = false;
			if ($x>0){
				if ($timetables2[$x-1]["Intake"] == "1"){
					$fy--;
					$timetables2[$x]["Intake"] = "2";
				}else{
					$timetables2[$x]["Intake"] = "1";
				}
				$timetables2[$x]["Year"] = ($fyo-$fy)+1;
			}
			$timetables2[$x]["FileYear"] = $fy;
			$timetables2[$x]["Semester"] = $s;
			
			if ($s == "1" && ($x%2 == 0) && $x == 0){
				$timetables2[$x]["Intake"] = "1";
				$timetables2[$x]["Year"] = "1";
			}
			if ($s == "2" && ($x%2 == 0) && $x == 0){
				$timetables2[$x]["Intake"] = "2";
				$timetables2[$x]["Year"] = "1";
			}	
			
		} 
		$y = 0;
		foreach ($timetables2 as $timetable2){
			$z =0;
			foreach ($timetables as $timetable){
				if ($timetable["FileYear"] == $timetable2["FileYear"] && $timetable["Intake"] == $timetable2["Intake"] && $timetable["Year"] == $timetable2["Year"]){
					//matching year,fileyear,intake.
					$timetables2[$y] = $timetables[$z]; 
					//$timetables2[$y]["hasUnits"] = true;
				}
					
				$z++;
			}
			$y++;
		}		
		echo json_encode($timetables2);
			
	}else{
		echo json_encode($timetables);		
	}


}
?>