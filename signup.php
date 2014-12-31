<?php require_once("includes/header.php"); ?>

    <title>Sign Up</title>

<?php require_once("includes/menu.php"); ?>

<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

try
{
	$con = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
}

catch(PDOException $e)
{
	echo 'Connection failed: ' . $e->getMessage();
}

/*
	echo "
		<a href=\"index.php\">" . WORDING_HOME_PAGE . "</a>
		<a href=\"view.php\">" . WORDING_DATA_VIEW . "</a>
		<a href=\"signup.php\">" . WORDING_EVENT_REGISTRATION . "</a>
		<a href=\"index.php?logout\">" . WORDING_LOGOUT . "</a>
	";
*/

//grab variables using $_GET['']
$program_relation_id = $_GET['date'];
$role_id = $_GET['role'];
$login_relation_id = $_GET['login_relation_id'];

if(isset($_GET['signup']))
{
	echo "
    <script>
      //this code allows the page to load before activating
      //modal, defined below in html, is activated when 'submit' button is pressed
      $(document).ready(function(){
        $('#signup_confirm_modal').modal('toggle');
      });
    </script>
	";
}

else if(isset($_GET['confirm']))
{
	//to prevent insertion in the case that the same person attempts to sign up for the same day twice
	$query_double_trouble = "SELECT COUNT(*) AS count FROM `signup_table` WHERE `login_relation_id` = :login_relation_id AND `program_relation_id` = :program_relation_id;";
	$stmt_double_trouble = $con->prepare($query_double_trouble);
	$stmt_double_trouble->bindValue(':login_relation_id', $login_relation_id, PDO::PARAM_STR);
	$stmt_double_trouble->bindValue(':program_relation_id', $program_relation_id, PDO::PARAM_STR);
	$stmt_double_trouble->execute();
	$result_double_trouble = $stmt_double_trouble->fetch();
	if(!$result_double_trouble['count'])
	{
	//! means "not" -- i.e., that hasnt occurred, so insert signup data
	//insert data into database
		$query_signup = "INSERT INTO `signup_table` (`login_relation_id`, `program_relation_id`, `role_id`) VALUES (:login_relation_id, :program_relation_id, :role_id);";
		$stmt_signup = $con->prepare($query_signup);
		$stmt_signup->bindValue(':login_relation_id', $login_relation_id, PDO::PARAM_STR);
		$stmt_signup->bindValue(':program_relation_id', $program_relation_id, PDO::PARAM_STR);
		$stmt_signup->bindValue(':role_id', $role_id, PDO::PARAM_STR);
		$stmt_signup->execute();
	}
	echo "
    <script>
      $(document).ready(function(){
        $('#signup_success_modal').modal('toggle');
      });
    </script>
	";
}

//grabbing programs
$query_program = "SELECT `program_id`, `program_name` FROM `program_table`;";
$stmt_program = $con->prepare($query_program);
$stmt_program->execute();

//grabbing dates
$date = new DateTime("now");
$query_date = "SELECT `program_relation_id`, `program_id`, `date` FROM `program_relation_table` WHERE DATE(`date`) > :start_date;";
$stmt_date = $con->prepare($query_date);
$stmt_date->bindValue(":start_date", $date->format("Y-m-d"), PDO::PARAM_STR);
$stmt_date->execute();
$result_date = $stmt_date->fetchAll();
$result_date = json_encode($result_date);

//grabs role logistic info for purpose of limiting roles based on program chosen as well as level & position of user
$query_role_program_type_level_position = "
  SELECT `role_table`.`role_name`, `temp1`.*
    FROM (
      SELECT `program_table`.`program_id`, `program_table`.`program_name`, `temp2`.*
        FROM (
          SELECT `role_program_type_relation_table`.`role_program_relation_id`, `role_program_type_relation_table`.`program_type_id`, `temp3`.*
            FROM (
              SELECT `role_level_relation_table`.*, `role_position_relation_table`.`role_position_relation_id`, `role_position_relation_table`.`position_id`
                FROM `role_level_relation_table`
                INNER JOIN `role_position_relation_table`
                ON `role_level_relation_table`.`role_id` = `role_position_relation_table`.`role_id`
            ) AS `temp3`
            INNER JOIN `role_program_type_relation_table`
            ON `role_program_type_relation_table`.`role_id` = `temp3`.`role_id`
        ) AS `temp2`
        INNER JOIN `program_table`
        ON `program_table`.`program_type_id` = `temp2`.`program_type_id`
    ) AS `temp1`
    INNER JOIN `role_table`
    ON `role_table`.`role_id` = `temp1`.`role_id`;
