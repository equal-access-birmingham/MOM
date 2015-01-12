<?php
$require_login = true;
$require_verified_account = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

    <title>Edit Profile | Equal Access Birmingham</title>

<?php require($path_to_root . "includes/menu.php"); ?>

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

/**
 * User Information
 */

// Acquiring user First Name, Middle Name, Last Name, Suffix, Gender, DOB, and Phone Number
$query ="
SELECT `person_table`.*
	FROM (
		SELECT `login_relation_table`.`person_id`
		FROM `login_relation_table`
			JOIN `users`
			ON `login_relation_table`.`user_id` = `users`.`user_id`
			WHERE `users`.`user_id` = " . $_SESSION['user_id'] . "
	) AS `login_info`
	JOIN `person_table`
	ON `person_table`.`person_id` = `login_info`.`person_id`;
";
$stmt_user_personal_info = $db_connection->prepare($query);
$stmt_user_personal_info->execute();
$result_user_personal_info = $stmt_user_personal_info->fetchObject();

// DOB format modification
list($dob_year, $dob_month, $dob_day) = explode("-", $result_user_personal_info->dob);

// Phone number format modification
list($before_open_par, $area_code, $region_code, $last_four) = preg_split("/[()-]/", $result_user_personal_info->phone_number);
$area_code = trim($area_code);
$region_code = trim($region_code);
$last_four = trim($last_four);

// Acquiring user school and level
$query = "
SELECT `school_relation_table`.`school_id`, `school_relation_table`.`level_id`
	FROM (
		SELECT `login_relation_table`.`login_relation_id`
			FROM `login_relation_table`
			JOIN `users`
			ON `login_relation_table`.`user_id` = `users`.`user_id`
			WHERE `users`.`user_id` = " . $_SESSION['user_id'] . "
	) `login_info`
	JOIN `school_relation_table`
	ON `school_relation_table`.`login_relation_id` = `login_info`.`login_relation_id`;
";
$stmt_user_academic = $db_connection->prepare($query);
$stmt_user_academic->execute();
$result_user_academic = $stmt_user_academic->fetchObject();

/**
 * Dropdown Information
 */
// Acquiring gender from database for dropdown
$query = "SELECT * FROM `gender_table`;";
$stmt_gender = $db_connection->prepare($query);
$stmt_gender->execute();
// Acquiring schools from database for dropdown
$query = "SELECT * FROM `school_table`;";
$stmt_school = $db_connection->prepare($query);
$stmt_school->execute();

// Acquiring levels from database for dropdown
$query = "SELECT * FROM `level_table`;";
$stmt_level = $db_connection->prepare($query);
$stmt_level->execute();
?>
    <div class="container no-image">
      <!-- clean separation of HTML and PHP -->
      <h2><?php echo $_SESSION['user_name']. ", " . WORDING_EDIT_YOUR_CREDENTIALS; ?></h2>
      
      <!-- edit form for username / this form uses HTML5 attributes, like "required" and type="email" -->
      <form method="post" action="" name="user_edit_form_name" role="form">
        <div class="form-group">
          <label for="user_name"><?php echo WORDING_NEW_USERNAME; ?></label>
          <input id="user_name" class="form-control" type="text" name="user_name" value="<?php echo $_SESSION['user_name']; ?>" pattern="[a-zA-Z0-9]{2,64}" required />
        </div>
        <input class="btn btn-default" type="submit" name="user_edit_submit_user_name" value="<?php echo WORDING_CHANGE_USERNAME; ?>" />
      </form><hr />
      
      <!-- edit form for user's password / this form uses the HTML5 attribute "required" -->
      <form method="post" action="" name="user_edit_form_password" role="form">
        <div class="form-group">
          <label for="user_password_old"><?php echo WORDING_OLD_PASSWORD; ?></label>
          <input id="user_password_old" class="form-control" type="password" name="user_password_old" autocomplete="off" />
        </div>
        <div class="form-group">
          <label for="user_password_new"><?php echo WORDING_NEW_PASSWORD; ?></label>
          <input id="user_password_new" class="form-control" type="password" name="user_password_new" autocomplete="off" />
        </div>
        <div class="form-group">
          <label for="user_password_repeat"><?php echo WORDING_NEW_PASSWORD_REPEAT; ?></label>
          <input id="user_password_repeat" class="form-control" type="password" name="user_password_repeat" autocomplete="off" />
        </div>
        <input class="btn btn-default" type="submit" name="user_edit_submit_password" value="<?php echo WORDING_CHANGE_PASSWORD; ?>" />
      </form><hr />
  
      <!-- edit user's name -->
      <form method="post" action="" role="form">
        <div class="form-group">
          <label for="fname">First Name</label>
          <input id="fname" class="form-control" type="text" name="fname" value="<?php echo $result_user_personal_info->fname; ?>" pattern="[a-zA-Z]{2,64}" required />
        </div>
        <div class="form-group">
          <label for="mname">Middle Name</label>
          <input id="mname" class="form-control" type="text" name="mname" value="<?php echo $result_user_personal_info->mname; ?>" pattern="[a-zA-Z]{2,64}" />
        </div>
        <div class="form-group">
          <label for="lname">Last Name</label>
          <input id="lname" class="form-control" type="text" name="lname" value="<?php echo $result_user_personal_info->lname; ?>" pattern="[a-zA-Z]{2,64}" required />
        </div>
        <div class="form-group">
          <label for="suffname">Suffix</label>
          <input id="suffname" class="form-control" type="text" name="suffname" value="<?php echo $result_user_personal_info->suffname; ?>" pattern="[a-zA-Z\.]{2,64}" />
        </div>
        <input class="btn btn-default" type="submit" name="user_edit_submit_name" value="Change Name" />
      </form><hr />
      
      <!-- edit user gender -->
      <form method="post" action="" role="form">
        <div class="form-group">
          <label for="gender">Gender</label>
          <select id="gender" class="form-control" name="gender">
