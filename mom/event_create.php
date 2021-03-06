<?php
/**
 * Warning: this file requires a database modification as shown below
 ** Move from program_relation_table
 *** start_time
 *** end_time
 *** location_id
 ** To program_relation_table
 *
 * Also, queries in both view.php and admin_event_manage.php will have to be modified (unless a database access layer is in place)
 *
 * File modifications
 ** Schedule event on top (Event create below)
 ** Move event time to event create part
 ** Make this a DAO
 */
$require_login = true;
$require_admin = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Setting up database connection
try
{
	$con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
}

// Query for program types for dropdown
$query = "SELECT * FROM `program_type_table`;";
$stmt_program_type = $con->prepare($query);
$stmt_program_type->execute();

// Query for programs for dropdown
$query = "SELECT * FROM `program_table`;";
$stmt_program = $con->prepare($query);
$stmt_program->execute();

// Query for roles for everything
$query = "SELECT * FROM `role_table`;";
$stmt_role = $con->prepare($query);
$stmt_role->execute();
?>

<html>
  <head>
    <title>Event Scheduling | Equal Access Birmingham </title>
  </head>
  <body>
    <div class="container no-image">
      <h1>Event Scheduling</h1>
      <h3>Create Event</h3>
      <form action="event_create_do.php" method="post" role="form">
        <!--  Event Name -->
        <label for="program_name">Event Name</label>
        <input type="text" id="program_name" name="program_name" required />

        <label for="program_type">Event Type</label>
        <select type="text" id="program_type" name="program_type_id" required >
          <option value="">-- Event Type --</option>
<?php
while($result = $stmt_program_type->fetch())
{
	echo "          <option value=\"" . $result['program_type_id'] . "\">" . $result['program_type'] . "</option>\n";
}
?>
        </select>

