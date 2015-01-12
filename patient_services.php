<?php require_once("includes/header.php"); ?>

    <title>Patient Services | Equal Access Birmingham</title>

<?php require_once("includes/menu.php"); ?>

    <!-- Main image that covers full screen -->
    <div class="hero-img patients-img">
      
       <!-- Main Image Caption -->
       <div class="hero-img-caption">
         <div class="container">
           <h1><strong>Patient Services</strong></h1>
           <p class="lead">
             We're here to serve you.  We have two separate clinics that provide a 
             number of free healthcare services.
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

    <!-- EAB Clinic -->
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <div class="vcenter vcenter-ht-500">
            <h1>EAB Clinic</h1>
            <div class="row">
              <div class="col-xs-6 col-sm-12">
                <!-- Location -->
                <p>
                  <strong>Location:</strong><br />
                  <a href="https://maps.google.com?saddr=Current+Location&daddr=112+14th+Street+North+Birmingham+Alabama+35203" target="_blank">
                    Church of the Reconciler<br />
                    112 14th St N, <br />
                    Birmingham, AL 35203
                  </a>
                </p>
    
                <!-- Hours -->
                <p>
                  <strong>Hours:</strong><br />
                  Sunday 1:00 p.m. - 5:30 p.m.
                </p>
              </div>
              <div class="col-xs-6 col-sm-12">
                <!-- Services -->
                <p>
                  <strong>Services:</strong><br />
                  Primary Care<br />
                  Pharmacy<br />
                  Health Education<br />
                  Physical Therapy<br />
                  Social Services<br />
                </p>

                <!-- Visits -->
                <p>
                  <strong>Visits:</strong><br />
                  By appointment (call <a href="tel:2052598836">(205) 259-8836</a>)<br />
                  Walk-ins accepted until 4:00 p.m.
                </p>
              </div>
            </div><!-- Inner Row close -->
          </div><!-- vcenter close -->
        </div><!-- column close -->

        <div class="col-sm-6">
          <div class="vcenter vcenter-ht-500 text-center">
            <img src="/images/patients/EABIcon.png" alt="EAB Logo" class="vcenter-img-sm" />
          </div>
        </div><!-- column close -->
      </div><!-- Outer row close -->
    </div><!-- Container close -->

    <!-- EAB Clinic picture division -->
    <div class="picture-div eab-location-img"></div>

    <!-- M-Power Clinic -->
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          <div class="vcenter vcenter-ht-500 vcenter-img-center">
            <img src="/images/patients/eab+m-power.png" alt="EAB + M-Power Logo" class="vcenter-img-lg" />
          </div>
        </div>

        <div class="col-sm-6">
          <div class="vcenter vcenter-ht-500">
            <h1>M-Power Clinic</h1>
            <div class="row">
              <div class="col-xs-6 col-sm-12">
                <!-- Location -->
                <p>
                  <strong>Location:</strong><br />
                  <a href="https://maps.google.com?saddr=Current+Location&daddr=4022+4th+Avenue+South+Birmingham+Alabama+35222" target="_blank">
                    M-Power Ministries<br />
                    4022 4th Avenue South<br />
                    Birmingham, Alabama 35222
                  </a>
                </p>
    
                <!-- Hours -->
                <p>
                  <strong>Hours:</strong><br />
                  Wednesday 4:00 p.m. - 7:30 p.m.
                </p>
              </div>
              <div class="col-xs-6 col-sm-12">
                <!-- Services -->
                <p>
                  <strong>Services:</strong><br />
                  Acute Care (only 3 visits per year)<br />
                  Pharmacy<br />
                  Health Education<br />
                  Social Services
                </p>

                <!-- Visits -->
                <p>
                  <strong>Visits:</strong><br />
                  Walk-ins only (Up to 8.  First come, first served)
                </p>
              </div>
            </div><!-- Inner Row close -->
          </div><!-- vcenter close -->
        </div><!-- column close -->
      </div><!-- Outer row close -->
    </div><!-- Container close -->

    <!-- M-Power Clinic picture division -->
    <div class="picture-div mpower-location-img"></div>

    <!-- Medication Availability (Inexpensive medication) -->
    <div id="health_resources" class="container">
      <div class="row">
        <div class="col-sm-6">
          <div class="vcenter vcenter-ht-500">
            <h1>Inexpensive Medications</h1>
            <p>For those medications we do not have on-site</p>

            <!-- Walmart -->
            <p>
              <strong>Walmart ($4 Prescriptions)</strong><br />
              <a href="http://www.walmart.com/cp/PI-4-Prescriptions/1078664" target="_blank">Medication List</a>
            </p>
            <p>
              <a href="https://maps.google.com?saddr=Current+Location&daddr=209+Lakeshore+Parkway+Homewood+Alabama+35209" target="_blank">
                209 Lakeshore Parkway<br />
                Homewood, AL 35209
              </a>
            </p>

          </div>
        </div>
        <div class="col-sm-6">
          <div class="vcenter vcenter-ht-500 text-center">
            <img src="/images/patients/meds_clipart.png" alt="Medication Clipart" class="vcenter-img-sm" />
          </div>
        </div>
      </div>
    </div>

<?php require_once("includes/footer.php"); ?>