<?php
$require_login = true;
$require_verified_account = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

    <title>My Schedule | Equal Access Birmingham</title>

<?php require_once($path_to_root . "includes/menu_mom.php");

// Sets up the database connection
try
{
	$con = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo 'Connection failed: ' . $e->getMessage();
}

// user_id from the Login() class
$user_id = $_SESSION['user_id'];

// MySQL query for full name of user based on $user_id
$query = "SELECT person_table.fname, person_table.lname, person_table.suffname
		FROM `person_table`
			JOIN `login_relation_table`
			ON person_table.person_id = login_relation_table.person_id
			WHERE login_relation_table.user_id = :user_id";

$stmt_user_full_name = $con->prepare ($query);
$stmt_user_full_name->bindValue (':user_id', $user_id, PDO::PARAM_STR);
$stmt_user_full_name->execute ();
$result = $stmt_user_full_name->fetch();

// MySQL query for user's schedule
// Captures all dates the user is signed up for and orders them in ascending order
$query = "SELECT `temp4`.`role_name`, `temp4`.`program_name`, `temp4`.`date`, `temp4`.`arrival_time`, `temp4`.`start_time`, `location_table`.`address`
	FROM (
		SELECT `arrival_time_table`.`arrival_time`, `temp3`.`role_name`, `temp3`.`program_name`, `temp3`.`date`, `temp3`.`location_id`, `temp3`.`start_time`
			FROM(
				SELECT `role_table`.`role_name`, `temp2`.`program_name`, `temp2`.`date`, `temp2`.`role_id`, `temp2`.`program_id`, `temp2`.`location_id`, `temp2`.`start_time`
					FROM (
						SELECT `program_table`.`program_name`, `temp1`.`role_id`, `temp1`.`date`, `temp1`.`program_id`, `temp1`.`location_id`, `temp1`.`start_time`
							FROM (
								SELECT `program_relation_table`.`program_id`, `program_relation_table`.`date`, `program_relation_table`.`location_id`, `program_relation_table`.`start_time`, `temp`.`role_id`
									FROM (
										SELECT `signup_table`.`program_relation_id`, `signup_table`.`role_id`
											FROM `signup_table`
											INNER JOIN `login_relation_table`
											ON `signup_table`.`login_relation_id` = `login_relation_table`.`login_relation_id`
											WHERE `login_relation_table`.`user_id` = :user_id
									) AS `temp`
									INNER JOIN `program_relation_table`
									ON `temp`.`program_relation_id` = `program_relation_table`.`program_relation_id`
							) AS `temp1`
							INNER JOIN `program_table`
							ON `temp1`.`program_id` = `program_table`.`program_id`
					) AS `temp2`
					INNER JOIN `role_table`
					ON `temp2`.`role_id` = `role_table`.`role_id`
			) AS `temp3`
			LEFT JOIN `arrival_time_table`
			ON `temp3`.`role_id` = `arrival_time_table`.`role_id` AND `temp3`.`program_id` = `arrival_time_table`.`program_id`
	) AS `temp4`
	LEFT JOIN `location_table`
	ON `temp4`.`location_id` = `location_table`.`location_id`
	ORDER BY `temp4`.`date` ASC;";
$stmt_user_schedule = $con->prepare ($query);
$stmt_user_schedule->bindValue (':user_id', $user_id, PDO::PARAM_STR);
$stmt_user_schedule->execute();
?>
    <div class="container no-image">

      <?php echo "<h1>Welcome, " . $result['fname'] . " " . $result['lname'] . " " . $result['suffname'] . "</h1>"; ?>

      <h2>My Schedule</h2>

<?php
// Counts events for echoing
$event_count = 1;

// Allows single execution to check if there is even one 
do
{
	// Only use this once to establish for first pass
	// While loop takes over after that
	if($event_count == 1)
	{
		$result = $stmt_user_schedule->fetch();
	}

	// If the user has no scheduled events, inform them that they need to sign up
	if(!$result && $event_count == 1)
	{
		echo "
      <p>
        You are not currently scheduled for any events.  Go to 
        <a href=\"/mom/index.php\">Sign Up</a> under Volunteers to sign up for a 
        volunteer spot with EAB.
      </p>\n";

	}
	// Otherwise, print out their schedule (can't use date as that is always populated with the current if it is null)
	else if($result['role_name'])
	{
		/**
		 * Sets up date and time in separate objects for easy format (format below in echo)
		 * Date --> "F j, Y" gives "January 1, 2013"
		 * Time --> "g:i A" gives "1:30 PM"
		 */
		$date = new DateTime($result['date']);
		$time = new DateTime($result['arrival_time']);

		// In case an arrival time has not been set for a role
		$time_start = new DateTime($result['start_time']);
		
		echo "
      <h3>Volunteer Time $event_count</h3>
      <ul>
        <li>Role: " . $result['role_name']. "</li>
        <li>Program: " . $result['program_name'] . "</li>
        <li>Date: " . $date->format("F j, Y") . "</li>\n";

		// If an arrival time is available, use it
		if($result['arrival_time'])
		{
			echo "
        <li>Arrival Time: " . $time->format("g:i A") . "</li>\n";
		}
		// If it is not, just use the program start time
		else
		{
			echo "
        <li>Arrival Time: " . $time_start->format("g:i A") . "</li>\n";
		}

		echo "
        <li>Address: " . $result['address'] . "</li> 
      </ul>\n";
		$event_count++;
	}
} while($result = $stmt_user_schedule->fetch());

?>
    </div>

<?php require_once($path_to_root . "includes/footer.php"); ?>