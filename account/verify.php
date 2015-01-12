<?php
$require_login = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");

// Prevents verified users from accessing this page again (might pose a problem with the database if they can...)
if($login->isUserVerified() == true)
{
	header("Location: /index.php");
}
?>

    <title>Verify Account | Equal Access Birmingham</title>

<?php require_once($path_to_root. "includes/menu.php"); ?>

<?php
// show potential errors / feedback (from registration object)
if (isset($registration)) {
    if ($registration->errors) {
        foreach ($registration->errors as $error) {
            echo $error;
        } 
    }
    if ($registration->messages) {
        foreach ($registration->messages as $message) {
            echo $message;
        }
    }
}
?>
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
 * Dropdown Information
 */
// Acquiring genders from database
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
      <h2><?php echo $_SESSION['user_name'] . ", " . WORDING_VERIFY_ACCOUNT_REQUEST; ?></h2>
      
      <!-- edit form for user's password / this form uses the HTML5 attribute "required" -->
      <form method="post" action="/index.php">
        <!-- Edit user password -->
        <div class="form-group">
          <label for="user_password_old"><?php echo WORDING_OLD_PASSWORD; ?>*</label>
          <input id="user_password_old" class="form-control" type="password" name="user_password_old" autocomplete="off" required />
        </div>
        <div class="form-group">
          <label for="user_password_new"><?php echo WORDING_NEW_PASSWORD; ?>*</label>
          <input id="user_password_new" class="form-control" type="password" name="user_password_new" autocomplete="off" required />
        </div>
        <div class="form-group">
          <label for="user_password_repeat"><?php echo WORDING_NEW_PASSWORD_REPEAT; ?>*</label>
          <input id="user_password_repeat" class="form-control" type="password" name="user_password_repeat" autocomplete="off" required />
        </div>
  
        <!-- Edit user First Name -->
        <div class="form-group">
          <label for="fname">First Name*</label>
          <input id="fname" class="form-control" type="text" name="fname" pattern="[a-zA-Z]{2,64}" required />
        </div>
  
        <!-- Edit user Middle Name -->
        <div class="form-group">
          <label for="mname">Middle Name</label>
          <input id="mname" class="form-control" type="text" name="mname" pattern="[a-zA-Z]{2,64}" />
        </div>
        
  
        <!-- Edit user Last Name -->
        <div class="form-group">
          <label for="lname">Last Name*</label>
          <input id="lname" class="form-control" type="text" name="lname" pattern="[a-zA-Z]{2,64}" required />
        </div>
        
  
        <!-- Edit user Suffix -->
        <div class="form-group">
          <label for="suffname">Suffix</label>
          <input id="suffname" class="form-control" type="text" name="suffname" pattern="[a-zA-Z\.]{2,64}" />
        </div>
        
  
        <!-- edit user gender -->
        <div class="form-group">
          <label for="gender">Gender*</label>
          <select id="gender" class="form-control" name="gender" required>

<?php
while($result = $stmt_gender->fetchObject())
{
	echo "            <option value=\"$result->gender_id\">$result->gender</option>\n";
}
?>
          </select>
        </div>
        <div class="form-group">
          <!-- edit user date of birth -->
          <label>Date of Birth*</label>
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
	echo "                <option value=\"$number\">$word</option>\n";
}
?>
              </select>
            </div>
            <div class="col-xs-4">
              <select id="dob_day" class="form-control" name="dob_day">
<?php
for($day = 1; $day < 32; $day++)
{
	echo "                <option value=\"$day\">$day</option>\n";
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
	echo "                <option value=\"$year\">$year</option>\n";
}
?>
              </select>
            </div>
          </div>
        </div>
  
        <!-- edit user phone number -->
        <div class="form-group">
          <label>Phone Number*</label>
          <div class="row">
            <div class="col-xs-3">
              <input id="area_code" class="form-control" type="text" name="area_code" placeholder="###" required />
            </div>
            <div class="col-xs-1 text-center">
              <span>&ndash;</span>
            </div>
            <div class="col-xs-3">
              <input id="region_code" class="form-control" type="text" name="region_code" placeholder="###" required />
            </div>
            <div class="col-xs-1 text-center">
              <span>&ndash;</span>
            </div>
            <div class="col-xs-4">
              <input id="last_four" class="form-control" type="text" name="last_four" placeholder="####" required />
            </div>
          </div>
        </div>

        <!-- Edit user's school -->
        <div class="form-group">
          <label for="school">School*</label>
          <select id="school" class="form-control" name="school" required>
<?php
while($result = $stmt_school->fetchObject())
{
	echo "            <option value=\"$result->school_id\">$result->school_name</option>\n";
}
?>
          </select>
        </div>
  
        <!-- edit user level -->
        <div class="form-group">
          <label for="level">Level*</label>
          <select id="level" class="form-control" name="level" required>
<?php
while($result = $stmt_level->fetchObject())
{
	echo "            <option value=\"$result->level_id\">$result->level_name</option>\n";
}
?>
          </select>
        </div>
  
        <input class="btn btn-default" type="submit" name="user_verify_account" value="<?php echo WORDING_VERIFY_ACCOUNT; ?>" />
      </form><hr />
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

<?php include($path_to_root . "includes/footer.php"); ?>
