<?php
require('includes/header-require_admin.php');
require_once('includes/menu_sign_in.php');

error_reporting(E_ALL);
ini_set("display_errors", 1);

if(isset($_GET['admin_event_manage']))
{
	$delete_event=$_GET['delete_event'];
	echo "
	<script>
		$(document).ready(function() {
			$('#confirm_delete').modal('toggle');
		});
	</script>
	";
}
?>

<div id="confirm_delete" div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-sm">Small modal</button>
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Close</span>
        </button>
        <img class="modal-logo" sec="/images/EABLogo.png" alt="EAB Logo" />
    </div>
    <div class="modal-body">
      <form method="get" action="" role="form">
<?php
if(isset($_GET['delete_event']))
{
foreach($delete_event as $event_id)
{
	echo "  
	    <input type=\"text\" name=\"delete_event[]\" value=\"$event_id\" style=\"display: none;\" />";
}
}
?>
        <input type="submit" class="btn btn-default" name="confirm_action" value="Confirm">
        <input type="submit" class="btn btn-default" value="No, Go Back!">
      </form>
    </div>
  </div>
</div>


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

//if screening delete from where program_id  & program_relation_id from program relation table
//only drop program_relation_id from program_relation table if eab and m power






if(isset($_GET['confirm_action']))
{
	
	$delete_array = $_GET['delete_event'];
	$query = "SELECT `program_id` FROM `program_relation_table` WHERE program_relation_id = :program_relation_id;";
	$select_program = $db_connection->prepare($query);
	$select_program->bindParam(':program_relation_id', $event_id, PDO::PARAM_STR);

	$query = "DELETE FROM `program_relation_table` WHERE program_relation_id = :program_relation_id;";
	$delete_relation = $db_connection->prepare($query);
	$delete_relation->bindParam(':program_relation_id', $event_id, PDO::PARAM_STR);

	
	$query = "DELETE FROM `program_table` WHERE program_id = :program_id;";
	$delete_program = $db_connection->prepare($query);
	$delete_program->bindParam('program_id', $program_id, PDO::PARAM_STR);
	
	
	foreach($delete_array as $event_id)
	{
		$select_program->execute();
		$result = $select_program->fetch();
		$program_id = $result['program_id'];
		
		print_r ($result);
		
		$delete_relation->execute();
		
		echo "$program_id";
		
		$delete_program->execute();
	}
}

// Set up query for entire table
$query = 
"SELECT tmp1.program_relation_id, tmp1.date, tmp1.program_name, location_table.location_name
	FROM (
		SELECT program_relation_table.program_relation_id, program_relation_table.date, program_table.program_name, program_relation_table.location_id
		FROM program_relation_table
		JOIN program_table
		ON program_relation_table.program_id = program_table.program_id
	) AS tmp1
	INNER JOIN location_table
	ON tmp1.location_id = location_table.location_id
	ORDER BY tmp1.date ASC;
";
	
$query_user_table = $db_connection->prepare($query);
$query_user_table->execute();

?>
-->
<div class="container no-image">


	<h2>Scheduled Events</h2>
<!-- Table of all users encapsulated by a form to allow checkboxes for quickly modifying account permissions -->

<form method="get" action="admin_event_manage.php">
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
  </table>
</form>

</div>


<br />
