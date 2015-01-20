<?php
$require_login = true;
$require_admin = true;
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
	WHERE `program_name_info`.`fname` COLLATE UTF8_GENERAL_CI LIKE :fname AND `program_name_info`.`lname` COLLATE UTF8_GENERAL_CI LIKE :lname";

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
          <tr>
            <th class=\"text-center\">Select</th>
            <th class=\"text-center\">First Name</th>
            <th class=\"text-center\">Last Name</th>
            <th class=\"text-center\">Email</th>
            <th class=\"text-center\">Phone Number</th>
            <th class=\"text-center\">Clinic</th>
            <th class=\"text-center\">Date</th>
            <th class=\"text-center\">Role</th>
          </tr>
";

while($result = $stmt_signup_table->fetch())
{
	$date = new DateTime($result['date']);

	$phone_number = str_replace("-", "&#8209;", $result['phone_number']);
	$phone_number = str_replace(" ", "&nbsp;", $phone_number);

	$replace_array = array("(", ")", " ", "-");
	$phone_number_link = str_replace($replace_array, "", $result['phone_number']);
	
	echo "
          <tr>
            <td><input type=\"checkbox\" name=\"delete_schedule[]\" value=\"" . $result['signup_id'] . "\" /></td>
            <td>" . $result['fname'] . "</td>
            <td>" . $result['lname'] . "</td>
            <td><a href=\"mailto:" . $result['user_email'] . "\">" . $result['user_email'] . "</a></td>
            <td><a href=\"tel:$phone_number_link\">" . $phone_number . "</a></td>
            <td>" . $result['program_name'] . "</td>
            <td>" . $date->format("F j, Y") . "</td>
            <td>" . $result['role_name'] . "</td>
          </tr>
";
}
?>