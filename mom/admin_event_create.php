<?php
$require_login = true;
$require_admin = true;
$require_verified_account = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

    <title>Create Event | Equal Access Birmingham</title>

<?php require_once($path_to_root . "includes/menu_sign_in.php");?>

<?php
/**
 * Clinic Event Setup
 */

// Get variables to be filled every time
$program_type_id = $_POST['program_type_id'];

// Establish the database connection
try
{
	$con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
	die();
}

// Query for program types for dropdown
$query = "SELECT * FROM `program_type_table`;";
$stmt_program_type = $con->prepare($query);
$stmt_program_type->execute();

/**
 * Clinic Scheduling
 */
if(isset($_POST['clinic_event_creation']))
{
	// Establishing date object from submitted date for easy format
	$date = new DateTime ($_POST['date'], new DateTimeZone('America/Chicago'));
	if($date->format("l") == "Sunday")
	{
		$next_clinic_day = "Sunday";
	}
	else if($date->format("l") == "Wednesday")
	{
		$next_clinic_day = "Wednesday";
	}

	// This is passed to a new variable so that the date will be in the correct format for the bindValue below
	$clinic_date = $date->format("Y-m-d");
	$clinic_repeats = $_POST['clinic_repeats'];

	/**
	 * This is a horrible hack and needs to be fixed
	 ** Need at least two separate forms, but 1 needs to create the events, and the other needs to schedule them :'(
	 */
	// Checking to make sure that all program types have a program name
	$query = "SELECT `program_type_table`.`program_type_id`, `program_type_table`.`program_type`, COUNT(`program_table`.`program_type_id`) AS `count`
				FROM `program_table`
				RIGHT JOIN `program_type_table`
				ON `program_type_table`.`program_type_id` = `program_table`.`program_type_id`
				GROUP BY `program_type_table`.`program_type_id`";
	$stmt_program_type_count = $con->prepare($query);
	$stmt_program_type_count->execute();

	// Prepare to insert a program if it is missing and the program is either EAB Clinic or M-Power Clinic
	$query = "INSERT INTO `program_table` (`program_name`, `program_type_id`) VALUES (:program_name, :program_type_id);";
	$stmt_insert_missing = $con->prepare($query);
	$stmt_insert_missing->bindParam(':program_name', $program_name_missing, PDO::PARAM_STR);
	$stmt_insert_missing->bindParam(':program_type_id', $program_type_id_missing, PDO::PARAM_STR);

	// checking the count of the program types
	while($result = $stmt_program_type_count->fetch())
	{
		$program_name_missing = $result['program_type'];
		$program_type_id_missing = $result['program_type_id'];

		// if the program type count is 0 (doesn't exist in program) and it's either EAB clinic or M-Power, create it in program_table (again horrible hack)
		if($result['count'] == 0 && ($program_name_missing == "EAB Clinic" || $program_name_missing == "M-Power Clinic"))
		{
			$stmt_insert_missing->execute();
		}
	}


	// establishing appropriate parameters for specific clinics
	// $program_id == 1 --> EAB and $program_id == 2 --> M-Power
	// If this becomes variable, just build this part into the form for admin submission and modification
	if ($program_type_id == 1)
	{
		// Permanent location id differentiates this from the screening location
		$permanent_location_id = 1;
		$start_time = "13:00:00";
		$end_time = "18:00:00";
	}
	elseif ($program_type_id == 2)
	{
		$permanent_location_id = 2;
		$start_time = "15:30:00";
		$end_time = "19:00:00";
	}

	//get variables
		//clinic
		//date
		//number of repeats
	//create an insert statement
		//program_id
		//location_id
		//date
		//start_time
		//end_time

	// Query for program_id based on program_type
	$query = "SELECT * FROM `program_table` WHERE `program_type_id` = :program_type_id;";
	$stmt_get_program_type = $con->prepare($query);
	$stmt_get_program_type->bindValue(':program_type_id', $program_type_id, PDO::PARAM_STR);
	$stmt_get_program_type->execute();
	$result = $stmt_get_program_type->fetch();
	$program_id = $result['program_id'];

	// Query to create new program times for clinic
	$query = "INSERT INTO `program_relation_table` (`program_id`, `location_id`, `date`, `start_time`, `end_time`) VALUES (:program_id, :location_id, :date, :start_time, :end_time);";
	$stmt_insert_program = $con->prepare($query);
	$stmt_insert_program->bindParam(':program_id', $program_id, PDO::PARAM_STR);
	$stmt_insert_program->bindParam(':location_id', $permanent_location_id, PDO::PARAM_STR);
	$stmt_insert_program->bindParam(':date', $clinic_date, PDO::PARAM_STR);
	$stmt_insert_program->bindParam(':start_time', $start_time, PDO::PARAM_STR);
	$stmt_insert_program->bindParam(':end_time', $end_time, PDO::PARAM_STR);

	// Query to check for duplicate program times
	$dup = "SELECT COUNT(*) AS 'dupes'
		FROM program_relation_table
		WHERE `date` = :new_date AND `program_id` = :program_id";
	$stmt_dupes = $con->prepare($dup);
	$stmt_dupes->bindParam(':new_date', $new_date, PDO::PARAM_STR);
	$stmt_dupes->bindParam(':program_id', $program_id, PDO::PARAM_STR);

	echo "
    <script>
      $(document).ready(function() {
        $('#eventCreationModal').modal('toggle');
      });
    </script>
	";
}

