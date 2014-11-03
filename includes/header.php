<?php

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

require_once('phploginadvanced/classes/Login.php');
require_once('phploginadvanced/classes/Permissions.php');

$login = new Login();
$permission = new Permissions();
?>
<?php
// show potential errors / feedback (from login object)
if (isset($login)) {
    if ($login->errors) {
        foreach ($login->errors as $error) {
            echo $error;
        }
    }
    if ($login->messages) {
        foreach ($login->messages as $message) {
            echo $message;
        }
    }
}
?>

<?php
// show potential errors / feedback (from registration object)
if (isset($registration)) {
    if ($registration->errors) {
        foreach ($registration->errors as $error) {
            echo $error;
        }
    }
    if ($registration->messages) {
        foreach ($registration->messages as $message) {
            echo $message;
        }
    }
}
?>

<?php
// show potential errors / feedback (from registration object)
if (isset($permission)) {
    if ($permission->errors) {
        foreach ($permission->errors as $error) {
            echo $error;
        }
    }
    if ($permission->messages) {
        foreach ($permission->messages as $message) {
            echo $message;
        }
    }
}
?>
