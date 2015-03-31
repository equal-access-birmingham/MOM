<?php
$require_login = true;
$require_verified_account = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");

$fname = $_GET['fname'];
$lname = $_GET['lname'];
$date = $_GET['year'] . "-" . $_GET['month'] . "-" . $_GET['day'];
$program = $_GET['program'];

try
{
	$db_connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
}
/**
 * Query for signup table info
 * Selects (listed are only the important ones)
 ** signup_id
 ** fname
 ** lname
 ** user_email
 ** phone_number
 ** program_id
 ** program_name
 ** date
 ** role_id
 ** role_name
 */
$current_date = new DateTime("now");
$query = "SELECT `program_name_info`.*, `users`.`user_email`
	FROM (
		SELECT `program_info`.*, `program_table`.`program_name`
			FROM (
				SELECT `people_info`.*, `program_relation_table`.`program_id`, `program_relation_table`.`date`
					FROM (
						SELECT `person_info`.*, `person_table`.`fname`, `person_table`.`lname`, `person_table`.`phone_number`
							FROM (
								SELECT `signup_info`.*, `login_relation_table`.`person_id`, `login_relation_table`.`user_id`
									FROM(
										SELECT `signup_table`.*, `role_table`.`role_name`
											FROM `signup_table`
											INNER JOIN `role_table`
											ON `role_table`.`role_id` = `signup_table`.`role_id`
									) AS `signup_info`
									INNER JOIN `login_relation_table`
									ON `signup_info`.`login_relation_id` = `login_relation_table`.`login_relation_id`
							) AS `person_info`
							INNER JOIN `person_table`
							ON `person_table`.`person_id` = `person_info`.`person_id`
					) AS `people_info`
					INNER JOIN `program_relation_table`
					ON `program_relation_table`.`program_relation_id` = `people_info`.`program_relation_id`
			) AS `program_info`
			INNER JOIN `program_table`
			ON `program_info`.`program_id` = `program_table`.`program_id`
	) AS `program_name_info`
	INNER JOIN `users`
	ON `program_name_info`.`user_id` = `users`.`user_id`
	WHERE `program_name_info`.`fname` COLLATE UTF8_GENERAL_CI LIKE :fname AND `program_name_info`.`lname` COLLATE UTF8_GENERAL_CI LIKE :lname AND DATE(`program_name_info`.`date`) >= :current_date";

/**
 * Setting limitations for dropdowns dynamically because these use number and can't use SQL LIKE
 */

// Adding date limitation
if($_GET['year'] && $_GET['month'] && $_GET['day'])
{
	$query .= " AND `program_name_info`.`date` = :date";
}

// Adding program limitation
if($program)
{
	$query .= " AND `program_name_info`.`program_id` = :program_id";
}

// Complete query with ordering by last name first and then date
$query .= " ORDER BY `program_name_info`.`date` ASC, `program_name_info`.`lname` ASC";

$stmt_signup_table = $db_connection->prepare($query);
$stmt_signup_table->bindValue(':fname', $fname."%", PDO::PARAM_STR);
$stmt_signup_table->bindValue(':lname', $lname."%", PDO::PARAM_STR);
$stmt_signup_table->bindValue(':current_date', $current_date->format("Y-m-d"), PDO::PARAM_STR);

// Binding values as needed based on the above query statement
if($_GET['year'] && $_GET['month'] && $_GET['day'])
{
	$stmt_signup_table->bindValue(':date', $date, PDO::PARAM_STR);
}
if($program)
{
	$stmt_signup_table->bindValue(':program_id', $program, PDO::PARAM_STR);
}
$stmt_signup_table->execute();

echo "
          <tr class=\"bg-green\">
            <th class=\"text-center\">First Name</th>
            <th class=\"text-center\">Last Name</th>
            <th class=\"text-center\">Email</th>
            <th class=\"text-center\">Clinic</th>
            <th class=\"text-center\">Date</th>
            <th class=\"text-center\">Role</th>
          </tr>
";

// Array of colors to alternate days of schedule
$color_array = array("schedule-gold", "schedule-green");

// Counts the dates for use in changing row color
$date_cntr = 0;

// Print out table contents
while($result = $stmt_signup_table->fetch())
{
	$date = new DateTime($result['date']);

	// Increments $date_cntr and adds date row each time new date exists
	if($date != $old_date)
	{
		// Echo's date with color background for each new date
		echo "
          <tr class=\"" . $color_array[$date_cntr%count($color_array)] . " text-left\">
            <td colspan=\"6\"><strong>" . $date->format('F j, Y') . "</strong></td>
          </tr>";

		$date_cntr++;
	}


	echo "
          <tr>
            <td>" . $result['fname'] . "</td>
            <td>" . $result['lname'] . "</td>
            <td><a href=\"mailto:" . $result['user_email'] . "\">" . $result['user_email'] . "</a></td>
            <td>" . $result['program_name'] . "</td>
            <td>" . $date->format("F j, Y") . "</td>
            <td>" . $result['role_name'] . "</td>
          </tr>
	";

	// Saves the date used for comparison with new one to check if it has changed
	$old_date = $date;

}
?>