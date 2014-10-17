<?php include('header.php'); ?>

<?php
if ($login->isUserLoggedIn() == true) 
{ 
	echo "
		<a href=\"view.php\">" . WORDING_DATA_VIEW . "</a>
		<a href=\"entry_test3.php\">" . WORDING_EVENT_REGISTRATION . "</a>
		<a href=\"index.php?logout\">" . WORDING_LOGOUT . "</a>
    ";
} 
else {
    // the user is not logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are not logged in" view.
	echo "    
    <form method=\"post\" action=\"index.php\" name=\"loginform\">
    <label for=\"user_name\">" .  WORDING_USERNAME . "</label>
    <input id=\"user_name\" type=\"text\" name=\"user_name\" required />
    <label for=\"user_password\">" .  WORDING_PASSWORD . "</label>
    <input id=\"user_password\" type=\"password\" name=\"user_password\" autocomplete=\"off\" required />
    <input type=\"checkbox\" id=\"user_rememberme\" name=\"user_rememberme\" value=\"1\" />
    <label for=\"user_rememberme\">" .  WORDING_REMEMBER_ME . "</label>
    <input type=\"submit\" name=\"login\" value=\"" .  WORDING_LOGIN . "\" />
</form>";

	echo "
		<a href=\"register.php\">" .  WORDING_REGISTER_NEW_ACCOUNT . "</a>
		<a href=\"password_reset.php\">" .  WORDING_FORGOT_MY_PASSWORD . "</a>
    ";
}
?>

<?php include('footer.php'); ?>