/**
 * Screening Event setup
 */
if(isset($_POST['screening_creation']))
{
	// Basic POST variables that need no modification
	$program_name = $_POST['name'];
	$location_name = $_POST['location_name'];
	$address = $_POST['location_address'];
	$program_type_id = $_POST['program_type_id'];

	// Creating the screening date from the parts
	$screening_month = $_POST['screening_month'];
	$screening_day = $_POST['screening_day'];
	$screening_year = $_POST['screening_year'];
	$screening_date = $screening_year . "-" . $screening_month . "-" . $screening_day;

	/**
	 * Creating the start time, end time, and arrival time from the parts as well
	 */

	// Start time
	$start_time_hour = $_POST['start_time_hour'];
	$start_time_minute = $_POST['start_time_minute'];
	$start_time_ampm = $_POST['start_time_ampm'];

	if ($start_time_ampm = 2)
	{
		$new_start_time_hour = $start_time_hour + 12;
	}
	elseif ($start_time_ampm = 1)
	{
		$new_start_time_hour = $start_time_hour;
	}
	$start_time = $new_start_time_hour . ":" . $start_time_minute;

	// End time
	$end_time_hour = $_POST['end_time_hour'];
	$end_time_minute = $_POST['end_time_minute'];
	$end_time_ampm = $_POST['end_time_ampm'];
	if ($end_time_ampm = 2)
	{
		$new_end_time_hour = $end_time_hour + 12;
	}
	elseif ($end_time_ampm = 1)
	{
		$new_end_time_hour = $end_time_hour;
	}
	$end_time = $new_end_time_hour . ":" . $end_time_minute;

	// Arrival time
	$arrival_time_hour = $_POST['arrival_time_hour'];
	$arrival_time_minute = $_POST['arrival_time_minute'];
	$arrival_time_ampm = $_POST['arrival_time_ampm'];
	if ($arrival_time_ampm = 2)
	{
		$new_arrival_time_hour = $arrival_time_hour + 12;
	}
	elseif ($arrival_time_ampm = 1)
	{
		$new_arrival_time_hour = $arrival_time_hour;
	}
	$arrival_time = $new_arrival_time_hour . ":" . $arrival_time_minute;


	//location table
		//location name
		//location address
	//program_table
		//program_type_id
		//program_name
	//Use location_id and program_id to insert program_relation_table
		//date
		//start_time
		//end_time
	//Take program_id from program_table and insert into arrival_time_table
	//Take role_id if program_type_id = 3 and insert all applicable role_id into arrival_time_table
		//arrival_time (into arrival_time_table)
		

	// Counting info in the tables to see if it's already been submitted (everything is in a different location, so doing this separately seems easier)
	$query = "SELECT COUNT(*) as count FROM location_table WHERE location_name = :location_name AND address = :address;";
	$stmt_location = $con->prepare($query);
	$stmt_location->bindParam(':location_name', $location_name, PDO::PARAM_STR);
	$stmt_location->bindParam(':address', $address, PDO::PARAM_STR);
	$stmt_location->execute();
	$result_location = $stmt_location->fetch();

	$query = "SELECT COUNT(*) as count FROM program_table WHERE program_name = :program_name AND program_type_id = :program_type_id;";
	$stmt_program = $con->prepare($query);
	$stmt_program->bindParam(':program_name', $program_name, PDO::PARAM_STR);
	$stmt_program->bindParam(':program_type_id', $program_type_id, PDO::PARAM_STR);
	$stmt_program->execute();
	$result_program = $stmt_program->fetch();

	$query = "SELECT COUNT(*) as count FROM program_relation_table WHERE `date` = :date AND start_time = :start_time AND end_time = :end_time;";
	$stmt_program_relation = $con->prepare($query);
	$stmt_program_relation->bindParam(':date', $screening_date, PDO::PARAM_STR);
	$stmt_program_relation->bindParam(':start_time', $start_time, PDO::PARAM_STR);
	$stmt_program_relation->bindParam(':end_time', $end_time, PDO::PARAM_STR);
	$stmt_program_relation->execute();
	$result_program_relation = $stmt_program_relation->fetch();

	// If the screening event is unique (that means that at least one of the counts above is 0)
	if(!$result_location['count'] || !$result_program['count'] || !$result_program_relation['count'])
	{
		$query = "INSERT INTO location_table VALUES ('', :location_name, :address);";
		$stmt = $con->prepare($query);
		$stmt->bindParam(':location_name', $location_name, PDO::PARAM_STR);
		$stmt->bindParam(':address', $address, PDO::PARAM_STR);
		$stmt->execute();
		
		$location_id = $con->lastInsertId();
		$stmt = null;
		
		
		$query = "INSERT INTO program_table (program_name, program_type_id) VALUES (:program_name, :program_type_id);";
		$stmt = $con->prepare($query);
		$stmt->bindParam(':program_name', $program_name, PDO::PARAM_STR);
		$stmt->bindParam(':program_type_id', $program_type_id, PDO::PARAM_STR);
		$stmt->execute();
		
		$program_id = $con->lastInsertId();
		
		$stmt = null;
		
		$query = "INSERT INTO program_relation_table (`program_id`, `location_id`, `date`, `start_time`, `end_time`) VALUES (:program_id, :location_id, :date, :start_time, :end_time);";
		$stmt = $con->prepare($query);
		$stmt->bindParam(':program_id', $program_id, PDO::PARAM_STR);
		$stmt->bindParam(':location_id', $location_id, PDO::PARAM_STR);
		$stmt->bindParam(':date', $screening_date, PDO::PARAM_STR);
		$stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);
		$stmt->bindParam(':end_time', $end_time, PDO::PARAM_STR);
		$stmt->execute();
		
		
		$stmt = null;
	}


	$query = "SELECT role_id FROM role_program_type_relation_table WHERE program_type_id = 3;";
	$stmt_role_program = $con->prepare($query);
	$stmt_role_program->execute();

	$insert_query = "INSERT INTO arrival_time_table (`role_id`, `program_id`, `arrival_time`) VALUES (:role_id, :program_id, :arrival_time);";
	$role_arrival = $con->prepare($insert_query);
	$role_arrival->bindParam(':role_id', $role_id, PDO::PARAM_STR);
	$role_arrival->bindParam(':program_id', $program_id, PDO::PARAM_STR);
	$role_arrival->bindParam(':arrival_time', $arrival_time, PDO::PARAM_STR);

	while ($result_role_program = $stmt_role_program->fetch())
	{
		$role_id = $result_role_program['role_id'];
		$role_arrival->execute();
	}

	$stmt_role_program = null;
	$role_arrival = null;


	// Establish objects of variables for easy formatting
	$date = new DateTime($screening_date);
	$start_time = new DateTime($start_time);
	$end_time = new DateTime($end_time);
	$arrival_time = new DateTime($arrival_time);

	echo "
    <script>
      $(document).ready(function() {
        $('#screeningCreationModal').modal('toggle');
      });
    </script>
	";
}
?>

    <!-- Clinic Event creation Modal -->
    <div class="modal fade" id="eventCreationModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>
            <img class="modal-logo" src="/images/EABLogo.png" alt="EAB Logo" />
          </div>
          <div class="modal-body">
            <h4 class="modal-body-header"><strong>Created Events</strong></h4>
            <ul>
