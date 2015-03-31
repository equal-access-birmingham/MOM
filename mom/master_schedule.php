<?php
$require_login = true;
$require_admin = true;
$require_verified_account = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

    <title>Master Schedule | Equal Access Birmingham</title>

<?php require_once($path_to_root . "includes/menu_mom.php"); ?>

<?php
// Establish variables to be populated with each refresh
$delete_schedule = $_POST['delete_schedule'];
$deleted_time_cnt = 0;

// Database Connection
try
{
	$db_connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
}

// Query for programs
$query = "SELECT * FROM `program_table`;";
$stmt_programs = $db_connection->prepare($query);
$stmt_programs->execute();


/**
 * Delete Confirmation Section
 */

// Query to retrieve scheduled volunteer time information from signup_id
$query = "SELECT `program_name_info`.*, `users`.`user_email`
	FROM (
		SELECT `program_info`.*, `program_table`.`program_name`
			FROM (
				SELECT `people_info`.*, `program_relation_table`.`program_id`, `program_relation_table`.`date`
					FROM (
						SELECT `person_info`.*, `person_table`.`fname`, `person_table`.`lname`, `person_table`.`phone_number`
							FROM (
								SELECT `signup_info`.*, `login_relation_table`.`person_id`, `login_relation_table`.`user_id`
									FROM(
										SELECT `signup_table`.*, `role_table`.`role_name`
											FROM `signup_table`
											INNER JOIN `role_table`
											ON `role_table`.`role_id` = `signup_table`.`role_id`
									) AS `signup_info`
									INNER JOIN `login_relation_table`
									ON `signup_info`.`login_relation_id` = `login_relation_table`.`login_relation_id`
							) AS `person_info`
							INNER JOIN `person_table`
							ON `person_table`.`person_id` = `person_info`.`person_id`
					) AS `people_info`
					INNER JOIN `program_relation_table`
					ON `program_relation_table`.`program_relation_id` = `people_info`.`program_relation_id`
			) AS `program_info`
			INNER JOIN `program_table`
			ON `program_info`.`program_id` = `program_table`.`program_id`
	) AS `program_name_info`
	INNER JOIN `users`
	ON `program_name_info`.`user_id` = `users`.`user_id`
	WHERE `program_name_info`.`signup_id` = :signup_id";

$stmt_delete_confirm = $db_connection->prepare($query);
$stmt_delete_confirm->bindParam(':signup_id', $signup_id, PDO::PARAM_STR);

if(isset($_POST['delete_schedule']))
{
	// Session variable to carry through page refreshes
	$_SESSION['delete_confirmed'] = array();

	// Trigger Confirmation modal
	echo "
    <script>
      $(document).ready(function() {
        $('#delete_confirm_modal').modal('toggle');
      });
    </script>
	";
}

/**
 * Delete Action Section
 */
if(isset($_POST['delete_confirmed']))
{
	// Pass $_SESSION variable to normal variable for use in PDO
	$delete_confirmed = $_SESSION['delete_confirmed'];

	// Deletion query
	$query = "DELETE FROM `signup_table` WHERE `signup_id` = :signup_table;";
	$stmt_delete_schedule = $db_connection->prepare($query);
	$stmt_delete_schedule->bindParam(':signup_table', $signup_id, PDO::PARAM_STR);
	foreach($delete_confirmed as $signup_id)
	{
		$stmt_delete_schedule->execute();

		// Check to see if deletion went through
		if($stmt_delete_schedule->rowCount())
		{
			// Count number of deletions
			$deleted_time_cnt++;
		}
	}

	// Trigger modal
	echo "
    <script>
      $(document).ready(function() {
        $('#delete_modal').modal('toggle');
      });
    </script>
	";
}

?>

    <!-- Deletion Confirm Modal -->
    <div class="modal fade" id="delete_confirm_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
            <h4 class="modal-body-header"><strong>Confirm Deletion</strong></h4>
            <p>Delete the following scheduled volunteer times?</p>
            <table class="table table-striped text-center">
              <tr>
                <th class="text-center">First Name</th>
                <th class="text-center">Last Name</th>
                <th class="text-center">Clinic</th>
                <th class="text-center">Date</th>
                <th class="text-center">Role</th>
              </tr>

