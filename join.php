<?php
require_once ("includes/header.php");

error_reporting(E_ALL);
ini_set("display_errors", 1);

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
// MySQL query of volunteer's fname, lname, and officer status
$query = "SELECT `position_info`.*, `position_table`.`position_name`
	FROM (
		SELECT `personal_info`.*, `position_relation_table`.`position_id`
			FROM (
				SELECT `person_table`.`fname`, `person_table`.`lname`, `login_relation_table`.`login_relation_id`
					FROM `person_table`
					INNER JOIN `login_relation_table`
					ON `person_table`.`person_id` = `login_relation_table`.`person_id`
			) AS `personal_info`
			INNER JOIN `position_relation_table`
			ON `personal_info`.`login_relation_id` = `position_relation_table`.`login_relation_id`
		) AS `position_info`
		INNER JOIN `position_table`
		ON `position_info`.`position_id` = `position_table`.`position_id`
		WHERE `position_info`.`fname` COLLATE UTF8_GENERAL_CI LIKE :fname AND `position_info`.`lname` COLLATE UTF8_GENERAL_CI LIKE :lname";

if($officer != "" && $officer != "none")
{
		$query .= " AND `position_info`.`position_id` = :position_id;";
}
else if($officer == "none")
{
	if($fname == "" && $lname =="")
	{
		$query .= " AND `position_info`.`position_id` = :position_id;";
	}
}

$stmt = $db_connection->prepare($query);
$stmt->bindValue(':fname', $fname."%", PDO::PARAM_STR);
$stmt->bindValue(':lname', $lname."%", PDO::PARAM_STR);
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
            <th>Select</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Position</th>
          </tr>
";

while($result = $stmt->fetch())
{
	echo "
          <tr>
            <td><input type=\"radio\" name=\"login_relation_id\" value=\"" . $result['login_relation_id'] . "\" /></td>
            <td>" . $result['fname'] . "</td>
            <td>" . $result['lname'] . "</td>
            <td>" . $result['position_name'] . "</td>
          </tr>
";   
}
?>
