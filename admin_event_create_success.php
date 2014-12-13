<?php
require('includes/header-require_admin.php');

error_reporting(E_ALL);
ini_set("display_errors", 1);

$action = $_GET['eventcreation'];
$program_id = $_GET['program_id'];
$date = new DateTime ($_GET['date'], new DateTimeZone('America/Chicago'));
$new_date = $date->format("Y-m-d");
$clinic_repeats = $_GET['clinic_repeats'];
try 
{
	$con=new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
	die();
}
if ($program_id == 1)
{
	$location_id = 1;
	$start_time = "13:00:00";
	$end_time = "18:00:00";
}
elseif ($program_id == 2)
{
	$location_id = 2;
	$start_time = "15:30:00";
	$end_time = "19:00:00";
}
//get variables
	//clinic
	//date
	//number of repeats
//create an insert statement
	//program_id
	//location_id
	//date
	//start_time
	//end_time
$query = "INSERT INTO program_relation_table VALUES ('', :program_id, :location_id, :date, :start_time, :end_time);";
$stmt1 = $con->prepare($query);
$stmt1->bindParam(':program_id', $program_id, PDO::PARAM_STR);
$stmt1->bindParam(':location_id', $location_id, PDO::PARAM_STR);
$stmt1->bindParam(':date', $new_date, PDO::PARAM_STR);
$stmt1->bindParam(':start_time', $start_time, PDO::PARAM_STR);
$stmt1->bindParam(':end_time', $end_time, PDO::PARAM_STR);

$dup = "SELECT COUNT(*) AS 'dupes'
FROM program_relation_table
WHERE `date` = :new_date AND `program_id` = :program_id";
$stmt2 = $con->prepare($dup);
$stmt2->bindParam(':new_date', $new_date, PDO::PARAM_STR);
$stmt2->bindParam(':program_id', $program_id, PDO::PARAM_STR);

$program = "SELECT program_table.program_name FROM program_table
WHERE `program_id` = :program_id";
$stmt3 = $con->prepare($program);
$stmt3->bindParam(':program_id', $program_id, PDO::PARAM_STR);
$stmt3->execute();
$result = $stmt3->fetch();

echo "Congratulations!";
echo "You have created a " . $result['program_name'] . " event on the following dates: <br />";


//build the for loop(properly inserts the dates.)
for($i = 0; $i < $clinic_repeats; $i++)
{
	$stmt2->execute();
	$result = $stmt2->fetch();
	if ($result['dupes'] == 0)
	{
		$stmt1->execute();
	}

echo "$new_date <br />";
	$new_date = $date->modify("next Sunday")->format("Y-m-d");

}


?>