<?php
foreach($delete_schedule as $signup_id)
{
	// Execute statement from above query (selecting scheduled volunteer time information) and then fetch the data
	$stmt_delete_confirm->execute();
	$result = $stmt_delete_confirm->fetch();

	// Add signup_id's to the session variable for use if the user decides to delete them
	$_SESSION['delete_confirmed'][] = $result['signup_id'];

	echo "
            <tr>
              <td>" . $result['fname'] . "</td>
              <td>" . $result['lname'] . "</td>
              <td>" . $result['program_name'] . "</td>
              <td>" . $result['date'] . "</td>
              <td>" . $result['role_name'] . "</td>
            </tr>";
}
?>
            </table>
          </div>
          <div class="modal-footer">
            <form action="" method="post">
              <input type="submit" class="btn btn-success" name="delete_not_confirmed" value="No" />
              <input type="submit" class="btn btn-danger" name="delete_confirmed" value="Yes" />
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
            <h4 class="modal-body-header"><strong>Deletion Successful</strong></h4>
<?php
// The delete count must be greater than one, otherwise nothing was ever deleted
if(count($delete_confirmed) == $deleted_time_cnt && $deleted_time_cnt > 0)
{
	echo "            <p>All selected times were successfully deleted</p>\n";
}
else
{
	echo "            <p>All selected times could not be deleted, please contact the <a href=\"mailto:eabitteam@gmail.com\">EAB IT team</a> for assistance.</p>\n";
}
?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-eab" data-dismiss="modal">Ok</button>
          </div>
        </div>
      </div>
    </div>



    <div class="container no-image">
      <h1>Master Schedule</h1>

      <h3>Search Schedule</h3>
      <form action="" method="get" role="form">
        <span id="keyup_event">
          <div class="form-group">
            <input type="text" id="fname" class="form-control" name="fname" placeholder="First Name" />
          </div>
          <div class="form-group">
            <input type="text" id="lname" class="form-control" name="lname" placeholder="Last Name" />
          </div>
        </span>
  
        <span id="change_event">

          <!-- Year -->
          <div class="form-group">
            <div class="row">
              <div class="col-xs-4">
                <select id="year" class="form-control" name="year">
                  <option value="">-- Year --</option>
<?php
// Establish year dropdown
// Allows viewing of schedule from last year and 4 years in the future
$years = new DateTime("last year");
for($i = 1; $i < 6; $i++)
{
	echo "                  <option value=\"" . $years->format("Y") . "\">" . $years->format("Y") . "</option>\n";
	$years->modify("next year");
}
?>
                </select>
              </div>

              <!-- Month -->
              <div class="col-xs-4">
                <select id="month" class="form-control" name="month">
                  <option value="">-- Month --</option>
<?php
// Creates the months of the year
// Day is necessary as the day will be pulled from the current date otherwise
// (results in skipping the month if the current day does not exist for selected month)
$months = new DateTime("January 1");
for($i = 1; $i < 13; $i++)
{
	echo "                  <option value=\"" . $months->format("n") . "\">" . $months->format("F") . "</option>\n";
	$months->modify("next month");
}
?>
                </select>
              </div>

          <!-- Day:  requires year and month selection first as is controlled via javascript -->
              <div class="col-xs-4">
                <select id="day" class="form-control" name="day">
                </select>
              </div>
            </div>
          </div>

          <!-- Programs -->
          <div class="form-group">
            <select id="program" class="form-control" name="program">
              <option value="">-- Program --</option>