<?php
//build the for loop (properly inserts the dates.)
for($i = 0; $i < $clinic_repeats; $i++)
{
	$stmt_dupes->execute();
	$result_dupes = $stmt_dupes->fetch();
	if ($result_dupes['dupes'] == 0)
	{
		$stmt_insert_program->execute();
		if($stmt_insert_program->rowCount())
		{
			echo "              <li>" . $date->format("F j, Y") . " event was successfully created</li>\n";
		}
		else
		{
			echo "              <li>" . $date->format("F j, Y") . " event could not be inserted into the database</li>\n";
		}
	}
	else
	{
		echo "              <li>" . $date->format("F j, Y") . " event has already been created for the selected program</li>\n";
	}
	$clinic_date = $date->modify("next " . $next_clinic_day)->format("Y-m-d");
}

?>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-eab" data-dismiss="modal">Ok</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Screening Creation Modal -->
    <div class="modal fade" id="screeningCreationModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>
            <img class="modal-logo" src="/images/EABLogo.png" alt="EAB Logo" />
          </div>
          <div class="modal-body">
            <h4 class="modal-body-header"><strong>Screening Event</strong></h4>

<?php
if(isset($_POST['screening_creation']))
{
	if($result_location['count'] && $result_program['count'] && $result_program_relation['count'])
	{
		echo "
            <p>This screening event has already been created</p>
            <ul>
              <li>Name: " . $program_name . "</li>
              <li>Location: " . $location_name . "</li>
              <li>Address: " . $address . "</li>
              <li>Date: " . $date->format("F j, Y") . "</li>
              <li>Start Time: " . $start_time->format("g:i A") . "</li> 
              <li>End Time: " . $end_time->format("g:i A") . "</li>
              <li>Officer Arrival Time: " . $arrival_time->format("g:i A") . "</li>
            </ul>\n";
	}
	else
	{
		echo "
            <p>Successfully created the following screening</p>
            <ul>
              <li>Name: " . $program_name . "</li>
              <li>Location: " . $location_name . "</li>
              <li>Address: " . $address . "</li>
              <li>Date: " . $date->format("F j, Y") . "</li>
              <li>Start Time: " . $start_time->format("g:i A") . "</li> 
              <li>End Time: " . $end_time->format("g:i A") . "</li>
              <li>Officer Arrival Time: " . $arrival_time->format("g:i A") . "</li>
            </ul>
		";
	}
}
?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-eab" data-dismiss="modal">Ok</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Body Content -->
    <div class="container no-image">

      <!-- Create clinic event -->
      <h1>Create Clinic Event</h1>

      <!-- Form for event creation -->
      <form method="post" action="admin_event_create.php" name="eventcreationform" role="form">
        
        <!--drop down menu to selct either EAB or M-Power-->
        <div class="form-group">
          <label for="program_type_id">Event Name</label>
          <select class="form-control" name="program_type_id" required>
            <option value="">--Select--</option>
