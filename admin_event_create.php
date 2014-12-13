<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
<html>
  <head>
    <title>Create Event(s)</title>
  </head>
  <body>
    <h1>Create Clinic Event</h1>
<!--drop down menu to selct either EAB or M-Power-->
<form method="get" action="admin_event_create_success.php" name="eventcreationform">
    <label for="program_id">Create Event</label>
    <select name="program_id" required>
      <option value="">--Select--</option>
      <option value="1">EAB Clinic</option>
      <option value="2">M-Power Clinic</option>
    </select><br />
    <label>Date</label>
    <select name="date" required>
	<option value="" selected>--Select--</option>
<?php
// Setting up dates to find first day in line, Sunday or Wednesday
$dateSunday = new DateTime("now", new DateTimeZone('America/Chicago'));
$dateWednesday = new DateTime("now", new DateTimeZone('America/Chicago'));

$dateSunday->modify("next Sunday");
$dateWednesday->modify("next Wednesday");

if($dateSunday < $dateWednesday)
{
	$day_array = array("Sunday", "Wednesday");
	$date = $dateSunday;
}
else
{
	$day_array = array("Wednesday", "Sunday");
	$date = $dateWednesday;
}

$week = 1;

for($week; $week <= 8; $week++)
{
	if($week == 1)
	{
		echo "<option value=\"" . $date->format("Y-m-d") . "\">" . $date->format("l, F j, Y") . "</option>";
		
		$date->modify("next " . $day_array[1]);
		
		echo "<option value=\"" . $date->format("Y-m-d") . "\">" . $date->format("l, F j, Y") . "</option>";
	}
	else
	{
		foreach($day_array as $day)
		{
			$add = "next $day";
			$date->modify($add)->format("Y-m-d");
			echo "<option value=\"" . $date->format("Y-m-d") . "\">" . $date->format("l, F j, Y") . "</option>";
		}
	}
}
?>
    </select>
<!--This allows creation of repeated events on the same day of the week at the same time-->
    <br />
    <label for="clinic_repeats">How many consecutive events?</label>
    <input id="clinic_repeats" type="text" pattern="[0-9]{1,99}" name="clinic_repeats" required />
    <br />
    </form>
    <input type="submit" name="eventcreation" value="Submit" />
    
    <h1>Create Screening Event</h1>
    
<form method="get" action="admin_event_create_screening.php" name="screeningform">
    <br />
    
    <label for="program_type_id">Screening?</label>
    <select name="program_type_id" required>
    
      <option value="">No</option>
      <option value="3">Yes</option>
    </select>
      
    <br />
    
    
    <label for="name">Name of Event</label>
    <input id="name" type="text" pattern="[a-zA-Z0-9!@#$%^&* ]{1,99}" name="name" required />
    
    <br />

    <label for="location_name">Location Name</label>
    <input id="location_name" type="text" pattern="[a-zA-Z0-9!@#$%^&* ]{1,99}" name="location_name" required />

    
    <br />
    
    <label for="location_address">Location Address</label>
    <input id="location" type="text" pattern="[a-zA-Z0-9!@#$%^&*\. ]{1,99}" name="location_address" required />

	<br />
	
    
    <label for="screening_month">Date</label>
    <select class="form-control" name="screening_month" required>
        <option value="">--Month--</option>
        <option value="1">January</option>
        <option value="2">February</option>
        <option value="3">March</option>
        <option value="4">April</option>
        <option value="5">May</option>
        <option value="6">June</option>
        <option value="7">July</option>
        <option value="8">August</option>
        <option value="9">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>

              
    <label for="screening_day"></label>          
    <select class="form-control" name="screening_day" required>
        <option value="">--Day--</option>
<?php
for($x = 1; $x < 32; $x++)
{
	echo "              <option value=\"$x\">$x</option>\n";
}
?>
    </select>
              
    <label for="screening_year"></label>          
    <select class="form-control" name="screening_year" required>
        <option value="">--Year--</option>
<?php
$x = date("Y");
$y = $x + 2;
for($x; $x < $y; $x++)
{
	echo "              <option value=\"$x\">$x</option>\n";
}
?>
	</select>
	<br />

    Start Time
  
	<select class="form-control" name="start_time_hour" required>
	<option value="">--Hour--</option>
<?php
    for ($i=1; $i<=12; $i++)
    {
        ?>
            <option value="<?php echo $i;?>"><?php echo $i;?></option>
        <?php
    }
?>

</select>
	:  
	<select class="form-control" name="start_time_minute" required>
	<option value="">--Minute--</option>
<?php
    for ($i=0; $i<=59; $i++)
    {
    if($i <=9) {
	$i = "0".$i;
	}
        ?>
            <option value="<?php echo $i;?>"><?php echo $i;?></option>
        <?php
    }
?>
	</select>
    <select class="form-control" name="start_time_ampm" required>
        <option value="">--AM/PM--</option>
        <option value="1">AM</option>
        <option value="2">PM</option>
    </select>
    <br />
    
     End Time
  
	<select class="form-control" name="end_time_hour" required>
	<option value="">--Hour--</option>
<?php
    for ($i=1; $i<=12; $i++)
    {
        ?>
            <option value="<?php echo $i;?>"><?php echo $i;?></option>
        <?php
    }
?>

</select>
	:  
	<select class="form-control" name="end_time_minute" required>
	<option value="">--Minute--</option>
<?php
    for ($i=1; $i<=59; $i++)
    {
    if($i <=9) {
	$i = "0".$i;
	}
        ?>
            <option value="<?php echo $i;?>"><?php echo $i;?></option>
        <?php
    }
?>
	</select>
    <select class="form-control" name="end_time_ampm" required>
        <option value="">--AM/PM--</option>
        <option value="1">AM</option>
        <option value="2">PM</option>
    </select>


    <br />
    
    Officer Arrival Time
  
	<select class="form-control" name="arrival_time_hour" required>
	<option value="">--Hour--</option>
<?php
    for ($i=1; $i<=12; $i++)
    {
        ?>
            <option value="<?php echo $i;?>"><?php echo $i;?></option>
        <?php
    }
?>

</select>
	:  
	<select class="form-control" name="arrival_time_minute" required>
	<option value="">--Minute--</option>
<?php
    for ($i=1; $i<=59; $i++)
    {
    if($i <=9) {
	$i = "0".$i;
	}
        ?>
            <option value="<?php echo $i;?>"><?php echo $i;?></option>
        <?php
    }
?>
	</select>
    <select class="form-control" name="arrival_time_ampm" required>
        <option value="">--AM/PM--</option>
        <option value="1">AM</option>
        <option value="2">PM</option>
    </select>
    
    <br />
    <input type="submit" name="screeningcreation" value="Submit" />
    
</form>
    
  </body>
<!-- if the clinic to be made is EAB. the program_id will be 1, the location_id will be 1. Program_id taken care of with the form. The start time will be 13:00.00 and the end time will be 18:00:00. The date will only occur on sundays. -->
</html>
<!-- $date = new = DateTime();
//$date->format("Y-m-d")

//$date_submit = $_GET['date'];
//process $date_submit
//$date = new DateTime($date_submit);
//for $i
-->
