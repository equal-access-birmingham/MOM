<?php include('includes/header-require_login.php'); ?>

<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once("includes/header-require_login.php");

try
{
	$con = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
}

catch(PDOException $e)
{
	echo 'Connection failed: ' . $e->getMessage();
}

$user_id = $_SESSION['user_id'];

//put in $_SESSION['user_name'] after Welcome

	echo "
		<a href=\"index.php\">" . WORDING_HOME_PAGE . "</a>
		<a href=\"view.php\">" . WORDING_DATA_VIEW . "</a>
		<a href=\"entry_test3.php\">" . WORDING_EVENT_REGISTRATION . "</a>
		<a href=\"index.php?logout\">" . WORDING_LOGOUT . "</a>
    ";

echo "
  <h1>Hi, Welcome <h1>
  <h2>Volunteer Schedule</h2>";
$query=
"SELECT temp4.role_name, temp4.program_name, temp4.date, temp4.arrival_time, location_table.address
	FROM (
		SELECT arrival_time_table.arrival_time, temp3.role_name, temp3.program_name, temp3.date, temp3.location_id
			FROM(
				SELECT role_table.role_name, temp2.program_name, temp2.date, temp2.role_id, temp2.program_id, temp2.location_id
					FROM (
						SELECT program_table.program_name, temp1.role_id, temp1.date, temp1.program_id, temp1.location_id
							FROM (
								SELECT program_relation_table.program_id, program_relation_table.date, program_relation_table.location_id, temp.role_id
									FROM (
										SELECT signup_table.program_id, signup_table.role_id
											FROM `signup_table`
											JOIN `login_relation_table`
											ON signup_table.login_relation_id = login_relation_table.login_relation_id
											WHERE login_relation_table.user_id = :user_id
									) AS temp
									JOIN program_relation_table
									ON temp.program_id = program_relation_table.program_id
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

$stmt=$con->prepare ($query);
$stmt->bindValue (':user_id', $user_id, PDO::PARAM_STR);
$stmt->execute ();  
$i=1;
while($result = $stmt->fetch())
{

	$date_array = explode("-", $result['date']);
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

	$time = explode(":", $result['arrival_time']);

	if(intval($time[0]) > 12)
	{
		$hour = $time[0] - 12;
		$new_time = $hour . ":" . $time[1] . " PM";
	}
	else
	{
		$hour = $time[0];
		$new_time = $hour . ":" . $time[1] . " AM";
	}

	echo "
        <h3>Volunteer Time $i</h23
          <ul>
            <li>Role: " . $result['role_name']. "</li>
            <li>Program: " . $result['program_name'] . "</li>
            <li>Date: " . $new_date . "</li>
            <li>Arrival Time: " . $new_time . "</li>
            <li>Address: " . $result['address'] . "</li> 
          </ul>
          ";
	$i++;
}
?>
