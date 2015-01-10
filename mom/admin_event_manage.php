<?php
$require_login = true;
$require_admin = true;
$require_verified_account = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require($path_to_root . 'includes/header.php');
?>

    <title>Event Manage | Equal Access Birmingham</title>

<?php require_once($path_to_root . 'includes/menu_sign_in.php'); ?>

<?php
// Create a database connection or return error and terminate admin page
try
{
	$db_connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
	die();
}

// Array of all selected events to be deleted
$delete_event = $_POST['delete_event'];

// Query selects pertinent information about events to be deleted in order to prompt user for confirmation
$query = "SELECT `tmp1`.`program_relation_id`, `tmp1`.`date`, `tmp1`.`program_name`, `location_table`.`location_name`
	FROM (
		SELECT `program_relation_table`.`program_relation_id`, `program_relation_table`.`date`, `program_table`.`program_name`, `program_relation_table`.`location_id`
		FROM program_relation_table
		JOIN program_table
		ON `program_relation_table`.`program_id` = `program_table`.`program_id`
	) AS tmp1
	INNER JOIN `location_table`
	ON `tmp1`.`location_id` = `location_table`.`location_id`
	WHERE `tmp1`.`program_relation_id` = :program_relation_id;";

$stmt_delete_confirm = $db_connection->prepare($query);
$stmt_delete_confirm->bindParam(':program_relation_id', $program_relation_id, PDO::PARAM_STR);

// Triggers modal for confirmation of deletion
if(isset($_POST['admin_event_manage']))
{
	echo "
    <script>
      $(document).ready(function() {
        $('#confirm_delete').modal('toggle');
      });
    </script>
	";
}


//if screening delete from where program_id  & program_relation_id from program relation table
//only drop program_relation_id from program_relation table if eab and m power

// If user confirms deletions, delete with impunity!!!
if(isset($_POST['confirm_action']))
{
	// Changed to $event_id for deletion
	$delete_array = $_POST['delete_event'];

	$query = "SELECT `program_id`, `location_id` FROM `program_relation_table` WHERE program_relation_id = :program_relation_id;";
	$select_program = $db_connection->prepare($query);
	$select_program->bindParam(':program_relation_id', $event_id, PDO::PARAM_STR);

	// Delete only the scheduled event (program_relation_table), not the event itself (program_table)
	// This can be changed only when the database is modified and a more powerful UI is in place
	$query = "DELETE FROM `program_relation_table` WHERE program_relation_id = :program_relation_id;";
	$delete_relation = $db_connection->prepare($query);
	$delete_relation->bindParam(':program_relation_id', $event_id, PDO::PARAM_STR);

	
	// Need to delete location as well once the scheduling file is fixed
	
	foreach($delete_array as $event_id)
	{
		$select_program->execute();
		$result = $select_program->fetch();
		$program_id = $result['program_id'];
		
		$delete_relation->execute();
	}
}

// Set up query for entire table of events
$query = "SELECT `tmp1`.`program_relation_id`, `tmp1`.`date`, `tmp1`.`program_name`, `location_table`.`location_name`
			FROM (
				SELECT `program_relation_table`.`program_relation_id`, `program_relation_table`.`date`, `program_table`.`program_name`, `program_relation_table`.`location_id`
				FROM `program_relation_table`
				INNER JOIN `program_table`
				ON `program_relation_table`.`program_id` = `program_table`.`program_id`
			) AS `tmp1`
			INNER JOIN `location_table`
			ON `tmp1`.`location_id` = `location_table`.`location_id`
			ORDER BY `tmp1`.`date` ASC;
";

$query_user_table = $db_connection->prepare($query);
$query_user_table->execute();

?>

    <!--  Delete Confirm Modal -->
    <div class="modal fade" id="confirm_delete" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
            <p>Delete the following events?</p>

            <table class="table table-striped text-center">
              <tr>
                <th class="text-center">Date</th>
                <th class="text-center">Event</th>
                <th class="text-center">Location</th>
              </tr>
<?php
// Displays all events selected by user in table and asks for confirmation
foreach($delete_event as $program_relation_id)
{
	$stmt_delete_confirm->execute();
	$result = $stmt_delete_confirm->fetch();
	$date = new DateTime($result['date']);

	echo "
            <tr>
              <td>" . $date->format("F j, Y") . "</td>
              <td>" . $result['program_name'] . "</td>
              <td>" . $result['location_name'] . "</td>
            </tr>
	";
}
?>

            </table>
          </div>
          <div class="modal-footer">
            <form method="post" action="" role="form">
<?php
// Hidden field so that all selected events can be passed along for deletion
foreach($delete_event as $program_relation_id)
{
	echo "
              <input type=\"text\" name=\"delete_event[]\" value=\"$program_relation_id\" style=\"display: none;\" />";
}
?>
              <input type="submit" class="btn btn-success" value="No, Go Back!">
              <input type="submit" class="btn btn-danger" name="confirm_action" value="Confirm">
            </form>
          </div>
        </div>
      </div>
    </div>



    <div class="container no-image">


      <h2>Scheduled Events</h2>
      <!-- Table of all users encapsulated by a form to allow checkboxes for quickly modifying account permissions -->

      <!-- Table of events for deletion -->
      <form method="post" action="" role="form">
        <div class="row">
          <div class="col-xs-4 col-xs-offset-1">  
            <div class="form-group">
              <input type="submit" name="admin_event_manage" class="btn btn-danger" value="Delete Events" />
            </div>
          </div>
        </div>
        <table class="table table-hover text-center"> 
          <tr>
            <th class="text-center">Select</th>
            <th class="text-center">Date</th>
            <th class="text-center">Event</th>
            <th class="text-center">Location</th>
          </tr>

<?php
// Creates table
while($data = $query_user_table->fetchObject())
{
	$new_date = new DateTime($data->date);
	echo "
          <tr>
            <td><input type=\"checkbox\" name=\"delete_event[]\" value=\"$data->program_relation_id\" /></td>
            <td>" . $new_date->format ("F j, Y") . "</td>
            <td>$data->program_name</td>
            <td>$data->location_name</td>
          </tr>\n";
}
?>
        </table>
      </form>
    </div>

<?php require_once($path_to_root . "includes/footer.php"); ?>