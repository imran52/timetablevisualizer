 <?php
$servername = "localhost";
$username = "root";
//$password = "root1234";
$password = "";
$sql_db = "arp20";

$units_table_name = "timetables";
$studyplans_table_name = "studyplans";

$connected = false;
$connResultArray = Array();
try {
    $conn = new PDO("mysql:host=$servername;dbname=$sql_db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
	$connected = true;
	//die(json_encode(array('outcome' => true)));
	//$connResultArray = array('outcome' => true, 'message' => 'success');
	$connResultArray["outcome"] = true;
	$connResultArray["message"] = 'success';
	
}
catch(PDOException $e){
    //echo "Connection failed: Check database settings or contact site admin.<br>".PHP_EOL;
	//die(json_encode(array('outcome' => false, 'message' => $e->getMessage())));
	//$connResultArray = array('outcome' => false, 'message' => $e->getMessage());
	$connResultArray["outcome"] = false;
	$connResultArray["message"] = $e->getMessage();

}

//Credit: https://stackoverflow.com/questions/1717495/check-if-a-database-table-exists-using-php-pdo
/**
 * Check if a table exists in the current database.
 *
 * @param PDO $pdo PDO instance connected to a database.
 * @param string $table Table to search for.
 * @return bool TRUE if table exists, FALSE if no table found.
 */
function tableExists($pdo, $table) {
	// Try a select statement against the table
	// Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
	try {
		$result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
	} catch (Exception $e) {
		// We got an exception == table not found
		return FALSE;
	}
	// Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
	return $result !== FALSE;
}	

//check if tables exists.
$config_units_table_exists = tableExists($conn,$units_table_name);
$config_studyplans_table_exists = tableExists($conn,$studyplans_table_name);

//create tables if does not exist.
if (!$config_units_table_exists){
	$sql = "CREATE TABLE ". $units_table_name." ( id INT NOT NULL AUTO_INCREMENT ,
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
}

if (!$config_studyplans_table_exists){
	$sql = "CREATE TABLE ". $studyplans_table_name." ( id INT NOT NULL AUTO_INCREMENT ,
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
}
	

?> 