<?php
while($result = $stmt_program_type->fetch())
{
	if($result['program_type'] != "EAB Screening")
	{
		echo "            <option value=\"" . $result['program_type_id'] . "\">" . $result['program_type'] . "</option>\n";
	}
}
?>
          </select>
        </div>

        <!-- Date of event -->
        <div class="form-group">
          <label>Date</label>
          <select class="form-control" name="date" required>
            <option value="" selected>--Select--</option>
<?php
/**
 * Setting up dates to find first day in line, Sunday or Wednesday
 */

// Establish DateTime objects for use as Wednesday or Sunday
$dateSunday = new DateTime("now", new DateTimeZone('America/Chicago'));
$dateWednesday = new DateTime("now", new DateTimeZone('America/Chicago'));

// Forcing DateTime objects to move to next Sunday or Wednesday respectively
$dateSunday->modify("next Sunday");
$dateWednesday->modify("next Wednesday");

// if Sunday is earlier, put it first
if($dateSunday < $dateWednesday)
{
	$day_array = array("Sunday", "Wednesday");
	$date = $dateSunday;
}
// Else put wednesday first (Wednesday is earlier)
else
{
	$day_array = array("Wednesday", "Sunday");
	$date = $dateWednesday;
}

// Week counter
$week = 1;

// Allow start date to go for one month
for($week; $week <= 8; $week++)
{
	// if first week, don't modify first day
	if($week == 1)
	{
		// Print first day (Wednesday or Sunday based on prior array)
		echo "            <option value=\"" . $date->format("Y-m-d") . "\">" . $date->format("l, F j, Y") . "</option>\n";
		
		// Modify to next day in array (still first week)
		$date->modify("next " . $day_array[1]);
		
		// Print second day (second value in array)
		echo "            <option value=\"" . $date->format("Y-m-d") . "\">" . $date->format("l, F j, Y") . "</option>\n";
	}

	// If it's not the first week
	else
	{
		// Continue to print each daty for the array before going back for the next week
		foreach($day_array as $day)
		{
			$add = "next $day";
			$date->modify($add)->format("Y-m-d");
			echo "            <option value=\"" . $date->format("Y-m-d") . "\">" . $date->format("l, F j, Y") . "</option>\n";
		}
	}
}
?>
          </select>
        </div>

        <!--This allows creation of repeated events on the same day of the week at the same time-->
        <div class="form-group">
          <label for="clinic_repeats">How many consecutive events?</label>
          <input type="text" pattern="[0-9]{1,99}" id="clinic_repeats" class="form-control" name="clinic_repeats" required />
        </div>

        <input type="submit" class="btn btn-default" name="clinic_event_creation" value="Submit" />
      </form><hr />
    
      <!-- Creating a Screening event (a different beast altogether) -->
      <h1>Create Screening Event</h1>
    
      <!-- Form for new screening event creation -->
      <form method="post" action="" name="screeningform">
        
        <!-- Submit hidden input for program_type_id for screening -->
        <input type="text" name="program_type_id" value="3" style="display: none;">
        
        <!-- Name for new screening event -->
        <div class="form-group">
          <label for="name">Name of Event</label>
          <input type="text" pattern="[a-zA-Z0-9!@#$%^&* ]{1,99}" id="name" class="form-control" name="name" required />
        </div>

        <!-- Name of location -->
        <div class="form-group">
          <label for="location_name">Location Name</label>
          <input type="text" pattern="[a-zA-Z0-9!@#$%^&* ]{1,99}" id="location_name" class="form-control" name="location_name" required />
        </div>

        <!-- Address of location -->
        <div class="form-group">
          <label for="location_address">Location Address</label>
          <input type="text" pattern="[a-zA-Z0-9!@#$%^&*\. ]{1,99}" id="location_address" class="form-control" name="location_address" required />
        </div>

        <!-- Date of screening -->
        <div class="form-group">
          <label>Date</label>
  
          <div class="row">
            <div class="col-xs-4">
              <!-- Month -->
              <select class="form-control" name="screening_month" required>
                <option value="">--Month--</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
              </select>
            </div>
  
            <!-- Day -->
            <div class="col-xs-4">
              <select class="form-control" name="screening_day" required>
                <option value="">--Day--</option>
