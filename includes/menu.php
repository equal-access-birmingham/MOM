    <!-- Core Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Social Icons -->
    <link href="bootstrap/socialIcons/bootstrap-social.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="bootstrap/css/custom.css" rel="stylesheet">
    <link href="bootstrap/socialIcons/assets/css/font-awesome.css" rel="stylesheet">
    
    <!-- Core jQuery -->
    <script src="bootstrap/js/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
        
    <!-- Favicon -->
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/ico" />
  </head>
  
  <body>
  
    <!-- Navigation Bar -->
    <nav class="navbar navbar-fixed-top navbar-eab" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
          <!-- New: Note that the height is decreased by one pixel to allow appropriate navbar when using small window -->
          <a class="navbar-brand" href="/"><img class="nav-logo" style="height: 34px;" src="/images/EABLogoInverse.png" /></a>
        </div>
        
        <!-- Menu items -->
        <div class="collapse navbar-collapse navbar-right">
          <ul class="nav navbar-nav">

            <li><a href="/about.php">About</a></li>

            <!-- This might be a better way to display the events -->
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Patients <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="/coming_soon.php">Clinic Information</a></li>
                <li><a href="/coming_soon.php">Screenings</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Volunteers <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="/coming_soon.php">Information</a></li>

                <!-- Doodle Sign-up -->
                <li><a href="http://doodle.com/rump7ea6f53cnexn" target="_blank">EAB H&amp;P Sign-Up</a></li>
                <li><a href="http://doodle.com/e2zmaezzt3vnv45q" target="_blank">EAB Dispensary Sign-Up</a></li>
                <li><a href="http://doodle.com/t8q4fgc89m2gchtv" target="_blank">M-Power H&amp;P Sign-Up</a></li>
              </ul>
            </li>

<?php
// Activates "Admin" dropdown on site if the logged in user is an administrator
if($permissions->isUserAdmin() == true)
{
	echo "
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Admin <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">
                <li><a href=\"/account/register.php\">" . WORDING_REGISTER_NEW_ACCOUNT . "</a></li>
                <li><a href=\"/account/admin_management.php\">Account Management</a></li>
              </ul>
            </li>";
}

// Alternates between "Sign In" button and "Profile" dropdown based on login status
if($login->isUserLoggedIn() == false)
{
	echo "            <li><a href=\"#\" onclick=\"$('#volunteerModal').modal('toggle');\">Sign In</a></li>\n";
}
else
{
	echo "
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Profile <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">\n";
/*
	if($login->isUserVerified() == false)
	{
		// if user has not verified their account, show verification page
		echo "                <li><a href=\"/account/verify.php\">Verify Account</a></li>";
	}
	else
	{
		// if user has verified account, show profile edit page
		echo "                <li><a href=\"/account/edit_profile.php\">Edit Profile</a></li>";
	}
*/
	echo "
                <li><a href=\"/index.php?logout\">Logout</a></li>
              </ul>
            </li>\n";
}
?>
          </ul>
          
          <!-- Donate Button -->
<?php
// Turned off if the user is an admin
if($permissions->isUserAdmin() == false)
{
	echo  "         <a class=\"btn btn-warning navbar-btn donate\" href=\"https://www.uab.edu/give/now/index.php?option=com_rsform&formId=4&fundid=809|Equal%20Access%20Birmingham\" target=\"_blank\">DONATE</a>";

}
?>
        </div>
      </div>
    </nav>

<?php
// Calls the incorrect modal if someone attempts to login but fails
if(isset($login))
{
	if(isset($_POST['login']) && $login->isUserLoggedIn() == false)
	{
		echo "
    <script>
      $(document).ready(function() {
        $('#incorrectModal').modal('toggle');
      });
    </script>
	";
	}
}

// Prevents error from triggering on admin page as those messages are reserved for a confirmation modal
if(isset($login) || isset($registration) || isset($permissions) && $_SERVER['SCRIPT_NAME'] != "/account/admin_management.php")
{
	if($login->errors || $login->messages || $registration->errors || $registration->messages || $permissions->errors || $permissions->messages)
	{
		// Prevents modal for login errors because this is controlled more closely by an incorrect modal login
		if(!in_array(MESSAGE_LOGIN_FAILED, $login->errors) && !in_array(MESSAGE_PASSWORD_WRONG, $login->errors))
		{
			echo "
    <script>
      $(document).ready(function() {
        $('#error_message_modal').modal('toggle');
      });
    </script>
		";
		}
	}
}
?>

    <!-- Sign-In Modal -->
    <div class="modal fade" id="volunteerModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
            <h2 class="modal-body-header">Login</h2>
            
            <!-- Form using PHP-Login -->
              <!-- Action is the current loction on the server -->
              <!-- The login object is on each page so this works fine -->
              <!-- This CANNOT be blank as the "?logout" get variable will be reset upon submission, otherwise the login will close the session-->
            <form role="form" method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
              <div class="form-group">
                <input id="user_name" class="form-control login_input" type="text" name="user_name" placeholder="User Name" required />
              </div>
              <div class="form-group">
                <input id="user_password" class="form-control login_input" type="password" name="user_password" placeholder="Password" autocomplete="off" required />
              </div>
              <div class="checkbox">
                <label for="user_rememberme">
                  <input type="checkbox" id="user_rememberme" name="user_rememberme" value="1" /> <?php echo WORDING_REMEMBER_ME . "\n"; ?>
                </label>
              </div>
              <div class="form-group">
                <input class="btn btn-eab" type="submit" name="login" value="Log In" />
              </div>
            </form>

            <a href="account/password_reset.php">I forgot my password</a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Incorrect Sign-In Modal -->
    <div class="modal fade" id="incorrectModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
            <h4 class="modal-body-header"><strong>Incorrect User Name or Password</strong></h4>
            <p>
              If you would like to volunteer, please contact 
              <a href="mailto:equalaccess@uab.edu">equalaccess@uab.edu</a> 
              for an account.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-eab" onclick="$('#volunteerModal').modal('toggle'); $('#incorrectModal').modal('toggle');">Back</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Error or Message Modal for objects -->
    <div class="modal fade" id="error_message_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
            <h4 class="moal-body-header"><strong>Response</strong></h4>
            <ul>
<?php
if (isset($login))
{
	if ($login->errors)
	{
		foreach ($login->errors as $error)
		{
			echo "          <li>Login Error: $error</li>";
		}
	}
	if ($login->messages) {
		foreach ($login->messages as $message)
		{
			echo "          <li>Login Message: $message</li>";
		}
	}
}

// show potential errors / feedback (from Permissions object)
if (isset($permissions))
{
	if ($permissions->errors)
	{
		foreach ($permissions->errors as $error)
		{
			echo "          <li>Permission Error: $error</li>";
		}
	}
	if ($permissions->messages)
	{
		foreach ($permissions->messages as $message)
		{
			echo "          <li>Permission Message: $message</li>";
		}
	}
}

// show potential errors / feedback (from registration object)
if (isset($registration))
{
	if ($registration->errors)
	{
		foreach ($registration->errors as $error)
		{
			echo "          <li>Registration Error: $error</li>";
		}
	}
	if ($registration->messages)
	{
		foreach ($registration->messages as $message)
		{
			echo "          <li>Registration Message: $message</li>";
		}
	}
}
?>
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-eab" data-dismiss="modal">Ok</button>
          </div>
        </div><!-- Modal Content -->
      </div>
    </div>