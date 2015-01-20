<?php
$require_login = true;
$require_admin = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

<?php
// Gets HTTP variables from account_management.php
$user_name = strtolower($_GET['user_name']);
$fname = strtolower($_GET['fname']);
$lname = strtolower($_GET['lname']);
$school = $_GET['school'];
$level = $_GET['level'];

echo "
          <tr>
            <th class=\"text-center\">User Name</th>
            <th class=\"text-center\">First Name</th>
            <th class=\"text-center\">Last Name</th>
            <th class=\"text-center\">User Email</th>
            <th class=\"text-center\">School Name</th>
            <th class=\"text-center\">Level Name</th>
            <th class=\"text-center\">Admin</th>
            <th class=\"text-center\">Reset Account</th>
            <th class=\"text-center\">Delete Account</th>
          </tr>
";

foreach($permissions->getEveryUsersData($user_name, $fname, $lname, $school, $level) as $result)
{
	echo "
          <tr>
            <td>$result->user_name</td>
            <td>$result->fname</td>
            <td>$result->lname</td>
            <td><a href=\"mailto:$result->user_email\">$result->user_email</a></td>
            <td>$result->school_name</td>
            <td>$result->level_name</td>\n";

	// Automatically checks admins so that they can be unchecked to remove admin privilege
	// Addition of the hidden field allows checking to see whether value was even submitted for admin removal in the first place
		// If the hidden field is not submitted, then the person didn't really uncheck anything
		// This is necessary because AJAX limits the form and the $permissions object only knows if something is unselected if it is not sent by default with the form
	echo "
            <td>
              <input type=\"hidden\" name=\"admin_check_submit[]\" value=\"$result->user_id\" />
              <input type=\"checkbox\" name=\"admin[]\" value=\"$result->user_id\" ";
	if($result->admin == 1) echo "checked";
	echo " />
            </td>\n";

	// Creates checkbox arrays for "reset_account" and "delete_account" so that multiple actions can be selected at once
	echo "
            <td><input type=\"checkbox\" name=\"reset_account[]\" value=\"$result->user_id\" /></td>
            <td><input type=\"checkbox\" name=\"delete_account[]\" value=\"$result->user_id\" /></td>
          </tr>\n";
}
?>

