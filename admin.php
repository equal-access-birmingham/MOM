

<?php include('includes/header-require_admin.php'); ?>


<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
	
	echo "
		<a href=\"index.php\">" . WORDING_HOME_PAGE . "</a>
		<a href=\"view.php\">" . WORDING_DATA_VIEW . "</a>
		<a href=\"entry_test3.php\">" . WORDING_EVENT_REGISTRATION . "</a>
		<a href=\"index.php?logout\">" . WORDING_LOGOUT . "</a>
		";


if($permission->confirm_action_prompt)
{
	// This is just one way to trigger the $_POST['confirm_action'] variable in the Permissions class
	echo "
<form method=\"post\" action=\"admin.php\">
  <input type=\"submit\" name=\"confirm_action\" value=\"Confirm\" />
  <input type=\"submit\" value=\"No, Go Back!\" />
</form>
	";

}

?>
    
<?php
try
{
	$con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
}
catch(PDOException $e)
{
	echo "Error: " . $e->getMessage();
	die();
}

// Set up query for entire table
$query = "SELECT * FROM `users`;";
$query_user_table = $con->prepare($query);
$query_user_table->execute();

$query=
"SELECT person_table.fname, person_table.lname, person_table.suffname
	FROM `person_table`
	JOIN `login_relation_table`
	ON person_table.person_id = login_relation_table.person_id
	WHERE login_relation_table.user_id = :user_id"; 
$stmt=$con->prepare ($query);
$stmt->bindValue (':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
$stmt->execute ();
$result = $stmt->fetch();

?>


<h2><?php echo $result['fname'] . " " . $result['lname'] . " " . $result['suffname'] . WORDING_ADMIN_EDIT_ACCOUNTS; ?></h2>

<a href="register.php"><?php echo WORDING_REGISTER_NEW_ACCOUNT; ?></a>

<br /><br />

<!-- Table of all users encapsulated by a form to allow checkboxes for quickly modifying account permissions -->
<form method="post" action="admin.php">
  <input type="submit" name="update" value="<?php echo WORDING_UPDATE; ?>" /> 
  <table border="1">
    <tr>
      <th>User Name</th>
      <th>User Email</th>
      <th>User Registration Date</th>
      <th>Admin</th>
      <th>Delete Account</th>
    </tr>
<?php

// Creates table
while($data = $query_user_table->fetchObject())
{
	echo "
    <tr>
      <td>$data->user_name</td>
      <td>$data->user_email</td>
      <td>$data->user_registration_datetime</td>\n";
	
	// Automatically checks admins so that they can be unchecked to remove admin privilege
	echo "      <td><input type=\"checkbox\" name=\"admin[]\" value=\"$data->user_id\" ";
	if($data->admin == 1) echo "checked";
	echo " /></td>\n";

// Creates checkbox arrays for "reset_account" and "delete_account" so that multiple actions can be selected at once
	echo "
      <td><input type=\"checkbox\" name=\"delete_account[]\" value=\"$data->user_id\" /></td>
    </tr>\n";
}

?>

   </table>
</form>

<br />
