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
require_once($path_to_root . 'php-login-admin/classes/Login.php');

// create a registration object.  it will handle all registration necessary
$login = new Login();

/* ================================================================================
 * dead_acc_rem.php
 * ================================================================================
 * Automatically removes unverified accounts that have expired from the database
 * This file can only be run from the command line 
 * ================================================================================*/

/**
 * Begin actual file contents
 */

// Inform the user of what the file will do and ask them if they want to execute it
fwrite(STDOUT, "This file will remove all unverified, expired accounts from the database.\n");
$answer = readline("Are you sure you want to continue? (y/n): ");
$answer = strtolower($answer);

if($answer != "y" && $answer != "yes")
{
	fwrite(STDOUT, "Script has been terminated!\n");
	exit;
}

// Begin removing unverified, expired users

// Attempt connection to the database
try {
	$con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e) {
	echo "Error: " . $e->getMessage();
}

// Query for all the users in the database
$query = "SELECT `user_name` FROM `users`;";
$stmt_users = $con->prepare($query);
$stmt_users->execute();

// Query for deleting users that have expired
$query = "DELETE FROM `users` WHERE `user_name` = :user_name;";
$stmt_delete_expired = $con->prepare($query);
$stmt_delete_expired->bindParam(':user_name', $user_name, PDO::PARAM_STR);

// Account deletion count
$delete_cnt = 0;

// Running through all the users to check for expired accounts and removing them
while($result = $stmt_users->fetchObject())
{
	// Determing if account is expired and deleting (execute delete statement from above)
	if($login->isAccountExpired($result->user_name) == true)
	{
		$user_name = $result->user_name;
		$stmt_delete_expired->execute();
		if($stmt_delete_expired->rowCount())
		{
			fwrite(STDOUT, "The user $user_name was deleted\n");
			$delete_cnt++;
		}
		else
		{
			fwrite(STDOUT, "The user $user_name was UNABLE to be deleted\n");
		}
	}
}

// If no accounts were deleted, let the user know
if($delete_cnt == 0)
{
	fwrite(STDOUT, "All accounts were either verified or not expired.  No accounts were deleted.\n");
}