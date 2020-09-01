<?php
if (isset($_POST['fileAction'])){
	require '../vendor/autoload.php';
	require '../myclass.php';
	require '../config.php';
	$filteredUnits = array();
	//create array of units
	$myunits = array();
	$fileAction = $_POST['fileAction'];
	$assumedFileType = null;
	
	//$fileType = $_POST['fileType'];
	$target_dir = "../uploads/";		
	$target_file = $target_dir . basename($_FILES["file"]["name"]);	
	$file_name = basename($_FILES["file"]["name"]);
	$fileuploaded = false;
	$toDbSuccess = false;
	$ext = pathinfo($file_name, PATHINFO_EXTENSION);
	if ($ext != "xlsx"){
		die("Invalid file extension. Only .xlsx files allowed.");
	}
	
	if (file_exists($target_file)){
		$actual_name = pathinfo($target_file,PATHINFO_FILENAME);
		$original_name = $actual_name;
		$extension = pathinfo($target_file, PATHINFO_EXTENSION);		
		//echo "File with this name already exists <br>";
		$aa = 1;
		while (file_exists($target_file)){
			$target_file = $target_dir.$original_name."_".$aa.".".$extension;
			$file_name = $original_name."_".$aa.".".$extension;
			$aa++;
		}
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){
			$fileuploaded= true;
		}
		
	}else{
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
			$fileuploaded = true;
			//echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
		} else {
			echo "Sorry, there was an error uploading your file.<br>";
		}	
	}
	if ($fileuploaded){
		//file uploaded, now read the content of file.
		$file = $target_file;
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
	//	$reader->setReadDataOnly(true);
		$reader->setReadEmptyCells(false);
		$spreadsheet = $reader->load($file);
		$worksheet = $spreadsheet->getActiveSheet();
		foreach ($worksheet->getRowIterator() as $row) {
			$currentunit = new MyUnit();
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
															   //    even if a cell value is not set.
															   // By default, only cells that have a value
															   //    set will be iterated.
			foreach ($cellIterator as $cell) {
				//$cellValue = $cell->getValue();
				$cellValue = $cell->getFormattedValue();
				$cellColumn = $cell->getColumn(); //This will get to the first row of that column which is basically the heading of column
					switch ($columnHeading = $worksheet->getCell($cellColumn.'1')->getValue()){
						case "Course":
							$currentunit->setCourse($cellValue);
						break;
						
						case "Specialisation":
							$currentunit->setSpecialisation($cellValue);
						break;
						
						case "Intake":
							$currentunit->setIntake($cellValue);
						break;

						case "Year":
							$currentunit->setYear($cellValue);
						break;					

						case "Semester":
							$currentunit->setSemester($cellValue);
						break;
						
						case "Unit Code ":
							$currentunit->setUnitCode($cellValue);
						break;

						case "Unit Code":
							$currentunit->setUnitCode($cellValue);
						break;
						
						case "Unit Title":
							$currentunit->setUnitTitle($cellValue);
						break;
						
						case "Acitivity Code":
							$currentunit->setActivitycode($cellValue);
						break;
						
						case "Activity Code":
							$currentunit->setActivitycode($cellValue); 
						break;

						case "Teaching Department":
							$currentunit->setTeachingdepartment($cellValue);
						break;
						
						case "Faculty":
							$currentunit->setFaculty($cellValue);
						break;					
						
						case "Activity Type":
							$currentunit->setActivitytype($cellValue);
						break;				

						case "Start Day":
							$currentunit->setStartday($cellValue);
						break;
						//for new file		
						case "Scheduled Day":
							$currentunit->setStartday($cellValue);
						break;

						case "Start Time":
							$currentunit->setStarttime($cellValue);
						break;					
						
						case "Teaching week pattern":
							$currentunit->setWeekPattern($cellValue);
						break;
						//for new file
						case "Week Pattern":
							$currentunit->setWeekPattern($cellValue);
						break;						
						case "Number of Teaching Weeks":
							$currentunit->setNoofweeks($cellValue);
						break;
						//for new file
						case "Teaching Weeks":
							$currentunit->setNoofweeks($cellValue);
						break;
						case "Duration":
							$currentunit->setDuration($cellValue);
						break;
						//for new file, because it has blank space after Duration
						case "Duration ":
							$currentunit->setDuration($cellValue);
						break;
						case "Locations":
							$currentunit->setLocations($cellValue);
						break;
						//for new file, in old file client provided, he had written 'Locations' and in new file it is 'Location' 
						case "Location":
							$currentunit->setLocations($cellValue);
						break;
						case "File Year":
							$currentunit->setFileYear($cellValue);
						break;
						default:
							//echo $columnHeading;
						break;
					}
			}
			$myunits[] = $currentunit;
		}
		$totalrecords = count($myunits) -1; //subtract the first heading row.
		//echo $totalrecords." records ready to go in db!";
		if ($totalrecords > 0){
			$testedUnit = "";
			$b = false;
			foreach  ($myunits as $myunit){
			    if(!$b && $myunit->getFileYear() !== '' && $myunit->getCourse() !== ''){       //to avoid first row (of headings)
					$b = true;
					continue;
				}
				//$unit1 = $myunits[1];
				$unit1 = $myunit;
				
				if (($myunit->getCourse() !== '') && ($myunit->getSpecialisation() !== '') && ($myunit->getActivitytype() == '')){
					//is study plan.
					$assumedFileType = "studyPlan";
					$testedUnit = $myunit;
					break;
				}
				if ($myunit->getCourse() == null && $myunit->getDuration() != null && $myunit->getActivitytype() !=null && $myunit->getSpecialisation() == null){
					//is timetable.
					$assumedFileType = "timeTable";
					$testedUnit = $myunit;
					break;
				}
			}
			$myUnits = $myunits;
			//add the data in database.
			if ($assumedFileType == "timeTable"){ 
				if (!$testedUnit->isValidTimetableFile()){
					die("File does not have all the columns required for timetable sheet file, refer to user manual.");
				}
				$tablename = $units_table_name;
				$sh = $conn->prepare( "DESCRIBE $tablename");
				if ( tableExists($conn, $tablename) ) {
					// my_table exists
					//echo "Table already there";
				}else{
					$sql = "CREATE TABLE ". $tablename." ( id INT NOT NULL AUTO_INCREMENT ,
					UnitCode VARCHAR(100) NOT NULL , 
					UnitTitle VARCHAR(300) NOT NULL ,
					Semester VARCHAR(100) NOT NULL ,
					ActivityCode VARCHAR(100) NOT NULL ,
					TeachingDepartment VARCHAR(300) NOT NULL , 
					Faculty VARCHAR(100) NOT NULL , 
					ActivityType VARCHAR(100) NOT NULL , 
					StartDay VARCHAR(100) NOT NULL , 
					StartTime VARCHAR(100) NOT NULL , 
					TeachingWeekPattern VARCHAR(100) NOT NULL , 
					NoOfWeeks VARCHAR(100) NOT NULL , 
					Duration VARCHAR(100) NOT NULL , 
					Locations VARCHAR(100) NOT NULL , 
					FileYear VARCHAR (100) NOT NULL,
					FileName VARCHAR (200) NOT NULL,
					DateUploaded TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (id)) ENGINE = InnoDB;";
					$conn->exec($sql);
					//echo "Table ". $tablename ." created successfully <br>";
				}
				$sql_delete = "DELETE FROM ".$tablename." WHERE FileYear='".$testedUnit->getFileYear()."'";
				//echo $sql_delete;
				$conn->exec($sql_delete);		
				$i=0;
				foreach ($myUnits as $unit){
					$activityCode = $unit->getActivitycode();
					$teachingDepartment = $unit->getTeachingdepartment();
					$faculty = $unit->getFaculty();
					$activityType = $unit->getActivitytype();
					$startDay = $unit->getStartDay();
					$startTime = $unit->getStartTime();
					$teachingWeekPattern =$unit->getWeekpattern();
					$noOfweeks =$unit->getNoofweeks();
					$duration = $unit->getDuration();
					$locations = $unit->getLocations();
					$unitcode = $unit->getUnitCode();
					$unittitle = $unit->getUnitTitle();
					$semester = $unit->getSemester();
					$fileyear = $unit->getFileYear();
					if ($i != 0){
						$sql = "INSERT INTO $tablename (id, UnitCode, UnitTitle, Semester, ActivityCode, TeachingDepartment, Faculty, ActivityType, StartDay, StartTime, TeachingWeekPattern, NoOfWeeks, Duration, Locations, FileYear, FileName)
						VALUES (NULL, '$unitcode', '$unittitle', '$semester' ,'$activityCode', '$teachingDepartment', '$faculty', '$activityType', '$startDay', '$startTime', '$teachingWeekPattern', '$noOfweeks', '$duration', '$locations', '$fileyear', '$file_name')";
						$conn->exec($sql);
					}
					$i++;
				}
				if (count($myUnits) == $i){
					$toDbSuccess = true;
					echo "Success! All data added to database.";
				}
			}else if ($assumedFileType == "studyPlan"){
				if (!$testedUnit->isValidStudyplanFile()){
					die("File does not have required columns for study plan sheet file, refer to user manual.");
				}
				
				//$tablename = "StudyPlanRecord".$date->getTimestamp();
				$tablename = $studyplans_table_name;
				//$sh = $conn->prepare( "DESCRIBE $tablename");
				if (tableExists($conn,$tablename)) {
					//echo "Table already there";
				} else {
					$sql = "CREATE TABLE ". $tablename." ( id INT NOT NULL AUTO_INCREMENT ,
					Course VARCHAR(100) NOT NULL ,
					Specialisation VARCHAR(300) NOT NULL , 
					Intake VARCHAR(100) NOT NULL , 
					Year VARCHAR (100) NOT NULL ,
					Semester VARCHAR(100) NOT NULL , 
					UnitCode VARCHAR(100) NOT NULL , 
					UnitTitle VARCHAR(100) NOT NULL ,
					FileYear VARCHAR (100) NOT NULL,
					FileName VARCHAR (200) NOT NULL,
					DateUploaded TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,					
					PRIMARY KEY (id)) ENGINE = InnoDB;";
					$conn->exec($sql);
					//echo "Table ". $tablename ." created successfully <br>";		
				}		
				//delete records if already exist for this year and course.
				$sql_delete = "DELETE FROM ".$tablename." WHERE Course = '".$testedUnit->getCourse()."' AND FileYear='".$testedUnit->getFileYear()."'";
				//echo $sql_delete;
				$conn->exec($sql_delete);

				$i=0;
				$addedCount = 0;
				foreach ($myUnits as $unit){
					$course = $unit->getCourse();
					$specialisation = $unit->getSpecialisation();
					$intake = $unit->getIntake();
					$year = $unit->getYear();
					$semester = $unit->getSemester();
					$unitcode = $unit->getUnitCode();
					$unittitle = $unit->getUnitTitle();
					$fileyear = $unit->getFileYear();
					if ($i != 0 && $course !== "" && $specialisation !== "" && $fileyear !== ""){
						$sql = "INSERT INTO $tablename (id, Course, Specialisation, Intake, Year, Semester, UnitCode, UnitTitle, FileYear, FileName)
						VALUES (NULL, '$course', '$specialisation', '$intake', '$year', '$semester', '$unitcode', '$unittitle', '$fileyear', '$file_name')";
						$conn->exec($sql);
						$addedCount++;
					}
					$i++;

				}
				if ($addedCount > 0){
					$toDbSuccess = true;
					echo "Success! All data added to database. (".$addedCount."rows)";
				}else{
					echo "Failed to add data in database. Please check if the file contains valid data.";
				}

			}else{
				echo "Invalid file format, please check if you uploaded valid file and selected correct file type(study plan or timetable?).";
			}
		}
	}else{
		echo "There was an error in uploading file to server!";
	}
	if (!$toDbSuccess){
		//failed to add data in database, so delete this file from server.
		if (file_exists($target_file)){
			unlink($target_file);
		}
	}
	
	//echo "File Type: ".$filetype.", File: ".$file_name;
}else{
	echo "No post";
}
?>
