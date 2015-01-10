<?php
// Note that this file is going to be required since it is so large, I am not copying this
$require_login = true;
$require_admin = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Setting up database connection
try
{
	$con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
}

/**
 * Create Event Setup Section
 */
if(isset($_POST['create_event']))
{
	// Get Variables for event creation
	$program_name = $_POST['program_name'];
	$program_type_id = $_POST['program_type_id'];

	/**
	 * These are very complicated arrays
	 * The index of the array is the role_id
	 * The values are the actual times
	 * This ties the role_id directly to the arrival time component
	 */
	$arrival_hour = $_POST['arrival_hour'];
	$arrival_minute = $_POST['arrival_minute'];
	$arrival_ampm = $_POST['arrival_ampm'];


	// Checking for duplicate events
	$query = "SELECT `program_name`, COUNT(*) AS `count` FROM `program_table` WHERE `program_name` = :program_name AND `program_type_id` = :program_type_id;";
	$stmt_dup = $con->prepare($query);
	$stmt_dup->bindValue(':program_name', $program_name, PDO::PARAM_STR);
	$stmt_dup->bindValue(':program_type_id', $program_type_id, PDO::PARAM_STR);
	$stmt_dup->execute();
	$result = $stmt_dup->fetch();

	// Query for role_id's for going through arrival_time arrays
	$query = "SELECT * FROM `role_table`;";
	$stmt_role_arrival = $con->prepare($query);
	$stmt_role_arrival->execute();
}


/**
 * Schedule Event Setup Section
 */
if(isset($_POST['schedule_event']))
{
	// Get variables for event schedule
	$program_id = $_POST['program_id'];
	$event_day = $_POST['event_day'];
	$start_date = new DateTime($_POST['start_year'] . "-" . $_POST['start_month'] . " " . $_POST['start_day']);
	$location_name = $_POST['location_name'];
	$location_address = $_POST['location_address'];
	$event_repeat = $_POST['event_repeat'];

	$start_time_obj = new DateTime($_POST['start_hour'] . ":" . $_POST['start_minute'] . " " .$_POST['start_ampm']);
	$start_time = $start_time_obj->format("H:i:00");

	$end_time_obj = new DateTime($_POST['end_hour'] . ":" . $_POST['end_minute'] . " " .$_POST['end_ampm']);
	$end_time = $end_time_obj->format("H:i:00");



	// Insert location for location_id
	// Note that this duplicates locations (not ideal, but easier for the front-end, should change this later)
	$query = "INSERT INTO `location_table` (`location_name`, `location_address`) VALUES (:location_name, :location_address);";
	$stmt_insert_location = $con->prepare($query);
	$stmt_insert_location->bindValue(':location_name', $location_name, PDO::PARAM_STR);
	$stmt_insert_location->bindValue(':location_address', $location_address, PDO::PARAM_STR);
	//$stmt_insert_location->execute();

	// Getting location_id from inserting location into table
	$location_id = $con->lastInsertId();

	// Checking for duplicate scheduled events
	$query = "SELECT COUNT(*) AS `count` FROM `program_relation_table` WHERE `program_id` = :program_id AND `location_id` = :location_id AND `date` = :date AND `start_time` = :start_time AND `end_time` = :end_time;";
	$stmt_insert_schedule = $con->prepare($query);
	$stmt_insert_schedule->bindParam(':program_id', $program_id, PDO::PARAM_STR);
	$stmt_insert_schedule->bindParam(':location_id', $location_id, PDO::PARAM_STR);
	$stmt_insert_schedule->bindParam(':date', $date, PDO::PARAM_STR);
	$stmt_insert_schedule->bindParam(':start_time', $start_time, PDO::PARAM_STR);
	$stmt_insert_schedule->bindParam(':end_time', $end_time, PDO::PARAM_STR);

	/*
	for($schedule_count = 0; $schedule_count < $event_repeat; $schedule_count++)
	{

	}
	*/
}
?>

<?php
/**
 * Create Event Action Section (used to display modal)
 */
