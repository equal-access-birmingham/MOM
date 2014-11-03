<?php

// check for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit('Sorry, this script does not run on a PHP version smaller than 5.3.7 !');
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once('phploginadvanced/libraries/password_compatibility_library.php');
}
// include the config
require_once('phploginadvanced/config/config.php');

// include the to-be-used language, english by default. feel free to translate your project and include something else
require_once('phploginadvanced/translations/en.php');

// include the PHPMailer library
require_once('phploginadvanced/libraries/PHPMailer.php');

// load the login class
require_once('phploginadvanced/classes/Login.php');

// load the permissions class
require_once('phploginadvanced/classes/Permissions.php');



// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process.
$login = new Login();

$permission = new Permissions();


// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == false && $permission->isUserAdmin() == false) {
    // the user is logged in and is admin. you can do whatever you want here.
    // this page is demo of the admin functions
    header("Location: index.php?logout");
}
?>