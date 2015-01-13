    <!-- Core Bootstrap -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Social Icons -->
    <link href="/bootstrap/socialIcons/bootstrap-social.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="/bootstrap/css/custom.css" rel="stylesheet">
    <link href="/bootstrap/socialIcons/assets/css/font-awesome.css" rel="stylesheet">
    
    <!-- Core jQuery:  placed at the end for faster loading -->
    <script src="/bootstrap/js/jquery.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>

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
          
          <a class="navbar-brand" href="/"><img class="nav-logo" src="/images/EABLogoInverse.png" /></a>
        </div>
        
        <!-- Menu items -->
        <div class="collapse navbar-collapse navbar-right">
          <ul class="nav navbar-nav">
            <li><a href="/mom/view.php">My Schedule</a></li>
            <li><a href="/mom/event_schedules.php">Event Schedules</a></li>
            <li><a href="/mom/index.php">Sign Up</a></li>
            <!-- <li><a href="/coming_soon.php">Upcoming Events</a></li> -->


<?php
// Activates "Admin" dropdown on site if the logged in user is an administrator
if($permissions->isUserAdmin() == true)
{
	echo "
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Admin <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">
                <li><a href=\"/mom/admin_officer_scheduler.php\">Master Sign Up</a></li>
                <li><a href=\"/mom/master_schedule.php\">Master Schedule</a></li>
                <li><a href=\"/mom/admin_event_create.php\">Create Event(s)</a></li>
                <li><a href=\"/mom/admin_event_manage.php\">View/Delete Event(s)</a></li>
              </ul>
            </li>";
}
elseif($permissions->isUserAdmin() == false)
{
	      echo "
        <li><a href=\"#\" onclick=\"$('#questionsModal').modal('toggle');\">Questions</a></li>";
}

if($login->isUserLoggedIn() == true)
{
	echo "
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Profile <b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">\n
                <li><a href=\"/account/edit_profile.php\">Edit Profile</a></li>
                <li><a href=\"/index.php?logout\">Logout</a></li>
              </ul>
            </li>\n";
}
?>

          </ul>
        </div>
      </div>
    </nav>

    <!-- Modals!!!! -->

    <!-- Questions Modal -->
    <div class="modal fade" id="questionsModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
            <h4 class="modal-body-header"><strong>Questions?</strong></h4>
            <p>
              If you're not sure what a role does, visit our 
              <a href="/volunteer_information.php#role_descriptions">volunteer page</a> to learn 
              more about the roles.
            </p>
            <p>
              Have more specific questions?  Just contact EAB's volunteer coordinator 
              <a href="mailto:emjohnso@uab.edu>">Erika Johnson</a>.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-eab" data-dismiss="modal">Ok</button>
          </div>
        </div>
      </div>
    </div>
