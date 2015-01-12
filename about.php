<?php require_once("includes/header.php"); ?>

    <title>About | Equal Access Birmingham</title>

<?php require_once("includes/menu.php"); ?>

    <!-- Main image that covers full screen -->
    <div class="hero-img about-img">
      
      <!-- Main Image Caption -->
      <div class="hero-img-caption">
        <div class="container">
          <h1><strong>About EAB</strong></h1>
          <p class="lead">
           Built and run by students with help from many others. 
           Scroll down to learn more about us.
          </p>
        </div>
      </div>
    </div>
    
    <!-- Scroll Button: should go with every page -->
    <div id="scroll" class="scroll text-center hidden-xs" >
      <a href="#body-section">
        <div class="scroll-background text-center">
          <div id="scroll-text" class="scroll-text">
            <p>Scroll</p>
          </div>
          <span class="glyphicon glyphicon-chevron-down"></span>
        </div>
      </a>
    </div>
    
    <!-- Body Content -->
    
    <!-- Dummy div used to counteract the fixed nav -->
    <div id="body-section" class="scroll-to"></div>
    
      <!-- Sponsors -->
      <div class="container">
        <!-- Photography Sponsors -->
        <div class="row">
          <div class="col-sm-4">
            <div class="vcenter vcenter-ht-500 text-center">
              <img src="/images/about/dishu.png" alt="Devanshu Kaushik" class="img-rounded vcenter-img-md" /></p>
            </div>
          </div>
          <div class="col-sm-8">
            <div class="vcenter vcenter-ht-500">
              <h1>Devanshu Kaushik Photography</h1>
              <p class="lead">
                One of our local medical students responsible 
                for the images on this website.  His website is located 
                at <a href="https://devanshu-kaushik.squarespace.com/" target="_blank">devanshu-kaushik.squarespace.com</a>.
              </p>
            </div>
          </div>
        </div>
        
        <!-- Website Design Table -->
        <div class="row">
          <div class="col-xs-12">
            <h2>Website Design</h2>
            <table class="table table-striped">
              <tr>
                <th>Software</th>
                <th>Description</th>
              </tr>
              <tr>
                <td><a href="http://www.ubuntu.com/server" target="_blank">Ubuntu Server</a></td>
                <td>One of the most popular servers to date, creating the basis for many common websites</td>
              </tr>
              <tr>
                <td><a href="http://nginx.org/en/" target="_blank">NGiNX</a></td>
                <td>A multi-threaded web server for faster web sites that is becoming more common</td>
              </tr>
              <tr>
                <td><a href="http://mariadb.org" target="_blank">MariaDB</a></td>
                <td>A fork of Oracle's MySQL database that easily integrates with PHP</td>
              </tr>
              <tr>
                <td><a href="http://php.net/" target="_blank">PHP-FPM</a></td>
                <td>One of the more popular server-side languages of the web 2.0 era</td>
              </tr>
            </table>
          </div>
        </div><hr />
      
        <!-- Contact Section -->
        <div class="row">
          <div class="col-xs-12">
            <h1 class="body-content-header">Contact</h1>
            <h3>General Contact</h3>
            <ul>
              <li><a href="mailto:equalaccess@uab.edu">equalaccess@uab.edu</a></li>
            </ul>
            <h3>Website Design and Development</h3>
            <ul>
              <li><a href="mailto:tikenn@uab.edu">Tim Kennell Jr.</a> - Design/Development</li>
              <li><a href="mailto:oramada@uab.edu">Omar Ramadan</a> - Design/Development</li>
              <li><a href="mailto:lnherrer@uab.edu">Nico Herrera</a> - Design/Development</li>
              <li><a href="mailto:jhsung@uab.edu">Jae Sung</a> - Design/Development</li>
              <li><a href="mailto:nshaywoo@uab.edu">Nathan Haywood</a> - Design/Development</li>
              <li><a href="mailto:acworth@uab.edu">Anna Worth</a> - Design</li>
            </ul>
            <h3>Informatics Advisor</h3>
            <ul>
              <li><a href="https://services.medicine.uab.edu/facultyDirectory/FacultyData.asp?FID=61255" target="_blank">Seung Park, MD</a></li>
            </ul>
          </div>
        </div>
      </div>
      
<?php require_once("includes/footer.php"); ?>