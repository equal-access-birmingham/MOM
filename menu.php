    <!-- Core Bootstrap -->
    <!-- links are used for CSS pages in the header to tell the page where the CSS rules are -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <!-- There is one minor rule that needs to be added to make everything work -->
    <link href="bootstrap/css/custom.css" rel="stylesheet">
    
    <!-- jQuery -->
    <!-- <script> is used to grab javascripts; note that jquery is javascript, just a special version -->
    <!-- jQuery is not a part of bootstrap, but is required to make bootstrap work.  Go to jquery.com to download it -->
    <script src="bootstrap/js/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </head>
  
  <body>
  
    <!-- The All-Powerful Navigation Bar -->
    <!-- This is placed in menu.php so that it can be easily included in all future files created -->
    
    <!-- navbar: defines the navbar -->
    <!-- navbar-inverse: makes the navbar black with white text -->
    <!-- navbar-fixed-top: prevents the navbar from scrolling with the rest of the page -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      
      <!-- container: creates padding (empty space) on the left and right sides of the page -->
      <!-- container-fluid: another option that creates a much smaller padding area -->
      <div class="container">
        
        <!-- Collapsed View -->
        <!-- This portion is what is viewed when the screen is small -->
        
        <div class="navbar-header">
          
          <!-- This button displays the menu when the screen is small -->
          
          <!-- data-target: identifies the menu to be displayed; can just target the collapse class as shown here -->
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".collapse">
            
            <!-- span: similar to a div but encompasses a smaller amount of content and is typically used inline -->
            <!-- sr-only: used for screen readers to help with navigation -->
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
          <!-- This is the name of the website on the left side that is always displayed -->
          <a class="navbar-brand" href="index.php">EAB Scheduling</a>
        </div>
        
        <!-- This is the menu -->
          <!-- Visible when the screen is large enough -->
          <!-- Put in dropdown menu when the screen is too small -->
        <div class="collapse navbar-collapse">
<?php
/* Only displaying options in the navigation bar if the user is logged in */
if($login->isUserLoggedIn() == true)
{
	echo "
          <!-- These are the menu items encapsulated in an unordered list that is modified by bootstrap -->
            <!-- List items are the actual navigation hyperlinks (they must be hyperlinks to display properly) -->
          <ul class=\"nav navbar-nav navbar-right\">
            <li><a href=\"index.php\">Home</a></li>
            <li><a href=\"view.php\">Schedule</a></li>
            <li><a href=\"entry_test3.php\">Register</a></li>
            <li><a href=\"index.php\">Profile</a></li>
            <li><a href=\"index.php?logout\">Logout</a></li>
          </ul>\n";
}
elseif($permission->isUserAdmin() == true)
	echo "
          <ul class=\"nav navbar-nav navbar-right\">
            <li><a href=\"index.php\">Home</a></li>
            <li><a href=\"view.php\">Schedule</a></li>
            <li><a href=\"entry_test3.php\">Register</a></li>
            <li><a href=\"index.php\">Profile</a></li>
            <li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Admin <span class=\"caret\"></span></a>
              <ul class=\"dropdown-menu\" role=\"menu\">
                <li><a href=\"admin.php\">Edit Users</a></li>
              </ul>
            <li><a href=\"index.php?logout\">Logout</a></li>
            </li>
          </ul>\n";
}

?>
        </div>
      </div>
    </nav>
    
    <!-- Body Content -->
    <!-- Trying to contain this gorgeous body -->
    <div class="container">