// Event is not a duplicate, create the event
if(isset($_POST['create_event']))
{
	// Variables to keep track of how many arrival times made it in that were supposed to
	$arrival_times = 0;
	$arrival_times_success = 0;
	$arrival_times_array = array();
	$arrival_time_roles = array();

	if(!$result['count'])
	{

		$query = "INSERT INTO `program_table` (`program_name`, `program_type_id`) VALUES (:program_name, :program_type_id);";
		$stmt_create_event = $con->prepare($query);
		$stmt_create_event->bindValue(':program_name', $program_name, PDO::PARAM_STR);
		$stmt_create_event->bindValue(':program_type_id', $program_type_id, PDO::PARAM_STR);
		$stmt_create_event->execute();

		// Grab program_id for inserting arrival times
		$arrival_program_id = $con->lastInsertId();

		// if insertion was successful, move toward inserting the arrival times
		if($stmt_create_event->rowCount())
		{
			// Re-execute select query in order to grab new info
			$stmt_dup->execute();
			$result_program_name = $stmt_dup->fetch();

			// Setting up query for inserting arrival (executed in loop below)
			$query = "INSERT INTO `arrival_time_table` (`role_id`, `program_id`, `arrival_time`) VALUES (:role_id, :program_id, :arrival_time);";
			$stmt_insert_arrival = $con->prepare($query);
			$stmt_insert_arrival->bindParam(':role_id', $role_id, PDO::PARAM_STR);
			$stmt_insert_arrival->bindValue(':program_id', $arrival_program_id, PDO::PARAM_STR);
			$stmt_insert_arrival->bindParam(':arrival_time', $arrival_time, PDO::PARAM_STR);

			// Walk through the role_table and use role_id's to go through arrival_time arrays
			while($result = $stmt_role_arrival->fetch())
			{
				// Only set an arrival time for the role if it has been set
				if($arrival_hour[$result['role_id']] && $arrival_minute[$result['role_id']] && $arrival_ampm[$result['role_id']])
				{
					// Count as arrival time that user entered
					$arrival_times++;

					// Create arrival time format for going into database
					$arrival_time_obj = new DateTime($arrival_hour[$result['role_id']] . ":" . $arrival_minute[$result['role_id']] . " " . $arrival_ampm[$result['role_id']]);
					$arrival_time = $arrival_time_obj->format("H:i:00");

					// Move $result['role_id'] to new variable for PDO
					$role_id = $result['role_id'];

					// Put it into the database
					$stmt_insert_arrival->execute();

					// Check to see if it made it in
					if($stmt_insert_arrival->rowCount())
					{
						// Successful entry of arrival time
						$arrival_times_success++;

						// Put roles and then arrival time in array for print out
						$arrival_time_roles[] = $result['role_name'];
						$arrival_times_array[] = $arrival_time_obj->format("g:i A");
					}
				}
			}

			// Check to see if the number of arrival times user entered match the number that made it into the database
			if($arrival_times == $arrival_times_success && count($arrival_time_roles) == count($arrival_times_array))
			{
				// Echo success message
				// Event has been created at this point
				echo "
    <p>
      You have created " . $result_program_name['program_name'] . "
      with the following arrival times
    </p>
    <ul>\n";
				// Print out each arrival time
				for($success_count = 0; $success_count < count($arrival_time_roles); $success_count++)
				{
					echo "
      <li>" . $arrival_time_roles[$success_count] . ": " . $arrival_times_array[$success_count] . "</li>";
					}

				echo "    </ul>\n";
			}
			// Arrival times were not set correctly, delete event that was successful
			else
			{
				$query = "DELETE FROM `program_table` WHERE `program_id` = :program_id;";
				$stmt_delete_event = $con->prepare($query);
				$stmt_delete_event->bindValue(':program_id', $arrival_program_id, PDO::PARAM_STR);
				$stmt_delete_event->execute();

				echo "Arrival times could not be set.  No event was created.";
			}
		}
		else
		{
			echo "The event could not be created, please contact a member of the IT team for assistance.";
		}
	}
	else
	{
		echo "This event has already been created";
	}
}
?>