<?php
$require_login = true;
$require_admin = true;
$require_verified_account = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

    <title>Admin Sign Up Tool | Equal Access Birmingham</title>

<?php require_once($path_to_root . "includes/menu_mom.php"); ?>

<?php
// Create a database connection or return error and terminate admin page
// Establish Get variables
$program = $_GET['program_id'];
$date = $_GET['date'];
$role = $_GET['role_id'];
$login_relation_id = $_GET['login_relation_id'];

// Variable to check if insert was successful
$insert_ok = 0;

// Checking for duplicate entries
$dup_entry = 0;

// Setting up database connection
try
{
	$con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
}

// MySQL query of programs
$query = "SELECT * FROM `program_table`;";
$stmt_program = $con->prepare($query);
$stmt_program->execute();

// MySQL query of dates ordered by date
$query = "SELECT * FROM `program_relation_table` ORDER BY `date`;";
$stmt_date = $con->prepare($query);
$stmt_date->execute();

// MySQL roles
$query = "SELECT * FROM `role_table`;";
$stmt_role = $con->prepare($query);
$stmt_role->execute();

/** sign up table
 *need login_relation_id
 *program_relation_id
 *role_id
 */
// acquire program_relation_id
$query = "SELECT `program_relation_id` FROM `program_relation_table` WHERE `program_id` = :program_id AND `date` = :date;";
$stmt_program_relation = $con->prepare($query);
$stmt_program_relation->bindValue(':program_id', $program, PDO::PARAM_STR);
$stmt_program_relation->bindValue(':date', $date, PDO::PARAM_STR); 
$stmt_program_relation->execute();
$result_program_relation = $stmt_program_relation->fetch();
$program_relation_id = $result_program_relation['program_relation_id'];

//to prevent insertion in the case that the same person attempts to sign up for the same day twice
$query_double_trouble = "SELECT COUNT(*) AS count FROM `signup_table` WHERE `login_relation_id` = :login_relation_id AND `program_relation_id` = :program_relation_id;";
$stmt_double_trouble = $con->prepare($query_double_trouble);
$stmt_double_trouble->bindValue(':login_relation_id', $login_relation_id, PDO::PARAM_STR);
$stmt_double_trouble->bindValue(':program_relation_id', $program_relation_id, PDO::PARAM_STR);
$stmt_double_trouble->execute();
$result_double_trouble = $stmt_double_trouble->fetch();

// Preventing double sign ups
if(!$result_double_trouble['count'])
{
	//! means "not" -- i.e., that hasnt occurred, so insert signup data
	//insert data into database

	// Signing volunteer up schedule time through insertion into database
	$query = "INSERT INTO `signup_table` (`login_relation_id`, `program_relation_id`, `role_id`) VALUES (:login_relation_id, :program_relation_id, :role_id)";
	$stmt_insert = $con->prepare($query);
	$stmt_insert->bindValue(':login_relation_id', $login_relation_id, PDO::PARAM_STR);
	$stmt_insert->bindValue(':program_relation_id', $program_relation_id, PDO::PARAM_STR);
	$stmt_insert->bindValue(':role_id', $role, PDO::PARAM_STR);
	$stmt_insert->execute();
	$result_insert = $stmt_insert->fetch();

	// If data never made it into the database (this is used in the modal)
	if($stmt_insert->rowCount())
	{
		$insert_ok = 1;
	}
}
// State variable that stores whether a duplicate entry was created
else
{
	$dup_entry = 1;
}

/**
 * Gathering information for alerting of sign up success
 */

// get name of person - join login_relation_id with person_id on person_table
$query = "SELECT `person_table`.`fname`, `person_table`.`lname`
			FROM `person_table`
			INNER JOIN `login_relation_table`
			ON `person_table`.`person_id` = `login_relation_table`.`person_id`
			WHERE `login_relation_table`.`login_relation_id` = :login_relation_id";
$stmt_login_relation_id = $con->prepare($query);
$stmt_login_relation_id->bindValue(':login_relation_id', $login_relation_id, PDO::PARAM_STR);
$stmt_login_relation_id->execute();
$result_login_relation_id = $stmt_login_relation_id->fetch();

// get program name
$query = "SELECT `program_name` FROM `program_table` WHERE `program_id` = :program_id";
$stmt_program_name = $con->prepare($query);
$stmt_program_name->bindValue(':program_id', $program, PDO::PARAM_STR);
$stmt_program_name->execute();
$result_program_name = $stmt_program_name->fetch();

// establish datetime object if the date is set
$date_object = ($date != "none") ? new DateTime($date) : "";

//get role_name
$query = "SELECT `role_name` FROM `role_table` WHERE `role_id` = :role_id";
$stmt_role_name = $con->prepare($query);
$stmt_role_name->bindValue(':role_id', $role, PDO::PARAM_STR);
$stmt_role_name->execute();
$result_role_name = $stmt_role_name->fetch();

// If no data has been entered in correctly
if(isset($_GET['schedule_volunteers']) && ($program == "none" || $date == "none" || $role == "none" || !$login_relation_id))
{
	echo "
    <script>
      $(document).ready(function() {
        $('#officer_scheduler_error').modal('toggle');
      });
    </script>
	";
}

