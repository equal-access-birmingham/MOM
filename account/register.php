<?php
$require_login = true;
$require_admin = true;

$path_to_root = str_repeat("../", substr_count($_SERVER['SCRIPT_NAME'], "/") - 1);
require_once($path_to_root . "includes/header.php");
?>

<?php
require_once($path_to_root . "php-login-admin/classes/Registration.php");
$registration = new Registration();
?>

    <title>Create Account | Equal Access Birmingham</title>

<?php require_once($path_to_root . "includes/menu.php"); ?>

    <div class="container no-image">
      <h2>Create Account</h2>
      <form method="post" action="register.php" name="registerform" role="form">
        <div class="form-group">
          <label for="user_email"><?php echo WORDING_REGISTRATION_EMAIL; ?></label>
          <input id="user_email" class="form-control" type="email" name="user_email" required />
        </div>
        <input class="btn btn-default" type="submit" name="register" value="<?php echo WORDING_REGISTER; ?>" />
      </form>
    </div>

<?php include($path_to_root . "includes/footer.php"); ?>
