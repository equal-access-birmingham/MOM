<?php
$require_login = true;
$require_admin = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

    <title>Account Management | Equal Access Birmingham</title>

<?php require_once($path_to_root . "includes/menu.php"); ?>

<?php
try {
	$db_connection = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
}
catch(PDOException $e) {
	echo "Error: " . $e->getMessage();
}

/**
 * Queries for dropdown
 */

// Get all schools from database
$query = "SELECT * FROM `school_table`;";
$stmt_school = $db_connection->prepare($query);
$stmt_school->execute();

// Get all levels from database
$query = "SELECT * FROM `level_table`;";
$stmt_level = $db_connection->prepare($query);
$stmt_level->execute();

// Confirmation of database update
if($permissions->confirm_action_prompt)
{
	// This is just one way to trigger the $_POST['confirm_action'] variable in the Permissions class
	echo "
    <script>
      $(document).ready(function() {
        $('#confirm_action').modal('toggle');
      });
    </script>
	";
}
?>

    <!-- Modal to confirm action (modify admin status, reset account, delete account) -->
    <div id="confirm_action" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmAction" aria-hidden="true">
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
            <h4 class="modal-body-header"><strong>Confirm Action</strong></h4>
            <ul>
<?php
// show potential errors / feedback (from Permissions object)
// In this case, this shows what actions will occur if "Confirm" is pressed
if (isset($permissions)) {
    if ($permissions->errors) {
        foreach ($permissions->errors as $error) {
            echo "          <li>$error</li>";
        }
    }
    if ($permissions->messages) {
        foreach ($permissions->messages as $message) {
            echo "          <li>$message</li>";
        }
    }
}
?>
            </ul>
          </div>
          <div class="modal-footer">
            <form method="post" action="" role="form">
              <button class="btn btn-success" type="button" data-dismiss="modal">No, Go Back</button>
              <input class="btn btn-danger" type="submit" name="confirm_action" value="Confirm" />
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="container no-image">
      <h2><?php echo $_SESSION['user_name']. ", " . WORDING_ADMIN_EDIT_ACCOUNTS; ?></h2>

      <!-- Form for AJAX submission for name search -->
      <h3>User Search</h3>
      <form class="form-inline" method="post" action="" role="form">
        <span id="key_event">
          <div class="form-group">
            <input id="user_name_search" class="form-control" type="text" name="user_name" placeholder="User Name" />
          </div>
          <div class="form-group">
            <input id="fname" class="form-control" type="text" name="fname" placeholder="First Name" />
          </div>
          <div class="form-group">
            <input id="lname" class="form-control" type="text" name="lname" placeholder="Last Name" />
          </div>
        </span>
        <span id="change_event">
          <div class="form-group">
            <select id="school" class="form-control" name="school">
              <option value="">-- School --</option>
<?php
while($result = $stmt_school->fetchObject())
{
	echo "              <option value=\"$result->school_id\">$result->school_name</option>\n";
}
?>
            </select>
          </div>
          <div class="form-group">
            <select id="level" class="form-control" name="level">
              <option value="">-- Level --</option>

<?php
while($result = $stmt_level->fetchObject())
{
	echo "              <option value=\"$result->level_id\">$result->level_name</option>\n";
}
?>
            </select>
          </div>
        </span>
      </form><hr />

      <!-- Form completed by AJAX search that returns searched names -->
      <h3>User Administration</h3>
      <form method="post" action="" role="form">
        <div class="form-group">
          <input class="btn btn-default" type="submit" name="update_accounts" value="<?php echo WORDING_UPDATE; ?>" />
        </div>

        <table id="user_table" class="table table-striped text-center">
        </table>
      </form>
    </div>


    <script>
      function search_volunteers(user_name, fname, lname, level, school)
      {
        /**
         * Setting up the ajax object
         */
        var xmlhttp;

        // Setting up object for all browser other than IE 6
        if(window.XMLHttpRequest)
        {
          xmlhttp = new XMLHttpRequest();
        }

        // Setting up object for IE 6
        else
        {
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function()
        {
          // If browser is ready...
          if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
          {
            // display response (see account_management_table.php) in table with id="user_table"
            document.getElementById("user_table").innerHTML = xmlhttp.responseText;
          
            // adjust footer based on body content size
            footerDrop();
          }
        }

        // Sends the variable values to the file via HTTP GET
        xmlhttp.open("GET", "admin_management_table.php?user_name="+user_name+"&fname="+fname+"&lname="+lname+"&level="+level+"&school="+school, true);
        xmlhttp.send();
      }

      // Applies keyup event to <span> encapsulating text inputs so that table returns based on name entry
      var key_event = document.getElementById("key_event");
      key_event.addEventListener("keyup", return_table, false);

      // Applies change event to <span> encapsulating dropdown inputs so that table returns based on academic selection
      var change_event = document.getElementById("change_event");
      change_event.addEventListener("change", return_table, false);

      // Loads the table when the document loads
      document.body.onload = return_table;

      function return_table(event)
      {
        var user_name_input = document.getElementById("user_name_search");
        var fname_input = document.getElementById("fname");
        var lname_input = document.getElementById("lname");
        var school_input = document.getElementById("school");
        var level_input = document.getElementById("level");

        if(event.target !== event.currentTarget || event == null)
        {
          search_volunteers(user_name_input.value, fname_input.value, lname_input.value, level_input.value, school_input.value);
          //console.log(user_name_input.value + fname_input.value + lname_input.value + level_input.value + school_input.value);
        }
      }
    </script>

<?php
// Close all stmts and db_connection
$stmt_school = null;
$stmt_level = null;
$db_connection = null;
?>
<?php include($path_to_root . "includes/footer.php"); ?>