<?php
while($result = $stmt_gender->fetchObject())
{
	echo "            <option value=\"$result->gender_id\"";
	if($result->gender_id == $result_user_personal_info->gender_id)
	{
		echo " selected";
	}
	echo ">$result->gender</option>\n";
}
?>
          </select>
        </div>
  
        <input class="btn btn-default" type="submit" name="user_edit_submit_gender" value="Change Gender" />
      </form><hr />

    <!-- edit user date of birth -->
      <form method="post" action="" role="form">
        <div class="form-group">
          <label>Date of Birth</label>
          <div class="row">
            <div class="col-xs-4">
              <select id="dob_month" class="form-control" name="dob_month" required>
<?php
$month = array(
	1 => "January",
	2 => "February",
	3 => "March",
	4 => "April",
	5 => "May",
	6 => "June",
	7 => "July",
	8 => "August",
	9 => "September",
	10 => "October",
	11 => "November",
	12 => "December"
);

foreach($month as $number => $word)
{
	echo "                <option value=\"$number\"";
	if($number == $dob_month)
	{
		echo " selected";
	}
	echo ">$word</option>\n";
}
?>
              </select>
            </div>
            <div class="col-xs-4">
              <select id="dob_day" class="form-control" name="dob_day" required>
<?php
for($day = 1; $day < 32; $day++)
{
	echo "                <option value=\"$day\"";
	if($day == $dob_day)
	{
		echo " selected";
	}
	echo ">$day</option>\n";
}
?>
              </select>
            </div>
            <div class="col-xs-4">
              <select id="dob_year" class="form-control" name="dob_year" required>
<?php
$year = date("Y");
$year_begin = $year - 120;
for($year; $year > $year_begin; $year--)
{
	echo "                <option value=\"$year\"";
	if($year == $dob_year)
	{
		echo " selected";
	}
	echo ">$year</option>\n";
}
?>
              </select>
            </div>
          </div>
        </div>
  
        <input class="btn btn-default" type="submit" name="user_edit_submit_dob" value="Change DOB" />
      </form><hr />
  
      <!-- edit form for user email / this form uses HTML5 attributes, like "required" and type="email" -->
      <form method="post" action="" name="user_edit_form_email" role="form">
        <div class="form-group">
          <label for="user_email"><?php echo WORDING_NEW_EMAIL; ?></label>
          <input id="user_email" class="form-control" type="email" name="user_email" value="<?php echo $_SESSION['user_email']; ?>" required />
        </div>
        <input class="btn btn-default" type="submit" name="user_edit_submit_email" value="<?php echo WORDING_CHANGE_EMAIL; ?>" />
      </form><hr />
  
      <!-- edit user phone number -->
      <form method="post" action="" role="form">
        <div class="form-group">
          <label>Phone Number</label>
          <div class="row">
            <div class="col-xs-3">
              <input id="area_code" class="form-control" type="text" name="area_code" value="<?php echo $area_code; ?>" placeholder="###" required />
            </div>
            <div class="col-xs-1 text-center">
              <span>&ndash;</span>
            </div>
            <div class="col-xs-3">
              <input id="region_code" class="form-control" type="text" name="region_code" value="<?php echo $region_code; ?>" placeholder="###" required />
            </div>
            <div class="col-xs-1 text-center">
              <span>&ndash;</span>
            </div>
            <div class="col-xs-4">
              <input id="last_four" class="form-control" type="text" name="last_four" value="<?php echo $last_four; ?>" placeholder="####" required />
            </div>
          </div>
        </div>
        <input class="btn btn-default" type="submit" name="user_edit_submit_phone_number" value="Change Phone Number" />
      </form><hr />
  
      <!-- Edit user's school -->
      <form method="post" action="" role="form">
        <div class="form-group">
          <label for="school">School</label>
          <select id="school" class="form-control" name="school" required>
<?php
while($result = $stmt_school->fetchObject())
{
	echo "            <option value=\"$result->school_id\"";
	if($result->school_id == $result_user_academic->school_id)
	{
		echo " selected";
	}
	echo ">$result->school_name</option>\n";
}
?>
          </select>
        </div>
        <input class="btn btn-default" type="submit" name="user_edit_submit_school" value="Change School" />
      </form><hr />

      <!-- edit user's level -->
      <form method="post" action="" role="form">
        <div class="form-group">
          <label for="level">Level</label>
          <select id="level" class="form-control" name="level" required>
<?php
while($result = $stmt_level->fetchObject())
{
	echo "            <option value=\"$result->level_id\"";
	if($result->level_id == $result_user_academic->level_id)
	{
		echo " selected";
	}
	echo ">$result->level_name</option>\n";
}
?>
          </select>
        </div>
        <input class="btn btn-default" type="submit" name="user_edit_submit_level" value="Change Level" />
      </form><hr />
  
      <!-- backlink -->
    </div>

    <script>
      $(document).ready(function() {
        $('#area_code').autotab({format: 'number', target: '#region_code', maxlength: 3, size: 3});
        $('#region_code').autotab({format: 'number', target: '#last_four', previous: '#area_code', maxlength: 3, size: 3});
        $('#last_four').autotab({format: 'number', previous: '#region_code', maxlength: 4, size: 4});
      });
    </script>

<?php
$stmt_user_personal_info = null;
$stmt_user_academic = null;
$stmt_gender = null;
$stmt_school = null;
$stmt_level = null;
$db_connection = null;
?>

<?php include($path_to_root . 'includes/footer.php'); ?>