";
$stmt_role_program_type_level_position = $con->prepare($query_role_program_type_level_position);
$stmt_role_program_type_level_position->execute();
$result_role_program_type_level_position = $stmt_role_program_type_level_position->fetchAll();
$result_role_program_type_level_position = json_encode($result_role_program_type_level_position);

//grabs signup data for purpose of limiting role based on scheduled signups
$query_role_signup = "
  SELECT `program_relation_table`.`date`, `temp1`.*, COUNT(program_relation_table.date) AS role_count
    FROM (
      SELECT `role_table`.`role_name`, `signup_table`.`role_id`, `signup_table`.`program_relation_id`
        FROM `role_table`
        LEFT JOIN `signup_table`
        ON `role_table`.`role_id` = `signup_table`.`role_id`
    ) AS `temp1`
    LEFT JOIN `program_relation_table`
    ON `temp1`.`program_relation_id` = `program_relation_table`.`program_relation_id` GROUP BY temp1.role_name, program_relation_table.date;
";
$stmt_role_signup = $con->prepare($query_role_signup);
$stmt_role_signup->execute();
$result_role_signup = $stmt_role_signup->fetchAll();
$result_role_signup = json_encode($result_role_signup);
//visualize how this looks in the table, then visualize how this transcribes to json --> here, we're selecting role_name, date, and role_count
//first row will be represented in json as:
//  1st row of table --> [{"role_name":value, "date":value, ...},
//  2nd row of table --> {"role_name":value, "date":value, ...}]
//json_object[#].date -- # = row_number & date = column_name

//grabs roles for json object building
$query_role_name = "SELECT * FROM `role_table`;";
$stmt_role_name = $con->prepare($query_role_name);
$stmt_role_name->execute();
$result_role_name = $stmt_role_name->fetchAll();
$result_role_name = json_encode($result_role_name);

//grabs user's info and privileges
$user_id = $_SESSION['user_id'];
$query_user_id = "
      SELECT `login_relation_table`.`person_id`, `login_relation_table`.`user_id`, `temp1`.*
        FROM (
          SELECT `school_relation_table`.*, `position_relation_table`.`position_relation_id`, `position_relation_table`.`position_id`
            FROM `school_relation_table`
            INNER JOIN `position_relation_table`
            ON `school_relation_table`.`login_relation_id` = `position_relation_table`.`login_relation_id`
        ) AS `temp1`
        INNER JOIN `login_relation_table`
        ON `login_relation_table`.`login_relation_id` = `temp1`.`login_relation_id`
        WHERE `login_relation_table`.`user_id` = :user_id;
";
$stmt_user_id = $con->prepare($query_user_id);
$stmt_user_id->bindValue(':user_id', $user_id, PDO::PARAM_STR);
$stmt_user_id->execute();
$result_user_id = $stmt_user_id->fetch();

//grabs user's info and privileges; limit based on program_relation_id
$query_program_info = "
    SELECT `program_table`.`program_name`, `program_relation_table`.`program_relation_id`, `program_relation_table`.`date`
        FROM `program_table`
        INNER JOIN `program_relation_table`
        ON `program_relation_table`.`program_id` = `program_table`.`program_id`
        WHERE `program_relation_table`.`program_relation_id` = :program_relation_id;
";
$stmt_program_info = $con->prepare($query_program_info);
$stmt_program_info->bindValue(':program_relation_id', $program_relation_id, PDO::PARAM_STR);
$stmt_program_info->execute();
$result_program_info = $stmt_program_info->fetch();

//grabs roles for json object building; limit based on role_id
$query_role_info = "SELECT * FROM `role_table` WHERE `role_table`.`role_id` = :role_id;";
$stmt_role_info = $con->prepare($query_role_info);
$stmt_role_info->bindValue(':role_id', $role_id, PDO::PARAM_STR);
$stmt_role_info->execute();
$result_role_info = $stmt_role_info->fetch();

