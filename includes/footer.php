  </body>
  <footer>
<?php
// unsets $_SESSION['messages'] to prevent it from continually filling up
if(isset($_SESSION['messages']))
{
	// Counting page refreshes and unsets session messages on second refresh
	// Page executes without loading on first refresh which will unset variables without displaying content
	$_SESSION['count_refresh'] += 1;
	if($_SESSION['count_refresh'] == 2)
	{
		unset($_SESSION['messages']);
		unset($_SESSION['count_refesh']);
	}
}
?>
    <script>
      // Schedule login -- allows login straight to schedule when not logged in
      $(document).ready(function() {
        $('#schedule_signin').click(function() {
          $('#signin_form').attr("action", "/mom/");
        });
      });

      /* Scroll Button Script */
      $(document).ready(function() { 
        $('.scroll-background').hover(function() {
          $('#scroll-text').slideToggle('slow');
        });
      });

      /* Smooth Scroll */
      $('a[href=#body-section]').click(function(){
        if(location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname)
        {
          var target = $(this.hash);
          target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

          if(target.length)
          {
            $('html,body').animate({
              scrollTop: target.offset().top
            }, 1000);
            
            return false;
          }
        }
      });

      /** 
       * Keeps the footer at the bottom of the page, no matter how little content is on the page
       * adds or removes footer-drop class (in css) based on whether footer is too high
       ** footer-drop class:  position: absolute; bottom: 0; width: 100%;
       */
      function footerDrop()
      {
        $(document).ready(function() {
          $body = $('body');
          $footer = $('footer');
  
          // removes the footer-drop clas
          var removeClass = function() {
            $footer.removeClass(function(index, currentClass) {
              if(currentClass === "footer-drop")
              {
                var removedClass = "footer-drop";
              }
              return removedClass;
            });
          }

          // This is triggered at the beginning to ensure that the process starts over (otherwise the measurements aren't accurate)
          removeClass();

          var window_height = $(window).height();
          var footer_bottom = $footer.offset().top + $footer.height();

          // If the footer isn't at the bottom, then add the class to force it to the bottom
          if(footer_bottom < window_height)
          {
            $footer.addClass(function(index, currentClass) {
              if(currentClass !== "footer-drop")
              {
                var addedClass = "footer-drop";
              }
              return addedClass;
            });
          }

          // Measure to make sure the footer and body don't overlap
          var footer_top = $footer.offset().top;
          var body_bottom = $body.offset().top + $body.height();

          // if the footer and body overlap, then rip the class back off
          if(footer_top > body_bottom)
          {
            removeClass();
          }
        });
      }

      // Adjusts the footer everytime a page reloads or resizes
      $(window).on("resize load", footerDrop);
    </script>
    
    <!-- Footer Information Content -->
    <section class="footer-section">
      <div class="container">
        <hr />
        <div class="row">
          <div class="col-sm-4 col-xs-6">
            <address>
              <a href="https://maps.google.com?saddr=Current+Location&daddr=112+14th+Street+North+Birmingham+Alabama+35203" target="_blank">
                Church of the Reconciler <br />
                112 14th St N, <br />
                Birmingham, AL 35203
              </a>
            </address>
            <p><a href="tel:2052598836">(205) 259-8836</a></p>
            <p><a href="mailto:equalaccess@uab.edu">equalaccess@uab.edu</a></p>
          </div>
          
          <!-- Quick Links Section -->
          <div class="col-sm-4 col-xs-6">
            <p style="font-weight:bold; font-size:20px;">Quicklinks</p>
<?php
// Location of the mom app
$directory = substr($_SERVER['SCRIPT_NAME'], 0, 5);

// Do not display volunteer link in footer if the user is in the application or is already logged in
if($directory != "/mom/" && $login->isUserLoggedIn() == false)
{
	echo "            <p><a href=\"#\" onclick=\"$('#volunteerModal').modal('toggle');\">Volunteer Sign Up</a></p>";
}
?>
            <p><a href="/coming_soon.php">Screenings</a></p>
            <p><a href="http://churchofthereconciler.com/" target="_blank">Church of the Reconciler</a></p>
            <p><a href="http://www.mpowerministries.org/" target="_blank">M-Power</a></p>
          </div>
          
          <!-- EAB Video -->
          <div class="col-sm-4">
            <iframe src="http://player.vimeo.com/video/37989682?title=0&byline=0&portrait=0&color=1e6b52" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Social Icons Section -->
    <section class="icons-section">
      <div class="container">
        <div class="row icons pull-right">
          <a href="https://www.facebook.com/equalaccessbirmingham" class="btn btn-social-icon btn-facebook" target="_blank">
            <i class="fa fa-facebook"></i>
          </a>
          <a href="https://twitter.com/equalaccessbham" class="btn btn-social-icon btn-twitter" target="_blank">
            <i class="fa fa-twitter"></i>
          </a>
          <a href="http://equalaccessblog.wordpress.com/" class="btn btn-social-icon btn-linkedin" target="_blank">
            <i class="fa fa-wordpress"></i>
          </a>
        </div>
      </div>
    </section>
  </footer>
</html>