<?php
// A PHP trick to get all role almost instantly that I need almost instantly
while($result = $stmt_role->fetch())
{
	echo "
        <!-- Arrival time for each role -->
        <label for=\"" . $result['role_name'] . "\">" . $result['role_name'] . " Arrival Time</label>

        <!-- Hour  -->
        <select id=\"" . $result['role_name'] . "_hour\" name=\"arrival_hour[" . $result['role_id'] . "]\">
          <option value=\"\">-- Hour --</option>\n";

	for($hour = 1; $hour < 13; $hour++)
	{
		echo "          <option value=\"$hour\">$hour</option>\n";
	}

		echo"
        </select>

        <!-- Minute -->
        <select id=\"" . $result['role_name'] . "_minute\" name=\"arrival_minute[" . $result['role_id'] . "]\">
          <option value=\"\">-- Minute --</option>\n";

	for($minute = 0; $minute < 60; $minute++)
	{
		// Ensures that minutes always have leading zero
		$minute = sprintf("%02d", $minute);
		echo "          <option value=\"$minute\">$minute</option>\n";
	}

	echo "
        </select>

        <!-- AM/PM -->
        <select id=\"arrival_ampm\" name=\"arrival_ampm[" . $result['role_id'] ."]\">
          <option value=\"\">-- AM/PM --</option>
          <option value=\"AM\">AM</option>
          <option value=\"PM\">PM</option>
        </select><br />\n";
}
?>

        <input type="submit" name="create_event" value="Create Event" />
      </form><hr />

      <h3>Schedule Event</h3>
      <form action="event_create_do.php" method="post" role="form">
        <!--
          event
          event day
          event repeat
          start time
          end time
          role arrival time (don't require this, and don't set it on the php side if it isn't given)
        -->
        <!-- Event Name -->
        <label for="program_name">Event</label>
        <select id="program_id" name="program_id" required>
          <option value="">-- Event --</option>
<?php
while($result = $stmt_program->fetch())
{
	echo "          <option value=\"" . $result['program_id'] . "\">" . $result['program_name'] . "</option>\n";
}
?>
        </select>

        <!-- Location Name -->
        <label for="location_name">Location Name</label>
        <input type="text" id="location_name" name="location_name" required />

        <!-- Location Address -->
        <label for="location_address">Location Address</label>
        <input type="text" id="location_address" name="location_address" required />

        <!-- Event Start Date -->
        <label>Start Date</label>

        <!-- Month -->
        <select id="start_month" name="start_month">
          <option value="">-- Month --</option>

<?php
// Creates the months of the year
// Day is necessary as the day will be pulled from the current date otherwise
// (results in skipping the month if the current day does not exist for selected month)
$months = new DateTime("January 1");
for($i = 1; $i < 13; $i++)
{
	echo "          <option value=\"" . $months->format("n") . "\">" . $months->format("F") . "</option>\n";
	$months->modify("next month");
}
?>
        </select>

        <!-- Day -->
        <select id="start_day" name="start_day">
          <option value="">-- Day --</option>
<?php
for($month_day = 1; $month_day < 32; $month_day++)
{
	echo "          <option value=\"$month_day\">$month_day</option>\n";
}
?>
        </select>

        <!-- Year -->
        <select id="start_year" name="start_year">
          <option value="">-- Year --</option>
<?php
// Establish year dropdown
// Allows viewing of schedule from last year and 4 years in the future
$years = new DateTime("this year");
for($i = 1; $i < 6; $i++)
{
	echo "                  <option value=\"" . $years->format("Y") . "\">" . $years->format("Y") . "</option>\n";
	$years->modify("next year");
}
?>

        </select>


        <!-- Event Day -->
        <label for="event_day">Event Day</label>
        <select id="event_day" name="event_day" required>
          <option value="">-- Day --</option>
<?php
// Array of days to print
$days = array(
	"Sunday",
	"Monday",
	"Tuesday",
	"Wednesday",
	"Thursday",
	"Friday",
	"Saturday",
);

foreach($days as $day)
{
	echo "          <option value=\"$day\">$day</option>\n";
}
?>
        </select>

        <!-- Number of times to repeat event -->
        <label for="event_repeat">Event Times Repeats</label>
        <input type="number" name="event_repeat" required />

        <!-- Start Time -->
        <label>Start Time</label>
        
        <!-- Hour -->
        <select id="start_hour" name="start_hour">
          <option value="">-- Hour --</option>
<?php
for($hour = 1; $hour < 13; $hour++)
{
	echo "            <option value=\"$hour\">$hour</option>\n";
}
?>
        </select>

        <!-- Minute -->
        <select id="start_minute" name="start_minute">
          <option value="">-- Minute --</option>

<?php
for($minute = 0; $minute < 60; $minute++)
{
	// Ensures that minutes always have leading zero
	$minute = sprintf("%02d", $minute);
	echo "            <option value=\"$minute\">$minute</option>\n";
}
?>
        </select>

        <!-- AM/PM -->
        <select id="start_ampm" name="start_ampm">
          <option value="">-- AM/PM --</option>
          <option value="AM">AM</option>
          <option value="PM">PM</option>
        </select>

        <!-- End Time -->
        <label>End Time</label>
        
        <!-- Hour -->
        <select id="end_hour" name="end_hour">
          <option value="">-- Hour --</option>
<?php
for($hour = 1; $hour < 13; $hour++)
{
	echo "            <option value=\"$hour\">$hour</option>\n";
}
?>
        </select>

        <!-- Minute -->
        <select id="end_minute" name="end_minute">
          <option value="">-- Minute --</option>
<?php
for($minute = 0; $minute < 60; $minute++)
{
	// Ensures that minutes always have leading zero
	$minute = sprintf("%02d", $minute);
	echo "            <option value=\"$minute\">$minute</option>\n";
}
?>
        </select>

        <!-- AM/PM -->
        <select id="end_ampm" name="end_ampm">
          <option value="">-- AM/PM --</option>
          <option value="AM">AM</option>
          <option value="PM">PM</option>
        </select>

<?php
// A PHP trick to get all role almost instantly that I need almost instantly
while($result = $stmt_role->fetch())
{
	echo "
        <!-- Arrival time for each role -->
        <label for=\"" . $result['role_name'] . "\">" . $result['role_name'] . " Arrival Time</label>\n";

	echo "
        <!-- Hour  -->
        <select id=\"" . $result['role_name'] . "_hour\" name=\"arrival_hour[" . $result['role_name'] . "]\">
          <option value=\"\">-- Hour --</option>\n";

	for($hour = 1; $hour < 13; $hour++)
	{
		echo "          <option value=\"$hour\">$hour</option>\n";
	}

		echo"
        </select>

        <!-- Minute -->
        <select id=\"" . $result['role_name'] . "_minute\" name=\"arrival_minute[" . $result['role_name'] . "]\">
          <option value=\"\">-- Minute --</option>\n";

	for($minute = 0; $minute < 60; $minute++)
	{
		// Ensures that minutes always have leading zero
		$minute = sprintf("%02d", $minute);
		echo "          <option value=\"$minute\">$minute</option>\n";
	}

	echo "
        </select>

        <!-- AM/PM -->
        <select id=\"arrival_ampm\" name=\"arrival_ampm[" . $result['role_name'] ."]\">
          <option value=\"\">-- AM/PM --</option>
          <option value=\"AM\">AM</option>
          <option value=\"PM\">PM</option>
        </select><br />\n";
}
?>
        <input type="submit" name="schedule_event" value="Schedule Event" />
      </form>
    </div>
  </body>
</html>