$date_signup_confirm = new DateTime($result_program_info['date']);

?>

    <!-- signup confirmation modal -->
    <div id="signup_confirm_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
            <h2 class="modal-body-header">Sign Up Confirmation</h2>
            <ul>
              <li> Program Name: <?php echo  $result_program_info['program_name']; ?></li>
              <li> Date: <?php echo  $date_signup_confirm->format("F j, Y"); ?></li>
              <li> Role Name: <?php echo  $result_role_info['role_name']; ?></li>
            </ul>
          </div>
          <div class="modal-footer">
            <?php echo "<a href=\"signup.php?confirm=1&login_relation_id=$login_relation_id&date=$program_relation_id&role=$role_id\" class=\"btn btn-success\">Yes</a>"; ?>
            <a href="signup.php" class="btn btn-danger">No</a>
          </div>
        </div>
      </div>
    </div>

    <!-- signup success modal -->
    <div id="signup_success_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
            <h2 class="modal-body-header">Sign Up Confirmation</h2>
<?php
//only trigger this php if confirm is set, so as to wait for variables to be set
if(isset($_GET['confirm']))
{
	//if the user has not already signed up for the selected day, allow them to proceed
	if(!$result_double_trouble['count'])
	{
		//check to make sure that the data made it into the table
		if($stmt_signup->rowCount())
		{
			echo "
            <p>Congratulations! Here are your signup details:</p>
            <ul>
              <li> Program Name: " . $result_program_info['program_name'] . "</li>
              <li> Date: " . $date_signup_confirm->format("F j, Y") . "</li>
              <li> Role Name: " . $result_role_info['role_name'] . "</li>
            </ul>
			";
		}

		else
		{
			echo "            <p>Error: signup could not be completed. Please contact the EAB Volunteer Coordinator.</p>";
		}
	}
	//if the user has already signed up for the selected day, inform them that they cannot do so twice
	else
	{
		echo "            <p>Error: you cannot sign up twice for the same day. Please contact the EAB Volunteer Coordinator if you have any questions.</p>";
	}
}
?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-eab" data-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>

    <div class="container no-image">
      <h1>Sign Up Now</h1>
      <form method="get" action="signup.php" name="entryForm" role="form">
        <div class="form-group">
          <input type="text" name="login_relation_id" value="<?php echo $result_user_id['login_relation_id'] ?>" style="display: none;" class="form-control" />
        <!-- style contains CSS, which explains the strange syntax (i.e. ;)
        this keeps the info hidden -->
        </div>
        <div class="form-group">
          <select name="program" class="form-control">
            <option value="" selected>Please select an option</option>

<?php
while($result_program = $stmt_program->fetch())
{
	echo "          <option value=\"" . $result_program['program_id'] . "\">" . $result_program['program_name'] . "</option>\n";
}
?>

          </select>
        </div>
        <div class="form-group">
          <select name="date" class="form-control">
          <!-- note that this is selecting program_relation_id -->
            <option value="" selected>Please select an option above first</option>
          </select>
        </div>
        <div class="form-group">
          <select name="role" class="form-control">
            <option value="" selected>Please select an option above first</option>
          </select>
        </div>
        <input type="submit" name="signup" value="Sign Up" class="btn btn-primary"/>

      </form>
    </div>
    <script>
      var program_select = document.entryForm.program;
      var date_select = document.entryForm.date;
      var role_select = document.entryForm.role;

//this function limits date based on program selected
      function setDate(program, date)
      {
        date_select.options.length = 0;
        role_select.options.length = 1;

        var eab_date_json = <?php echo $result_date; ?>;

//if program is not null, then start listing dates
        if(program != "")
        {
          date_select.options[date_select.options.length] = new Option("Please select an option", "");
          //prints all available dates for selected program
          for(var i = 0; i < eab_date_json.length; i++)
          {
            if(eab_date_json[i].program_id == program)
            {
              date_select.options[date_select.options.length] = new Option(eab_date_json[i].date, eab_date_json[i].program_relation_id);
            }
          }
        }

//if program is null, then tell user to select program first
        else
        {
          date_select.options[date_select.options.length] = new Option("Please select an option above first", "");
            //here, the first clause in the parantheses is what's appearing on the page, and the second is what its value is
          //this function forces dates to be reset when all dropdowns are filled and first dropdown is changed
          setTimeout(function(){
            if(date_select.value == "")
            {
              role_select.options.length = 0;
              role_select.options[role_select.options.length] = new Option("Please select an option above first", "");
            }
          }, 1);
          //this is to rapidly reset the value of dropdown 2 (date_select) to "", so that dropdown 3 (role_select) displays "Please select an option first" rather than "Please select an option" -- however, this is a workaround and RAM-taxing
          //there is a perceptible lag when resetting all dropdowns -- fine for now, but keep this in mind for future updates
        }
      }

