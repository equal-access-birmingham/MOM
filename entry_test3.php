<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once("includes/db.php");

try 
{
	$con = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_db, $db_user, $db_pw);
}

catch(PDOException $e)
{
	echo 'Connection failed: ' . $e->getMessage();
}

$query_program = "SELECT `program_id`, `program_name` FROM `program_table`;";
$stmt_program = $con->prepare($query_program);
$stmt_program->execute();

$date = new DateTime("now");

$query_date = "SELECT `program_relation_id`, `program_id`, `date` FROM `program_relation_table` WHERE DATE(`date`) > :start_date;";

$stmt_date = $con->prepare($query_date);
$stmt_date->bindValue(":start_date", $date->format("Y-m-d"), PDO::PARAM_STR);
$stmt_date->execute();

$result = $stmt_date->fetchAll();
$result = json_encode($result);
//echo $result;

$query_role = "SELECT * FROM `role_table`;";
$stmt_role = $con->prepare($query_role);
$stmt_role->execute();

?>

<html>
  <head>
  </head>
  <body>
    <h1>Sign Up Now</h1>
    <form method="get" action="entry_test3.php" name="entryForm">
      <select name="optone" onchange="setOptions(document.entryForm.optone.options[document.entryForm.optone.selectedIndex].value);">
        <option value="" selected></option>
<?php
while($stmt_program->fetch())
{
	echo "<option value=\"$program_id\">$program_name</option>\n";
}
?>
      </select><br /><br />
      <select name="opttwo">
        <option value="" selected>Please select an option above first</option>
      </select>
    </form>
    <script>
      function setOptions(chosen)
      {
        var selbox = document.entryForm.opttwo;
        selbox.options.length = 0;
        var eab_date_json = <?php echo $result; ?>;

        if(eab_date_json[i].program_id == "")
        {
          selbox.options[selbox.options.length] = new Option("Please select an option above first", '');
        }

        for(var i = 0; i < eab_date_json.length; i++)
        {
          console.log(eab_date_json[i].program_id);
          if(eab_date_json[i].program_id == chosen)
          {
            echo "          selbox.options[selbox.options.length] = new Option('$date', '$program_relation_id');\n";
          }
        }
        
        /* if(chosen == "1")
        {

<?php

while($stmt_date->fetch())
{
	if($program_id_date == 1)
	{
		echo "          selbox.options[selbox.options.length] = new Option('$date', '$program_relation_id');\n";
	}
}


?>

        }

        if(chosen == "2")
        {
          selbox.options[selbox.options.length] = new Option('second choice - option one','twoone');
          selbox.options[selbox.options.length] = new Option('second choice - option two','twotwo');
        }
        
        if(chosen == "3")
        {
          selbox.options[selbox.options.length] = new Option('third choice - option one','threeone');
          selbox.options[selbox.options.length] = new Option('third choice - option two','threetwo');
        }
*/
      }

    </script>
  </body>
</html>