<?php
while($result = $stmt_programs->fetch())
{
	echo "               <option value=\"" . $result['program_id'] . "\">" . $result['program_name'] . "</option>";
}
?>
            </select>
          </div>
        </span>
      </form><hr />

      <h3>View Schedule</h3>
      <form action="" method="post" role="form">
        <input type="submit" class="btn btn-default" name="delete_schedule" value="Delete Volunteer Times" /><br /><br />
        <div class="table-responsive">
          <table id="master_schedule" class="table text-center">
          </table>
        </div>
      </form>
    </div>

    <script>
      // AJAX for table display

      // Sets up the AJAX process and sends data to server for processing
      function search_schedule(fname, lname, year, month, day, program)
      {
        var xmlhttp;

        if(window.XMLHttpRequest)
        {
          xmlhttp = new XMLHttpRequest();
        }
        else
        {
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function()
        {
          if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
          {
            document.getElementById("master_schedule").innerHTML = xmlhttp.responseText;

            // Allows footer to adjust once table is searched or initially loaded
            footerDrop();
          }
        }

        xmlhttp.open("GET", "master_schedule_table.php?fname="+fname+"&lname="+lname+"&year="+year+"&month="+month+"&day="+day+"&program="+program, true);
        xmlhttp.send();
      }

      // Applies keyup event to <span> encapsulating text inputs so that table returns based on name entry
      var keyup_event = document.getElementById("keyup_event");
      keyup_event.addEventListener("keyup", return_table, false);

      // Applies change event to <span> encapsulating dropdown inputs so that table returns based on user selection
      var change_event = document.getElementById("change_event");
      change_event.addEventListener("change", return_table, false)

      // Returns table when the document loads
      document.body.onload = return_table;

      // returns the table when an event is triggered
      function return_table(event)
      {
        var fname_input = document.getElementById("fname");
        var lname_input = document.getElementById("lname");
        var year_input = document.getElementById("year");
        var month_input = document.getElementById("month");
        var day_input = document.getElementById("day");
        var program_input = document.getElementById("program");

        if(event.target != event.currentTarget)
        {
          search_schedule(fname_input.value, lname_input.value, year_input.value, month_input.value, day_input.value, program_input.value);
        }
      }

      // Date function
      var $day = $("#day");

      // Set options each time month or year is changed
      $("#month, #year").on("change", function() {
        setDayOptions($day);
      });

      // Sets day options on loading (solely to check for blank month and year on load)
      $(document).ready(function() {
        setDayOptions($day);
      });

      // Sets the day options in the day dropdown
      function setDayOptions(day_elem)
      {
        // Empties the dropdown each time for re-population
        day_elem.empty();

        // Gets the values of the month and year dropdown
        var month = $("#month").val();
        var year = $("#year").val();

        // Creates the date object and sets the year and month to those selected by the user
        // The date is set generically as Date() pulls from now otherwise and getMonth will not pull the correct month if the current day is not in month chosen
        // i.e. if the day is the 31st of the month and the user selects the month of June, getMonth will automatically select July because there aren't 31 days in June
        var day = new Date("January 1, 1970 00:00:00");
        day.setFullYear(year);
        day.setMonth(month - 1);

        // Arrays for the months with different days (February is excluded as an odd-ball)
        // Note that JavaScript starts with "January" as month 0... :P
        thirty_days = [3, 5, 8, 10];
        thirty_one_days = [0, 2, 4, 6, 7, 9, 11];

        // If the month or year are blank, tell user to fill these our first
        if(month == "" || year == "")
        {
          var new_option = "<option value=\"\">Please select a year and month first</option>";
          day_elem.append(new_option);
        }
        else
        {
          // The number of days in the month (based on above arrays) determines how many days are checked
          if($.inArray(day.getMonth(), thirty_days) > -1)
          {
            writeOptions(day_elem, day, 30);
          }
          else if($.inArray(day.getMonth(), thirty_one_days) > -1)
          {
            writeOptions(day_elem, day, 31);
          }
          // Odd-ball February month
          else
          {
            // Checking for leap year
            if(leapYear(year))
            {
              writeOptions(day_elem, day, 29);
            }
            else
            {
              writeOptions(day_elem, day, 28);
            }
          }
        }
      }

      // Determines if a year is a leap year
      function leapYear(year)
      {
        if(year.toString().substr(2,3) == "00")
        {
          if(year % 400 == 0)
          {
            return true;
          }
        }
        else if(year % 4 == 0)
        {
          return true;
        }

        return false;
      }

      // Writes in the day options based on the day being Sunday or Wednesday
      function writeOptions(day_elem, date_obj, num_days)
      {
        // num_days = number of day in the month
        for(var i = 1; i <= num_days; i++)
        {
          date_obj.setDate(i);
          // if the day is Sunday (0) or Wednesday (3), print the day in an option
          // change this line if you want more dates
          if(date_obj.getDay() == 0 || date_obj.getDay() == 3)
          {
            var new_option = "<option value=\"" + date_obj.getDate() + "\">" + date_obj.getDate() + "</option>";
            day_elem.append(new_option);
          }
        }
      }
    </script>

<?php require_once($path_to_root . "includes/footer.php"); ?>