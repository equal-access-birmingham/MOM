<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
//ini_set('display_startup_errors', TRUE);
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

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);

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

// load the login class
require_once($path_to_root . 'php-login-admin/classes/Login.php');

// load the permissions class
require_once($path_to_root . 'php-login-admin/classes/Permissions.php');

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process.
$login = new Login();

// create a permissions object. it will handle permission checking and changing for the accounts
$permissions = new Permissions();

// Method of requiring login on a page
if($login->isUserLoggedIn() == false && isset($require_login))
{

	header("Location: /index.php?logout");
	exit();
}

// Method or requiring no login on a page
if($login->isUserLoggedIn() == true && isset($require_no_login))
{
	header("Location: /index.php");
	exit();
}

// Method of requiring admin on a page
if($permissions->isUserAdmin() == false && isset($require_admin))
{
	header("Location: /index.php?logout");
	exit();
}

// Sends logout signal and incorrect password signal
// Needed for those that click directly on MOM link as it will do a redirect with incorrect login with no information sent without $_GET variables placed
if($_POST['login'] && $login->isUserLoggedIn() == false)
{
	header("Location: /index.php?logout&incorrectLogin");
	exit();
}

// Redirects user to the verify page if their account is not verified (header does not work if user is on the verify page to prevent redirect loop)
// An unverified user has no choice but to verify account upon login as no other page will work without verification
if($login->isUserLoggedIn() == true && $login->isUserVerified() == false && !isset($verify_page))
{
	header("Location: /account/verify.php");
	exit();
}


?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="A free, student-run clinic serving the Birmingham community">