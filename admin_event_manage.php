<?php
require('includes/header-require_admin.php');
require_once('includes/menu_sign_in.php');

error_reporting(E_ALL);
ini_set("display_errors", 1);



if(isset($_GET['admin_event_manage']))
{
echo "
	<form method=\"get\" action=\"admin_event_manage.php\">
";
$delete_event=$_GET['delete_event'];
	// This is just one way to trigger the $_POST['confirm_action'] variable in the Permissions class
foreach($delete_event as $event_id)
{
	echo "
  <input type=\"text\" name=\"delete_event[]\" value=\"$event_id\" style=\"display: none;\" />\n";
}
echo "
  <input type=\"submit\" name=\"confirm_action\" value=\"Confirm\" />
  <input type=\"submit\" value=\"No, Go Back!\" />
</form>
	";
}


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

<!-- Table of all users encapsulated by a form to allow checkboxes for quickly modifying account permissions -->
<table class="table table-hover"> 
<form method="get" action="admin_event_manage.php">
  <input type="submit" name="admin_event_manage" value="Delete Events" />
  <table border="1">
    <tr>
      <th>Select</th>
      <th>Date</th>
      <th>Event</th>
      <th>Location</th>
      
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
</table>


<br />
