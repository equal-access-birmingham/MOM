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
          <a class="navbar-brand" href="/"><img class="nav-logo" style="height: 34px;" src="images/EABLogoInverse.png" /></a>
        </div>
        
        <!-- Menu items -->
        <div class="collapse navbar-collapse navbar-right">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Volunteer <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="view.php">Schedule</a></li>
                <li><a href="http://eab.path.uab.edu/coming_soon.php">Register</a></li>
                <li><a href="http://eab.path.uab.edu/coming_soon.php">Upcoming Events</a></li>
              </ul>
            </li>
                <!-- Using bootstrap.js to call the modal (faster than PHP) -->
                  <!-- This is being modified for scheduling problems -->
                <!-- <li><a href="#" onclick="$('#volunteerModal').modal('toggle');">Sign Up</a></li> -->


<?php


// Activates "Admin" dropdown on site if the logged in user is an administrator
if($permission->isUserAdmin() == true)
{
	echo "
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Admin <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">
                <li><a href=\"admin_hq.php\">Admin Home</a></li>
                <li><a href=\"admin_event_create.php\">Create Event(s)</a></li>
                <li><a href=\"admin_event_manage.php\">View/Delete Event(s)</a></li>
                <li><a href=\"http://eab.path.uab.edu/coming_soon.php\">Register Users for Events</a></li>
              </ul>
            </li>";
}
elseif($permission->isUserAdmin() == false)
{
	      echo "  
	        <li>
	          <a href=\"error.php\">Questions?</a>
	        </li>";  
}

			
			
if($login->isUserLoggedIn() == false)
{
	echo "";
}
else
{
	echo "
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Profile <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">\n
				<li><a href=\"edit_profile.php\">Edit Profile</a></li>
                <li><a href=\"/index.php?logout\">Logout</a></li>
              </ul>
            </li>\n";
}
?>            
            

          </ul>
          
        </div>
      </div>
    </nav>
