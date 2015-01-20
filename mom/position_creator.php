#!/usr/bin/php5
<?php
/**
 * A simple PHP Login Script / ADVANCED VERSION
 * For more versions (one-file, minimal, framework-like) visit http://www.php-login.net
 *
 * @author Panique
 * @link http://www.php-login.net
 * @link https://github.com/panique/php-login-advanced/
 * @license http://opensource.org/licenses/MIT MIT License
 */

/**
 * $path_to_root is a var that goes back to the root of the server based on the current location
 * This var is present in all files that need it as well so that it can be excluded where not needed (files in the root)
 */

$path_to_root = "../";

// check for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit('Sorry, this script does not run on a PHP version smaller than 5.3.7 !');
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once($path_to_root . 'php-login-admin/libraries/password_compatibility_library.php');
}
// include the config
require_once($path_to_root . 'php-login-admin/config/config.php');

// include the to-be-used language, english by default. feel free to translate your project and include something else
require_once($path_to_root . 'php-login-admin/translations/en.php');

// include the PHPMailer library
require_once($path_to_root . 'php-login-admin/libraries/PHPMailer.php');

// Load the registration class
require_once($path_to_root . 'php-login-admin/classes/Registration.php');

// create a registration object.  it will handle all registration necessary
$registration = new Registration();

/**
 * Begin actual file contents
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

fwrite(STDOUT, "This file will set all verified accounts that don't already have a role as \"Volunteers\"\n");
$confirm = readline("Run this file (y/n)? ");
$confirm = trim(strtolower($confirm));

// If user wants to run the file
if($confirm == "y" || $confirm == "yes")
{
	// Set up database connection
	try
	{
		$con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
	}
	catch(PDOException $e)
	{
		echo "Error: " . $e->getMessage();
	}

	// Query to count positions for each login_relation_id (verified user) (i.e. check if a position exists for verified user)
	// Also returns the first and last name for message printout
	$query = "SELECT `verify_info`.*, `person_table`.`fname`, `person_table`.`lname`
				FROM (
					SELECT `login_relation_table`.`login_relation_id`, `login_relation_table`.`person_id`, COUNT(`position_relation_table`.`position_relation_id`) AS `position_count`
						FROM `login_relation_table`
						LEFT JOIN `position_relation_table`
						ON `login_relation_table`.`login_relation_id` = `position_relation_table`.`login_relation_id`
						GROUP BY `login_relation_table`.`login_relation_id`
				) AS `verify_info`
				INNER JOIN `person_table`
				ON `verify_info`.`person_id` = `person_table`.`person_id`;";
	$stmt_position_exist = $con->prepare($query);
	$stmt_position_exist->execute();

	// Query for position_id of Volunteer
	$query = "SELECT * FROM `position_table` WHERE `position_name` = 'Volunteer';";
	$stmt_volunteer = $con->prepare($query);
	$stmt_volunteer->execute();
	$result_volunteer = $stmt_volunteer->fetchObject();

	// Assign position_id to new variable for insert statement
	$position_id = $result_volunteer->position_id;

	// Query to create volunteer positions
	$query = "INSERT INTO `position_relation_table` (`login_relation_id`, `position_id`) VALUES (:login_relation_id, :position_id);";
	$stmt_insert_position = $con->prepare($query);
	$stmt_insert_position->bindParam(':login_relation_id', $login_relation_id, PDO::PARAM_STR);
	$stmt_insert_position->bindValue(':position_id', $position_id, PDO::PARAM_STR);
	
	while($result_position_exist = $stmt_position_exist->fetchObject())
	{
		$login_relation_id = $result_position_exist->login_relation_id;

		// If there is no position for the login_relation_id
		if($result_position_exist->position_count == 0)
		{
			$stmt_insert_position->execute();
			if($stmt_insert_position->rowCount())
			{
				fwrite(STDOUT, "$result_position_exist->fname $result_position_exist->lname was made a volunteer.\n");
			}
			else
			{
				fwrite(STDOUT, "Error: could not make $result_position_exist->fname $result_position_exist->lname a volunteer as needed.\n");
			}
		}
		else
		{
			
		}
	}

}
else
{
	fwrite(STDOUT, "Exiting script\n");
}
?>