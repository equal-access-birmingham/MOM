<?php
$require_login = true;
$require_admin = true;
$require_verified_account = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");

// Acquiring get variables
$fname = $_GET['fname'];
$lname = $_GET['lname'];
$officer = $_GET['officer'];

// Create a database connection or return error and terminate admin page
try
{
	$db_connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "ERROR: " . $e->getMessage();
	die();
}

/**
 * MySQL query of volunteer's fname, lname, and officer status
 * 
 * Selects
 ** First Name
 ** Last Name
 ** Phone Number
 ** Email
 ** Level
 ** Position
 ** Login_relation_id (identifies person)
 */
$query = "SELECT `user_info`.*, `users`.`user_email`
	FROM (
		SELECT `level_info`.*, `level_table`.`level_name`
			FROM (
				SELECT `level_id_info`.*, `school_relation_table`.`level_id`
					FROM (
						SELECT `position_info`.*, `position_table`.`position_name`
							FROM (
								SELECT `personal_info`.*, `position_relation_table`.`position_id`
									FROM (
										SELECT `person_table`.`fname`, `person_table`.`lname`, `person_table`.`phone_number`, `login_relation_table`.`login_relation_id`, `login_relation_table`.`user_id`
											FROM `person_table`
											INNER JOIN `login_relation_table`
											ON `person_table`.`person_id` = `login_relation_table`.`person_id`
									) AS `personal_info`
									LEFT JOIN `position_relation_table`
									ON `personal_info`.`login_relation_id` = `position_relation_table`.`login_relation_id`
							) AS `position_info`
							LEFT JOIN `position_table`
							ON `position_info`.`position_id` = `position_table`.`position_id`
					) AS `level_id_info`
					LEFT JOIN `school_relation_table`
					ON `level_id_info`.`login_relation_id` = `school_relation_table`.`login_relation_id`
			) AS `level_info`
			LEFT JOIN `level_table`
			ON `level_info`.`level_id` = `level_table`.`level_id`
	) AS `user_info`
	LEFT JOIN `users`
	ON `user_info`.`user_id` = `users`.`user_id`
	WHERE `user_info`.`fname` COLLATE UTF8_GENERAL_CI LIKE :fname AND `user_info`.`lname` COLLATE UTF8_GENERAL_CI LIKE :lname";

// Limiting queries with dropdown info from admin_officer_scheduler.php only if info is passed (LIKE can't be used with numbers that are sent from dropdown)
if($officer != "" && $officer != "none")
{
		$query .= " AND `user_info`.`position_id` = :position_id;";
}
else if($officer == "none")
{
	if($fname == "" && $lname =="")
	{
		$query .= " AND `user_info`.`position_id` = :position_id;";
	}
}

$stmt = $db_connection->prepare($query);
$stmt->bindValue(':fname', $fname."%", PDO::PARAM_STR);
$stmt->bindValue(':lname', $lname."%", PDO::PARAM_STR);

// Binding values as necessary
if($officer != "" && $officer != "none")
{
	$stmt->bindValue(':position_id', $officer, PDO::PARAM_STR);
}
else if($officer == "none")
{
	if($fname == "" && $lname =="") 
	{
		$stmt->bindValue(':position_id', $officer, PDO::PARAM_STR);
	}
}
$stmt->execute();

// build the table
echo "  
          <tr>
            <th class=\"text-center\">Select</th>
            <th class=\"text-center\">First Name</th>
            <th class=\"text-center\">Last Name</th>
            <th class=\"text-center\">Phone Number</th>
            <th class=\"text-center\">Email</th>
            <th class=\"text-center\">Level</th>
            <th class=\"text-center\">Position</th>
          </tr>
";

while($result = $stmt->fetch())
{
	$phone_number = str_replace("-", "&#8209;", $result['phone_number']);
	$phone_number = str_replace(" ", "&nbsp;", $phone_number);

	$replace_array = array("(", ")", " ", "-");
	$phone_number_link = str_replace($replace_array, "", $result['phone_number']);

	echo "
          <tr>
            <td><input type=\"radio\" name=\"login_relation_id\" value=\"" . $result['login_relation_id'] . "\" /></td>
            <td>" . $result['fname'] . "</td>
            <td>" . $result['lname'] . "</td>
            <td><a href=\"tel:$phone_number_link\">" . $phone_number . "</a></td>
            <td><a href=\"mailto:" . $result['user_email'] . "\">" . $result['user_email'] . "</a></td>
            <td>" . $result['level_name'] . "</td>
            <td>" . $result['position_name'] . "</td>
          </tr>
";   
}
?>