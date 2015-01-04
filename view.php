<?php
$require_login = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

    <title>My Schedule | Equal Access Birmingham</title>

<?php require_once($path_to_root . "includes/menu.php");

// Sets up the database connection
try
{
	$con = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo 'Connection failed: ' . $e->getMessage();
}
	echo "
    <a href=\"index.php\">" . WORDING_HOME_PAGE . "</a>
    <a href=\"view.php\">" . WORDING_DATA_VIEW . "</a>
    <a href=\"entry_test3.php\">" . WORDING_EVENT_REGISTRATION . "</a>
    <a href=\"index.php?logout\">" . WORDING_LOGOUT . "</a>
	";
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
// Captures all dates the user is signed up for
$query = "SELECT temp4.role_name, temp4.program_name, temp4.date, temp4.arrival_time, location_table.address
	FROM (
		SELECT arrival_time_table.arrival_time, temp3.role_name, temp3.program_name, temp3.date, temp3.location_id
			FROM(
				SELECT role_table.role_name, temp2.program_name, temp2.date, temp2.role_id, temp2.program_id, temp2.location_id
					FROM (
						SELECT program_table.program_name, temp1.role_id, temp1.date, temp1.program_id, temp1.location_id
							FROM (
								SELECT program_relation_table.program_id, program_relation_table.date, program_relation_table.location_id, temp.role_id
									FROM (
										SELECT signup_table.program_relation_id, signup_table.role_id
											FROM `signup_table`
											JOIN `login_relation_table`
											ON signup_table.login_relation_id = login_relation_table.login_relation_id
											WHERE login_relation_table.user_id = :user_id
									) AS temp
									JOIN program_relation_table
									ON temp.program_relation_id = program_relation_table.program_id
							) AS temp1
							JOIN program_table
							ON temp1.program_id = program_table.program_id
					) AS temp2
					JOIN role_table
					ON temp2.role_id = role_table.role_id
			) AS temp3
			JOIN arrival_time_table
			ON temp3.role_id = arrival_time_table.role_id AND temp3.program_id = arrival_time_table.program_id
	)AS temp4
	JOIN location_table
	ON temp4.location_id = location_table.location_id";
$stmt_user_schedule = $con->prepare ($query);
$stmt_user_schedule->bindValue (':user_id', $user_id, PDO::PARAM_STR);
$stmt_user_schedule->execute();
?>
    <div class="container no-image">

      <?php echo "<h1>Hi, Welcome " . $result['fname'] . " " . $result['lname'] . " " . $result['suffname'] . "</h1>"; ?>

      <h2>Volunteer Schedule</h2>

<?php
$event_count = 1;
while($result = $stmt_user_schedule->fetch())
{
	/**
	 * Sets up date and time in separate objects for easy format (format below in echo)
	 * Date --> "F j, Y" gives "January 1, 2013"
	 * Time --> "g:i A" gives "1:30 PM"
	 */
	$date = new DateTime($result['date']);
	$time = new DateTime($result['arrival_time']);
	
	echo "
      <h3>Volunteer Time $event_count</h3>
      <ul>
        <li>Role: " . $result['role_name']. "</li>
        <li>Program: " . $result['program_name'] . "</li>
        <li>Date: " . $date->format("F j, Y") . "</li>
        <li>Arrival Time: " . $time->format("g:i A") . "</li>
        <li>Address: " . $result['address'] . "</li> 
      </ul>\n";
	$event_count++;
}
?>
    </div>

<?php require_once($path_to_root . "includes/footer.php"); ?>
