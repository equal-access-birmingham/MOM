<?php require_once("includes/header.php"); ?>
    
    <title>Equal Access Birmingham</title>
    
<?php require_once("includes/menu.php"); ?>

    <!-- Carousel -->
    <div id="homeCarousel" class="carousel slide" data-interval="6000" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#homeCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#homeCarousel" data-slide-to="1"></li>
        <li data-target="#homeCarousel" data-slide-to="2"></li>
        <li data-target="#homeCarousel" data-slide-to="3"></li>
      </ol>
      
      <!-- Scroll Button -->
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
      <!-- Wrapper for slides -->
      <div class="carousel-inner">
      
        <!-- Image 1 -->
        <div class="item carousel-image eab-img active">
          <div class="carousel-caption">
            <div class="background-highlight">
              <div class="container text-left">
                <div class="row">
                  <div class="col-sm-7">
                    <a href="about.php"><h1>Who We Are</h1></a>
                    <p class="lead">
                      A student-run, free clinic meeting the healthcare needs 
                      of the Birmingham community
                    </p>
                  </div>
                  <div class="col-xs-5 carousel-btn">
                    <a class="btn btn-eab btn-lg hidden-xs" href="about.php">Learn More</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
       
        <!-- Image 2 -->
        <div class="item carousel-image race-home-img">
          <div class="carousel-caption">
            <div class="background-highlight">
              <div class="container text-left">
                <div class="row">
                  <div class="col-sm-7">
                    <a href="http://www.active.com/birmingham-al/running/distance-running-races/eab-heart-and-sole-5k-2015?int=" target="_blank"><h1>Heart + Sole 5K/Fun Run</h1></a>
                    <p class="lead">
                      Taking place on Saturday August 29, 2015 at UAB Campus Green at 8 AM!
                      Come join in and support EAB.
                    </p>
                  </div>
                  <div class="col-xs-5 carousel-btn">
                    <a class="btn btn-eab btn-lg hidden-xs" href="http://www.active.com/birmingham-al/running/distance-running-races/eab-heart-and-sole-5k-2015?int=" target="_blank">Sign Up</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Image 3 -->
        <div class="item carousel-image location-img">
          <div class="carousel-caption">
            <div class="background-highlight">
              <div class="container text-left">
                <div class="row">
                  <div class="col-sm-7">
                    <a href="coming_soon.php"><h1>Our Location</h1></a>
                    <p class="lead">
                      We are located in the heart of Birmingham to serve the 
                      community where the community is
                    </p>
                  </div>
                  <div class="col-xs-5 carousel-btn">
                    <a class="btn btn-eab btn-lg hidden-xs" href="coming_soon.php">Find Us</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Image 4 -->
        <div class="item carousel-image patient-img">
          <div class="carousel-caption">
            <div class="background-highlight">
              <div class="container text-left">
                <div class="row">
                  <div class="col-sm-7">
                    <a href="patient_services.php"><h1>For Patients</h1></a>
                    <p class="lead">
                      We are here to serve you!  Let us be your primary care
                      provider
                    </p>
                  </div>
                  <div class="col-xs-5 carousel-btn">
                    <a class="btn btn-eab btn-lg hidden-xs" href="patient_services.php">Learn More</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- End Carousel Images -->

      <!-- Controls -->
      <a class="left carousel-control" href="#homeCarousel" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
      </a>
      <a class="right carousel-control" href="#homeCarousel" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
      </a>
    </div><!-- End Carousel -->
  
    <!-- Content -->
    
    <!-- Dummy div used to counteract navbar (same height as navbar) -->
    <div id="body-section" class="scroll-to"></div>
    
    <!-- Body Section -->
    <section class="body-section-padding bg-green">
      <div class="container">
      
        <!-- Trifecta -->
        <h1 class="fancy-header text-center">Serving the community,<br />In the community</h1>
        <div class="row">
          <div class="col-sm-4">
            <div class="trifecta volunteer">
              <h3>Service Mission</h3>
              <a class="btn btn-eab" href="about.php">Learn More</a>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="trifecta health-education">
              <h3>Health Resources</h3>
              <a class="btn btn-eab" href="patient_services.php#health_resources">Start Now</a>
            </div>
          </div>
          <div class="col-sm-4">
            <div class="trifecta volunteer">
              <h3>Student Volunteer</h3>
              <a class="btn btn-eab" href="volunteer_information.php">Learn More</a>
            </div>
          </div>
        </div><!-- Trifecta Row -->
      </div><!-- Content Container -->
    </section><!-- Body Background -->

<?php require_once("includes/footer.php"); ?>