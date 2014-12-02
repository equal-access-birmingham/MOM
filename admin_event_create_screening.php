<?php
require('includes/header-require_admin.php');

error_reporting(E_ALL);
ini_set("display_errors", 1);



$program_name = $_GET['name'];
$location_name = $_GET['location_name'];
$address = $_GET['location_address'];
$program_type_id = $_GET['program_type_id'];



$screening_month = $_GET['screening_month'];
$screening_day = $_GET['screening_day'];
$screening_year = $_GET['screening_year'];
$screening_date = $screening_year . "-" . $screening_month . "-" . $screening_day;


$start_time_hour = $_GET['start_time_hour'];
$start_time_minute = $_GET['start_time_minute'];
$start_time_ampm = $_GET['start_time_ampm'];
if ($start_time_ampm = 2)
{
	$new_start_time_hour = $start_time_hour + 12;
}
elseif ($start_time_ampm = 1)
{
	$new_start_time_hour = $start_time_hour;
}
$start_time = $new_start_time_hour . ":" . $start_time_minute;

$end_time_hour = $_GET['end_time_hour'];
$end_time_minute = $_GET['end_time_minute'];
$end_time_ampm = $_GET['end_time_ampm'];
if ($end_time_ampm = 2)
{
	$new_end_time_hour = $end_time_hour + 12;
}
elseif ($end_time_ampm = 1)
{
	$new_end_time_hour = $end_time_hour;
}
$end_time = $new_end_time_hour . ":" . $end_time_minute;

$arrival_time_hour = $_GET['arrival_time_hour'];
$arrival_time_minute = $_GET['arrival_time_minute'];
$arrival_time_ampm = $_GET['arrival_time_ampm'];
if ($arrival_time_ampm = 2)
{
	$new_arrival_time_hour = $arrival_time_hour + 12;
}
elseif ($arrival_time_ampm = 1)
{
	$new_arrival_time_hour = $arrival_time_hour;
}
$arrival_time = $new_arrival_time_hour . ":" . $arrival_time_minute;


try 
{
	$con=new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
	die();
}
//location table
	//location name
	//location address
//program_table
	//program_type_id
	//program_name
//Use location_id and program_id to insert program_relation_table
	//date
	//start_time
	//end_time
//Take program_id from program_table and insert into arrival_time_table
//Take role_id if program_type_id = 3 and insert all applicable role_id into arrival_time_table
	//arrival_time (into arrival_time_table)
	
	

$query = "SELECT COUNT(*) as count FROM location_table WHERE location_name = :location_name AND address = :address;";
$stmt_location = $con->prepare($query);
$stmt_location->bindParam(':location_name', $location_name, PDO::PARAM_STR);
$stmt_location->bindParam(':address', $address, PDO::PARAM_STR);
$stmt_location->execute();
$result_location = $stmt_location->fetch();

$query = "SELECT COUNT(*) as count FROM program_table WHERE program_name = :program_name AND program_type_id = :program_type_id;";
$stmt_program = $con->prepare($query);
$stmt_program->bindParam(':program_name', $program_name, PDO::PARAM_STR);
$stmt_program->bindParam(':program_type_id', $program_type_id, PDO::PARAM_STR);
$stmt_program->execute();
$result_program = $stmt_program->fetch();

$query = "SELECT COUNT(*) as count FROM program_relation_table where program_id = :program_id AND location_id = :location_id AND date = :date AND start_time = :start_time AND end_time = :end_time);";
$stmt_program_relation = $con->prepare($query);
$stmt_program_relation->bindParam(':program_id', $program_id, PDO::PARAM_STR);
$stmt_program_relation->bindParam(':location_id', $location_id, PDO::PARAM_STR);
$stmt_program_relation->bindParam(':date', $screening_date, PDO::PARAM_STR);
$stmt_program_relation->bindParam(':start_time', $start_time, PDO::PARAM_STR);
$stmt_program_relation->bindParam(':end_time', $end_time, PDO::PARAM_STR);
$stmt_program_relation->execute();
$result_program_relation = $stmt_program_relation->fetch();


