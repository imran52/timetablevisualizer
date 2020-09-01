<?php
class MyUnit {
	private $course = null;
	private $specialisation = null;
	private $intake = null;
	private $year = null;
	private $semester = null;
	private $unitcode = null;
	private $unittitle = null;
	
	private $activitycode = null;
	private $teachingdepartment = null;
	private $faculty = null;
	private $activitytype = null;
	private $startday = null;
	private $starttime = null;
	private $weekpattern = null;
	private $noofweeks = null;
	private $duration = null;
	private $locations = null;
	
	private $fileYear = null;
	
	//Setters
	public function setCourse($_course){
		$this->course = $_course;
	}

	public function setSpecialisation($_specialisation){
		$this->specialisation = $_specialisation;
	}
	
	public function setIntake($_intake){
		$this->intake = $_intake;
	}
	
	public function setYear($_year){
		$this->year = $_year;
	}
	
	public function setSemester($_semester){
		$this->semester = $_semester;
	}
	
	public function setUnitCode($_unitcode){
		$this->unitcode = $_unitcode;
	}	
	public function setUnitTitle($_unittitle){
		$this->unittitle = $_unittitle;
	}	
	//Get Methods
	public function getCourse(){
		return $this->course;
	}
	public function getSpecialisation(){
		return $this->specialisation;
	}
	
	public function getIntake(){
		return $this->intake;
	}
	
	public function getYear(){
		return $this->year;
	}
	
	public function getSemester(){
		return $this->semester;
	}
	
	public function getUnitCode(){
		return $this->unitcode;
	}	
	public function getUnitTitle(){
		return $this->unittitle;
	}		
	
	//More getters and setters
	public function getActivitycode(){
		return $this->activitycode;
	}

	public function setActivitycode($activitycode){
		$this->activitycode = $activitycode;
	}

	public function getTeachingdepartment(){
		return $this->teachingdepartment;
	}

	public function setTeachingdepartment($teachingdepartment){
		$this->teachingdepartment = $teachingdepartment;
	}

	public function getFaculty(){
		return $this->faculty;
	}

	public function setFaculty($faculty){
		$this->faculty = $faculty;
	}

	public function getActivitytype(){
		return $this->activitytype;
	}

	public function setActivitytype($activitytype){
		$this->activitytype = $activitytype;
	}

	public function getStartday(){
		return $this->startday;
	}

	public function setStartday($startday){
		$this->startday = $startday;
	}

	public function getStarttime(){
		return $this->starttime;
	}

	public function setStarttime($starttime){
		$this->starttime = $starttime;
	}

	public function getWeekpattern(){
		return $this->weekpattern;
	}

	public function setWeekpattern($weekpattern){
		$this->weekpattern = $weekpattern;
	}

	public function getNoofweeks(){
		return $this->noofweeks;
	}

	public function setNoofweeks($noofweeks){
		$this->noofweeks = $noofweeks;
	}

	public function getDuration(){
		return $this->duration;
	}

	public function setDuration($duration){
		$this->duration = $duration;
	}

	public function getLocations(){
		return $this->locations;
	}

	public function setLocations($locations){
		$this->locations = $locations;
	}

	public function setFileYear($fileYear){
		$this->fileYear = $fileYear;
	}

	public function getFileYear(){
		return $this->fileYear;
	}
	
	/*
	*function to check if the file we used to create this object has valid set of columns required for a study plan sheet
	*/
	public function isValidStudyplanFile(){
		if ($this->getFileYear() == null ) {return false;}
		if ($this->getCourse() == null ) {return false;}
		if ($this->getSpecialisation() == null ) {return false;}
		if ($this->getIntake() == null ) {return false;}
		if ($this->getYear() == null ) {return false;}
		if ($this->getSemester() == null ) {return false;}
		if ($this->getUnitCode() == null ) {return false;}
		if ($this->getUnitTitle() == null ) {return false;}
		return true;
	}
	/*
	*function to check if the file we used to create this object has valid set of columns required for a timetable sheet
	*/
	public function isValidTimetableFile(){
		if ($this->getFileYear() == null ) {return false;}
		if ($this->getUnitCode() == null ) {return false;}
		if ($this->getUnitTitle() == null ) {return false;}		
		if ($this->getSemester() == null ) {return false;}
		if ($this->getActivitycode() == null ) {return false;}
		if ($this->getActivitytype() == null ) {return false;}
		if ($this->getStarttime() == null ) {return false;}
		if ($this->getStartday() == null ) {return false;}
		if ($this->getDuration() == null ) {return false;}

		return true;
	}	
}
?>