<?php
for($x = 1; $x < 32; $x++)
{
	echo "                <option value=\"$x\">$x</option>\n";
}
?>
              </select>
            </div>
  
            <!-- Year -->
            <div class="col-xs-4">
              <select class="form-control" name="screening_year" required>
                <option value="">--Year--</option>
<?php
$x = date("Y");

// Allow to year in advance scheduling
$y = $x + 2;
for($x; $x < $y; $x++)
{
	echo "                <option value=\"$x\">$x</option>\n";
}
?>
              </select>
            </div>
          </div>
        </div>

        <!-- Start Time -->
        <div class="form-group">
          <label>Start Time</label>
  
          <div class="row">
            <!-- Hour -->
            <div class="col-xs-4">
              <select class="form-control" name="start_time_hour" required>
                <option value="">--Hour--</option>
<?php
for ($i = 1; $i <= 12; $i++)
{
	echo "                <option value=\"$i\">$i</option>\n";
}
?>

              </select>
            </div>

            <!-- Minute -->
            <div class="col-xs-4">
              <select class="form-control" name="start_time_minute" required>
                <option value="">--Minute--</option>
<?php
for ($i = 0; $i <= 59; $i++)
{
	if($i <=9)
	{
		$i = "0".$i;
	}
	echo "                <option value=\"$i\">$i</option>\n";
}
?>
              </select>
            </div>

            <!-- AM/PM -->
            <div class="col-xs-4">
              <select class="form-control" name="start_time_ampm" required>
                <option value="">--AM/PM--</option>
                <option value="1">AM</option>
                <option value="2">PM</option>
              </select>
            </div>
          </div>
        </div>
        
    
        <!-- End Time -->
        <div class="form-group">
          <label>End Time</label>
          
          <div class="row">

          <!-- Hour -->
            <div class="col-xs-4">
              <select class="form-control" name="end_time_hour" required>
                <option value="">--Hour--</option>