//this function limits roles based on program & date selected
      function setRole(program, date)
      {
        role_select.options.length = 0;

        var eab_user_position = <?php echo $result_user_id['position_id']; ?>;
        var eab_user_level = <?php echo $result_user_id['level_id']; ?>;
        var eab_role_program_type_level_position_json = <?php echo $result_role_program_type_level_position; ?>;
        var eab_role_signup_json = <?php echo $result_role_signup; ?>;
        var eab_role_name_json = [];

//this for loop builds the list of roles based on program type selected as well as level & position of user
        for(var role_name_row = 0; role_name_row < eab_role_program_type_level_position_json.length; role_name_row++)
        {
          if(eab_role_program_type_level_position_json[role_name_row].position_id == eab_user_position)
          {
            if(eab_role_program_type_level_position_json[role_name_row].level_id == eab_user_level)
            {
              if(eab_role_program_type_level_position_json[role_name_row].program_id == program)
              {
                eab_role_name_json.push({
                  "role_id": eab_role_program_type_level_position_json[role_name_row].role_id,
                  "role_name": eab_role_program_type_level_position_json[role_name_row].role_name
                });
              }
            }
          }
        }

//if date is not null, attempt to list proper set of roles
        if(date != "")
        {
          role_select.options[role_select.options.length] = new Option("Please select an option", "");
          for(var role_name_row = 0; role_name_row < eab_role_signup_json.length; role_name_row++)
          {
            //rather than doing eab_role_signup_json[role_name_row].date == date, you set eab_role_signup_json[role_name_row].program_relation_id == date instead because the dates will never be equal; using program_relation_id to submit into the signup table as it is equivalent to the date
            //note [role_name_row] = [i]
            if(eab_role_signup_json[role_name_row].program_relation_id == date) 
            {
              //this statement checks to see whether more than 2 Medical H&P's are signed up, and removes the role from the printed list if so
              if(eab_role_signup_json[role_name_row].role_name == "Medical H&P" && eab_role_signup_json[role_name_row].role_count > 2)
              {
                removeRoleName(eab_role_signup_json[role_name_row].role_id, eab_role_name_json);
              }
              //this statement checks to see whether more than 0 non-Medical H&P's are signed up (i.e. any role that's not Medical H&P), and removes said role from the printed list if so
              else if(eab_role_signup_json[role_name_row].role_name != "Medical H&P" && eab_role_signup_json[role_name_row].role_count > 0)
              {
                removeRoleName(eab_role_signup_json[role_name_row].role_id, eab_role_name_json);
              }
            }
          }
          //prints out what's left of eab_role_name_json -- i.e., the legitimate roles
          for(var role_name_row = 0; role_name_row < eab_role_name_json.length; role_name_row++)
          {
            role_select.options[role_select.options.length] = new Option(eab_role_name_json[role_name_row].role_name, eab_role_name_json[role_name_row].role_id);
            //use "" only when not using a variable
          }
        }

        else
        {
          role_select.options[role_select.options.length] = new Option("Please select an option above first", "");
        }

      }

//removes role from json_array when certain conditions are met
      function removeRoleName(role, role_array)
      {
        for(var role_name_row = 0; role_name_row < role_array.length; role_name_row++)
        {
          if(role_array[role_name_row].role_id == role)
          {
            role_array.splice(role_name_row, 1);
            return role_array;
          }
        }
        return role_array;
        //if ask it to remove a role that isnt in role array, this is activated to return a role
      }

//these onchanges actually trigger the above functions
      program_select.onchange = function(){
        setDate(program_select.value, date_select.value);
        };

      date_select.onchange = function(){
        setRole(program_select.value, date_select.value);
        };

    </script>

<?php require_once("includes/footer.php"); ?>