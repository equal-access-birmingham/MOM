<?php
$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

    <title>Reset Password | Equal Access Birmingham</title>

<?php require_once($path_to_root . "includes/menu.php"); ?>

    <div class="container no-image">
<?php
// If user hasn't received password reset link
if($login->passwordResetLinkIsValid() == true)
{
	echo "
      <h2>New Password</h2>
      <form method=\"post\" action=\"/index.php\" name=\"new_password_form\">
        <input type=\"hidden\" name=\"user_name\" value=\"" . $_GET['user_name'] . "\" />
        <input type=\"hidden\" name=\"user_password_reset_hash\" value=\"" . $_GET['verification_code'] . "\" />
    
        <div class=\"form-group\">
          <label for=\"user_password_new\">" . WORDING_NEW_PASSWORD . "</label>
          <input id=\"user_password_new\" class=\"form-control\" type=\"password\" name=\"user_password_new\" pattern=\".{6,}\" required autocomplete=\"off\" />
        </div>
        <div class=\"form-group\">
          <label for=\"user_password_repeat\">" . WORDING_NEW_PASSWORD_REPEAT . "</label>
          <input id=\"user_password_repeat\" class=\"form-control\" type=\"password\" name=\"user_password_repeat\" pattern=\".{6,}\" required autocomplete=\"off\" />
        </div>
        <input class=\"btn btn-default\" type=\"submit\" name=\"submit_new_password\" value=\"" . WORDING_SUBMIT_NEW_PASSWORD . "\" />
      </form>\n";
}

// no data from a password-reset-mail has been provided, so we simply show the request-a-password-reset form
else
{
	echo "
      <h2>Password Reset</h2>
      <form method=\"post\" action=\"password_reset.php\" name=\"password_reset_form\" role=\"form\">
        <div class=\"form-group\">
          <label for=\"user_name\">" . WORDING_REQUEST_PASSWORD_RESET . "</label>
          <input id=\"user_name\" class=\"form-control\" type=\"text\" name=\"user_name\" required />
        </div>
        <input class=\"btn btn-default\" type=\"submit\" name=\"request_password_reset\" value=\"" . WORDING_RESET_PASSWORD . "\" />
      </form>\n";
}
?>

    </div>

<?php include($path_to_root . "includes/footer.php"); ?>
