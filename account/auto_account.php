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

// Send messages to user and get input back
fwrite(STDOUT, "This file requires a list of emails, each on a new line\n");
$emails = readline("Please indicate the path and file that contains the emails: ");

// Prep file for reading
$email_file = fopen($emails, "r") or die("Error: this file can't be opened!\n");

$line_cntr = 0;

while($email = fgets($email_file))
{
	$line_cntr++;
	$email = trim($email);

	// error check the line to make sure that it contains ONE, VALID email

	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		fwrite(STDOUT, "Line $line_cntr does not contain a valid email: $email\n");
	}
	else
	{
		$registration->registerNewUser($email);

		// print any messages from the registration object
		if($registration->messages)
		{
			foreach($registration->messages as $message)
			{
				fwrite(STDOUT, $message . "\n");
				$registration->messages = array();
			}
		}

		// print any errors associated with the login system
		if($registration->errors)
		{
			foreach($registration->errors as $error)
			{
				fwrite(STDOUT, $email . ": " . $error . "\n");
				$registration->errors = array();
			}
		}
		else
		{
			fwrite(STDOUT, "Successfully registered $email\n");
		}
	}
}

fclose($email_file);
?>
