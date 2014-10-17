<?php
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are logged in" view.
    
 echo "<a href=\"view.php\">" . WORDING_DATA_VIEW . "</a>";
 echo "<a href=\"entry_test3.php\">" . WORDING_EVENT_REGISTRATION . "</a>";

} else {
    // the user is not logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are not logged in" view.
    include("views/not_logged_in.php");
}


 
?>
