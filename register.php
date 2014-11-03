<?php  require('includes/header.php'); ?>

<!-- load the registration class -->
<?php require_once('phploginadvanced/classes/Registration.php'); ?>



<!-- create the registration object. when this object is created, it will do all of the registration stuff automatically
so this single line handles the entire registration process. -->
<?php $registration = new Registration(); ?>

<!-- showing the register view (with the registration form, and messages/errors) -->

<!-- show registration form, but only if we didn't submit already -->


<?php if (!$registration->registration_successful && !$registration->verification_successful) { ?>
<form method="post" action="register.php" name="registerform">
    
    <?php echo WORDING_REGISTRATION_REQUIREMENTS; ?>
    </br>
    </br>
    <label for="user_name"><?php echo WORDING_REGISTRATION_USERNAME; ?></label>
    </br>
    <input id="user_name" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />
	</br>
	</br>

    <label for="user_email"><?php echo WORDING_REGISTRATION_EMAIL; ?></label>
    </br>
    <input id="user_email" type="email" name="user_email" required />
    </br>
    </br>

    <label for="user_password_new"><?php echo WORDING_REGISTRATION_PASSWORD; ?></label>
    <input id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />
    </br>

    <label for="user_password_repeat"><?php echo WORDING_REGISTRATION_PASSWORD_REPEAT; ?></label>
    <input id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />
    </br>
    </br>

    <label for="f_name"><?php echo WORDING_REGISTRATION_FNAME; ?></label>
    <input id="f_name" type="text" pattern="[a-zA-Z]*" name="fname" required />
    </br>
    </br>
    
    <label for="mname"><?php echo WORDING_REGISTRATION_MNAME; ?></label>
    <input id="mname" type="text" pattern="[a-zA-Z]*" name="mname" />
    </br>
    </br>
    
    <label for="lname"><?php echo WORDING_REGISTRATION_LNAME; ?></label>
    <input id="lname" type="text" pattern="[a-zA-Z]*" name="lname" required />
    </br>
    </br>
    
    <label for="suffname"><?php echo WORDING_REGISTRATION_SUFFNAME; ?></label>
    <input id="suffname" type="text" pattern="[a-zA-Z]+[.]*" name="suffname" />
    </br>
    </br>
    
    <label for="gender_id"><?php echo WORDING_REGISTRATION_GENDER; ?></label>
    <select class="form-control" name="gender_id" required>
        
    
<?php
$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$query_gender = "SELECT * from gender_table";
$stmt_gender = $con->prepare($query_gender);
$stmt_gender->bind_result($gender_id, $gender);
$stmt_gender->execute();
	
while($stmt_gender->fetch())
{
	echo "        <option value=\"$gender_id\"";
	if($gender == "Other") echo "selected";
	echo ">$gender</option>\n";
}
?>
    </select>
    <br />
    <br />
    
            
        
    <?php echo WORDING_REGISTRATION_PHONE_NUMBER; ?>
    </br>   
    (   
    <label for="phone_number_area_code"></label>
    <input id="phone_number_area_code" type="text" pattern="[0-9]{3}" name="phone_number_area_code" required />
    )
    <label for="phone_number_three"></label>
    <input id="phone_number_three" type="text" pattern="[0-9]{3}" name="phone_number_three" required />
    -
    <label for="phone_number_four"></label>
    <input id="phone_number_four" type="text" pattern="[0-9]{4}" name="phone_number_four" required />
    </br>
    </br>
    
    <?php echo WORDING_REGISTRATION_DOB; ?>
    </br>
    <label for="dob_month"></label>
    <select class="form-control" name="dob_month" required>
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

              
    <label for="dob_day"></label>          
    <select class="form-control" name="dob_day" required>
        <option value="">--Day--</option>
<?php
for($x = 1; $x < 32; $x++)
{
	echo "              <option value=\"$x\">$x</option>\n";
}
?>
    </select>
              
    <label for="dob_year"></label>          
    <select class="form-control" name="dob_year" required>
        <option value="">--Year--</option>
<?php
$x = date("Y");
$y = $x - 100;
for($x; $x > $y; $x--)
{
	echo "              <option value=\"$x\">$x</option>\n";
}
?>
	</select>
	</br>
	</br>
	
    <label for="level_id"><?php echo WORDING_REGISTRATION_LEVEL_NAME; ?></label>
    <select class="form-control" name="level_id" required>
    <option value="">--Select--</option> 
    <?php

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$query_level = "SELECT * from level_table";
$stmt_level = $con->prepare($query_level);
$stmt_level->bind_result($level_id, $level_name);
$stmt_level->execute();
while($stmt_level->fetch())
{
	echo "<option value=\"$level_id\">$level_name</option>\n";
}
?>

     </select> 
     
     <label for="school_id"><?php echo WORDING_REGISTRATION_SCHOOL_NAME; ?></label>
    <select class="form-control" name="school_id" required>
    <option value="">--Select--</option> 
    <?php
$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$query_school = "SELECT * from school_table";
$stmt_school = $con->prepare($query_school);
$stmt_school->bind_result($school_id, $school_name);
$stmt_school->execute();
while($stmt_school->fetch())
{
	echo "<option value=\"$school_id\">$school_name</option>\n";
}
?>

     </select> 
    </br>
    </br>
    <img src="phploginadvanced/tools/showCaptcha.php" alt="captcha" />
    </br>

    <label><?php echo WORDING_REGISTRATION_CAPTCHA; ?></label>
    <input type="text" name="captcha" required />
    </br>
    </br>

    <input type="submit" name="register" value="<?php echo WORDING_REGISTER; ?>" />
</form>
<?php } ?>

    <a href="index.php"><?php echo WORDING_BACK_TO_LOGIN; ?></a>

<?php include('includes/footer.php'); ?>
