<?php
require('includes/header-require_admin.php');
$action = $_GET['eventcreation'];
$program_id = $_GET['program_id'];
$date = $_GET['date'];
echo "$date";
try 
{
	$con=new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
	die();
}

?>