if(!$result_location['count'] && !$result_program['count'] && !$result_program_relation['count'])
{
	$query = "INSERT INTO location_table VALUES ('', :location_name, :address);";
	$stmt = $con->prepare($query);
	$stmt->bindParam(':location_name', $location_name, PDO::PARAM_STR);
	$stmt->bindParam(':address', $address, PDO::PARAM_STR);
	$stmt->execute();
	
	$location_id = $con->lastInsertId();
	$stmt = null;
	
	
	$query = "INSERT INTO program_table(program_name, program_type_id) VALUES (:program_name, :program_type_id);";
	$stmt = $con->prepare($query);
	$stmt->bindParam(':program_name', $program_name, PDO::PARAM_STR);
	$stmt->bindParam(':program_type_id', $program_type_id, PDO::PARAM_STR);
	$stmt->execute();
	
	$program_id = $con->lastInsertId();
	
	$stmt = null;
	
	$query = "INSERT INTO program_relation_table VALUES ('', :program_id, :location_id, :date, :start_time, :end_time);";
	$stmt = $con->prepare($query);
	$stmt->bindParam(':program_id', $program_id, PDO::PARAM_STR);
	$stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR);
	$stmt->bindParam(':date', $screening_date, PDO::PARAM_STR);
	$stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);
	$stmt->bindParam(':end_time', $end_time, PDO::PARAM_STR);
	$stmt->execute();
	
	
	$stmt = null;
}


$query = "SELECT role_id FROM role_program_type_relation_table WHERE program_type_id = 3;";
$stmt_role_program = $con->prepare($query);
$stmt_role_program->execute();

echo $program_id;

$insert_query = "INSERT INTO arrival_time_table VALUES ('', :role_id, :program_id, :arrival_time);";
$role_arrival = $con->prepare($insert_query);
$role_arrival->bindParam(':role_id', $role_id, PDO::PARAM_STR);
$role_arrival->bindParam(':program_id', $program_id, PDO::PARAM_STR);
$role_arrival->bindParam(':arrival_time', $arrival_time, PDO::PARAM_STR);
while ($result_role_program = $stmt_role_program->fetch())
{
	$role_id = $result_role_program['role_id'];
	$role_arrival->execute();
}

$stmt_role_program = null;
$role_arrival = null;



	$date_array = explode("-", $screening_date);
	switch($date_array[1])
	{
		case 1:
			$date_array[1] = "January";
			break;
		case 2;
			$date_array[1] = "February";
			break;
		case 3;
			$date_array[1] = "March";
			break;
		case 4;
			$date_array[1] = "April";
			break;
		case 5;
			$date_array[1] = "May";
			break;
		case 6;
			$date_array[1] = "June";
			break;
		case 7;
			$date_array[1] = "July";
			break;
		case 8;
			$date_array[1] = "August";
			break;
		case 9;
			$date_array[1] = "September";
			break;
		case 10;
			$date_array[1] = "October";
			break;
		case 11;
			$date_array[1] = "November";
			break;
		case 12;
			$date_array[1] = "December";
			break;
	}

	$new_date = $date_array[1] . " " . $date_array[2] . ", " . $date_array[0];
	
	echo $new_date;

	$time_start = explode(":", $start_time);

	if(intval($time_start[0]) > 12)
	{
		$hour_start = $time_start[0] - 12;
		$new_start_time = $hour_start . ":" . $time_start[1] . " PM";
	}
	else
	{
		$hour_start = $time_start[0];
		$new_start_time = $hour_start . ":" . $time_start[1] . " AM";
	}

	echo $new_start_time;
	

	$time_end = explode(":", $end_time);

	if(intval($time_end[0]) > 12)
	{
		$hour_end = $time_end[0] - 12;
		$new_end_time = $hour_end . ":" . $time_end[1] . " PM";
	}
	else
	{
		$hour_end = $time_end[0];
		$new_end_time = $hour_end . ":" . $time_end[1] . " AM";
	}
	
	echo $new_end_time;
	
	
		$time_arrival = explode(":", $arrival_time);

	if(intval($time_arrival[0]) > 12)
	{
		$hour_arrival = $time_arrival[0] - 12;
		$new_arrival_time = $hour_arrival . ":" . $time_arrival[1] . " PM";
	}
	else
	{
		$hour_arrival = $time_arrival[0];
		$new_arrival_time = $hour_arrival . ":" . $time_arrival[1] . " AM";
	}
	
	echo $new_arrival_time;
	
	
	
echo "	
<h1> Congratulations! </h1>
     ";



echo "
        <h2>You have created the following screening event.</h2>
          <ul>
            <li>Name: " . $program_name . "</li>
            <li>Location: " . $location_name . "</li>
            <li>Address: " . $address . "</li>
            <li>Date: " . $new_date . "</li>
            <li>Start Time: " . $new_start_time . "</li> 
            <li>End Time: " . $new_end_time . "</li>
            <li>Officer Arrival Time: " . $new_arrival_time . "</li>
          </ul>
          ";





?>