// Trigger confirmation modal if user submits to schedule volunteers with all the correct information
if(isset($_GET['schedule_volunteers']) && $program != "none" && $date != "none" && $role != "none" && $login_relation_id)
{
	echo "
    <script>
      $(document).ready(function() {
        $('#signup_confirm').modal('toggle');
      });
    </script>
	";
}
?>


    <!-- Build html page -->

    <!-- Send error if person didn't fill out entire form -->
    <div class="modal fade" id="officer_scheduler_error" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>
            <img class="modal-logo" src="/images/EABLogo.png" alt="EAB Logo" />
          </div>
          <div class="modal-body">
            <h4 class="modal-body-header"><strong>Scheduler Error</strong></h4>
            <ul>

<?php
if(!$login_relation_id)
{
	echo "            <li>Error: Please select a volunteer.</li>";
}

if($program == "none")
{
	echo "            <li>Error: Please select a program.</li>";
}

if($date == "none")
{
	echo "            <li>Error: Please select a date.</li>";
}

if($role == "none")
{
	echo "            <li>Error: Please select a role for the volunteer.</li>";
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



    <!-- confirmation for sign up of volunteer by admin (this does not ask, but merely informs) -->
    <div class="modal fade" id="signup_confirm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>
            <img class="modal-logo" src="/images/EABLogo.png" alt="EAB Logo" />
          </div>
          <div class="modal-body">
            <h4 class="modal-body-header"><strong>Sign Up Confirmation</strong></h4>

<?php
// confirm insertion and alert 
if($insert_ok) //(insertion successful)
{
	// echo information
	echo "
            <ul>
              <li>" . $result_login_relation_id['fname'] . " " . $result_login_relation_id['lname'] . "</li>
              <li>" . $result_program_name['program_name'] . "</li>
              <li>" . $date_object->format("F j, Y") . "</li>
              <li>" . $result_role_name['role_name'] . "</li>
            </ul>\n";
}
else
{
	// inform user if it didn't work
	echo "
            <p>You can't sign a person up twice for the same day</p>
            <p>Failed to schedule volunteer</p>\n";
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
      <h1>Schedule Officer</h1>

      <!-- Search users via AJAX -->
      <form name="search_form" role="form">

        <!-- Volunteer's First Name -->
        <div class="form-group">
          <label>First Name:</label>
          <input type="text" class="form-control" id="fname" />
        </div>

        <!-- Volunteer's Last Name -->
        <div class="form-group">
          <label>Last Name:</label>
          <input type="text" class="form-control" id="lname" />
        </div>

        <!-- Volunteer's Position -->
        <div class="form-group">
          <label>Position:</label>
          <select class="form-control" id="officer">
            <option value="none">-- Select --</option>
            <option value="">All</option>
            <option value="1">Officer</option>
            <option value="2">Non-Officer</option>
          </select>
        </div>
      </form><hr />

      <!-- Admin Sign Up Form -->
      <form action="" method="get" name="role_id" role="form">

        <!-- User Acquired via AJAX and put into table in form -->
        <div class="form-group">
          <label>Volunteers (search above)</label>
          <div class="table-responsive">
            <table class="table table-striped text-center" id="volunteer_table">
            </table>
          </div>
        </div>

        <!-- Program -->
        <div class="form-group">
          <label>Program:</label>
          <select class="form-control" id="program_id" name="program_id" required>
            <option value="none">-- Program --</option>
<?php
while($result = $stmt_program->fetch())
{
	echo "            <option value=\"" . $result['program_id'] . "\">" . $result['program_name'] . "</option>\n";
}
?>
          </select>
        </div>

        <!--  Date -->
        <div class="form-group">
          <label>Date:</label>
          <select class="form-control" id="date" name="date" required>
            <option value="none">-- Date --</option>
<?php
while($result = $stmt_date->fetch())
{
	$date = new Datetime($result['date']);
	echo "            <option value=\"" . $date->format("Y-m-d") . "\">" . $date->format("F j, Y") . "</option>\n";
}
?>
          </select>
        </div>

        <!-- Role -->
        <div class="form-group">
          <label>Role:</label>
          <select class="form-control" id="role_id" name="role_id" required>
            <option value="none">-- Role --</option>
<?php
while($result = $stmt_role->fetch())
{
	echo "            <option value=\"" . $result['role_id'] . "\">" . $result['role_name'] . "</option>\n";
}
?>
          </select>
        </div>

        <!-- Submit button -->
        <input type="submit" class="btn btn-default" name="schedule_volunteers" value="Schedule Volunteers" />
      </form>
    </div>

    <script>
      // Allows searching through users for scheduling purposes
      function ajax_test(fname, lname, officer)
      {
        var xmlhttp;
        if (window.XMLHttpRequest)
        {
          xmlhttp = new XMLHttpRequest();
        }
        else
        {
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP")
        }
        xmlhttp.onreadystatechange=function()
        {
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
          {
            document.getElementById("volunteer_table").innerHTML = xmlhttp.responseText;
          }
        }
        xmlhttp.open("GET", "/mom/join.php?fname="+fname+"&lname="+lname+"&officer="+officer, true);
        xmlhttp.send();
      }

      // Grab elements' values from HTML to be used in AJAX
      var fname_input = document.getElementById("fname");
      var lname_input = document.getElementById("lname");
      var officer_input = document.getElementById("officer");

      // Trigger AJAX when values are changed
      fname_input.onkeyup = function(){ajax_test(fname_input.value, lname_input.value, officer_input.value);};
      lname_input.onkeyup = function(){ajax_test(fname_input.value, lname_input.value, officer_input.value);};
      officer_input.onchange = function(){ajax_test(fname_input.value, lname_input.value, officer_input.value);};
      document.body.onload = function(){ajax_test(fname_input.value, lname_input.value, officer_input.value);};
    </script>

<?php require_once($path_to_root . "includes/footer.php"); ?>