<?php
for ($i = 1; $i <= 12; $i++)
{
	echo "                <option value=\"$i\">$i</option>\n";
}
?>

              </select>
            </div>

            <div class="col-xs-4">
              <select class="form-control" name="end_time_minute" required>
                <option value="">--Minute--</option>
<?php
for ($i = 0; $i <= 59; $i++)
{
	if($i <=9)
	{
		$i = "0".$i;
	}
	echo "                <option value=\"$i\">$i</option>\n";
}
?>
              </select>
            </div>

            <!-- AM/PM -->
            <div class="col-xs-4">
              <select class="form-control" name="end_time_ampm" required>
                <option value="">--AM/PM--</option>
                <option value="1">AM</option>
                <option value="2">PM</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Arrival Time -->
        <div class="form-group">
          <label>Officer Arrival Time</label>

          <div class="row">

            <!-- Hour -->
            <div class="col-xs-4">
              <select class="form-control" name="arrival_time_hour" required>
                <option value="">--Hour--</option>
<?php
for ($i = 1; $i <= 12; $i++)
{
	echo "                <option value=\"$i\">$i</option>\n";
}
?>

              </select>
            </div>
  
            <!-- Minute -->
            <div class="col-xs-4">
              <select class="form-control" name="arrival_time_minute" required>
                <option value="">--Minute--</option>
<?php
for ($i = 0; $i <= 59; $i++)
{
	if($i <=9)
	{
		$i = "0".$i;
	}
	echo "                <option value=\"$i\">$i</option>\n";
}
?>
              </select>
            </div>
  
            <!-- AM/PM -->
            <div class="col-xs-4">
              <select class="form-control" name="arrival_time_ampm" required>
                <option value="">--AM/PM--</option>
                <option value="1">AM</option>
                <option value="2">PM</option>
              </select>
            </div>
          </div>
        </div>

        <input type="submit" class="btn btn-default" name="screening_creation" value="Submit" />
      </form>
    </div>

<?php require_once($path_to_root . "includes/footer.php"); ?>