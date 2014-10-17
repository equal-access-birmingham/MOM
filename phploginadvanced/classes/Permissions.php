<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * Checks and modifies the permissions of users
 * This object uses the $_SESSION variables from the Login object (it is dependent on the Login object to work)
 * @author tikenn
 * @license http://opensource.org/licenses/MIT MIT License
 */

class Permissions
{
	/**
	 * @var object $db_connection The database connection
	 */
	private $db_connection = null;
	/**
	 * @var boolean $user_is_admin The user's admin status
	 */
	private $user_is_admin = false;
	/**
	 * @var boolean $confirm_action_prompt Checks whether user has tried to delete user(s)
	 */
	public $confirm_action_prompt = false;
	/**
	 * @var array $errors Collection of error messages
	 */
	public $errors = array();
	/**
	 * @var array $messages Collection of success / neutral messages
	 */
	public $messages = array();
	
	/**
	 * the function "__construct()" automatically starts whenever an object of this class is created,
	 * you know, when you do "$login = new Login();"
	 */
	public function __construct()
	{
		// This statement only allows this object to work if a user is logged in
		if(!empty($_SESSION['user_name']) && ($_SESSION['user_logged_in'] == 1))
		{
			// Automatically checks admin status of user on pages supplied with the Permissions object
			$this->checkAdminStatus($_SESSION['user_name']);
			
			// Checks to see if admin has chosen to update users
			if(isset($_POST['update']))
			{
				// Modifies admin status (can't remove all of them)
				if(isset($_POST['admin']))
				{
					$this->modifyAdminStatus($_POST['admin']);
				}
				else
				{
					$this->messages[] = "Cannot remove all admins!";
				}
				
				// Resets accounts
				// Receives 'reset_account' array from html
				if(isset($_POST['reset_account']))
				{
					$this->resetAccountConfirm($_POST['reset_account']);
				}
				
				// Starts deletion process
				if(isset($_POST['delete_account']))
				{
					// Stores delete_array in Session variable to keep it between page refreshes
					$this->deleteAccountConfirm($_POST['delete_account']);
				}
			}
			
			// Shows confirmation messages if they have been triggered (delete, reset, permission changes)
			// This is only triggered with a confirmation
			// Outside 'update' POST as page will refresh due to use of PHP
			elseif(isset($_POST['confirm_action']))
			{
				if(isset($_SESSION['delete_users_array']))
				{
					foreach($_SESSION['delete_users_array'] as $delete_id)
					{
						$this->deleteAccount($delete_id);
					}
				}
				
				if(isset($_SESSION['reset_user_accounts_array']))
				{
					foreach($_SESSION['reset_user_accounts_array'] as $reset_id)
					{
						$this->resetAccount($reset_id);
					}
				}
			}
		}
	}
	
	/**
	 * Checks if database connection is opened. If not, then this method tries to open it.
	 * @return bool Success status of the database connecting process
	 */
	private function databaseConnection()
	{
		// if connection already exists
		if ($this->db_connection != null) {
			return true;
		} else {
			try {
				// Generate a database connection, using the PDO connector
				// @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
				// Also important: We include the charset, as leaving it out seems to be a security issue:
				// @see http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers#Connecting_to_MySQL says:
				// "Adding the charset to the DSN is very important for security reasons,
				// most examples you'll see around leave it out. MAKE SURE TO INCLUDE THE CHARSET!"
				$this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
				return true;
			} catch (PDOException $e) {
				$this->errors[] = MESSAGE_DATABASE_ERROR . $e->getMessage();
			}
		}
		// default return
		return false;
	}
	
	/**
	 * Search into database for the user data of user_name specified as parameter
	 * @return user data as an object if existing user
	 * @return false if user_name is not found in the database
	 * TODO: @devplanete This returns two different types. Maybe this is valid, but it feels bad. We should rework this.
	 * TODO: @devplanete After some resarch I'm VERY sure that this is not good coding style! Please fix this.
	 */
	private function getUserData($user_name)
	{
		// if database connection opened
		if($this->databaseConnection()) {
			// database query, getting all the info of the selected user
			$query_user = $this->db_connection->prepare('SELECT * FROM users WHERE user_name = :user_name');
			$query_user->bindValue(':user_name', $user_name, PDO::PARAM_STR);
			$query_user->execute();
			// get result row (as an object)
			return $query_user->fetchObject();
		} else {
			return false;
		}
	}

	/**
	 * Searches database for user data by user ID
	 * @return user data as an object if user exists
	 * @return false if user ID does not exist
	 */
	private function getUserDataById($user_id)
	{
		if($this->databaseConnection())
		{
			// Database query selecting all user info using chosen user_id
			$query = "SELECT * FROM `users` WHERE `user_id` = :user_id;";
			$query_user_id = $this->db_connection->prepare($query);
			$query_user_id->bindValue(":user_id", $user_id, PDO::PARAM_STR);
			$query_user_id->execute();
			// get result row (as an object)
			return $query_user_id->fetchObject();
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * checks whether the user is admin and set $this->user_is_admin = true if user is admin --> __construct()
	 */
	private function checkAdminStatus($user_name)
	{
		// Uses the getUserData() function from the Login object to retrieve user data via user name
		// specifically, acquires the admin status of the user
		$result_row = $this->getUserData(trim($user_name));
		$admin_status = $result_row->admin;
		
		// Sets the user admin variable to true if the user is an admin
		if($admin_status == 1)
		{
			$this->user_is_admin = true;
		}
	}
	
	/**
	 * Returns the admin status of the user
	 */
	public function isUserAdmin()
	{
		return $this->user_is_admin;
	}
	
	/**
	 * Places a prompt into $this->messages for confirmation of admin's choice to add/remove admin status to/from an account
	 */
	public function modifyAdminStatusConfirm($admin_confirm_array)
	{
		
	}

	/**
	 * Adds/removes admin status to user upon selection
	 * This function depends on the admins having their checkboxes automatically selected upon loading the user table
	 */
	private function modifyAdminStatus($admin_array)
	{
		
		if($this->databaseConnection())
		{
			/**
			 * This part removes administrative status
			 */
			 
			// Selects all current admins before update submission
			$query = "SELECT * FROM `users` WHERE `admin` = 1;";
			$query_current_admins = $this->db_connection->prepare($query);
			$query_current_admins->execute();
			
			while($admin_data = $query_current_admins->fetchObject())
			{
				// Compares the current admins with the array of user submitted for admin status update
				if(!in_array($admin_data->user_id, $admin_array))
				{
					// If current admin is not in submitted list, then they have been removed from admin status
					// Note that this is heavily dependent on automatic selection of all admins in the displayed user table
					$query = "UPDATE `users` SET `admin` = 0 WHERE `user_id` = :user_id;";
					$query_remove_admin = $this->db_connection->prepare($query);
					$query_remove_admin->bindValue(":user_id", $admin_data->user_id, PDO::PARAM_STR);
					$query_remove_admin->execute();
					
					$this->messages[] = $admin_data->user_name  . WORDING_ADMIN_REMOVAL;
				}
			}
			
			/**
			 * This part adds administrative status
			 */
			
			// Goes through the submitted admin list from admin table
			foreach($admin_array as $user_id)
			{
				$result_row = $this->getUserDataById($user_id);
				
				// If the user is not already an admin ...
				if($result_row->admin == 0)
				{
					// Make the user an admin
					$query = "UPDATE `users` SET `admin` = 1 WHERE `user_id` = :user_id;";
					$query_admin = $this->db_connection->prepare($query);
					$query_admin->bindValue(':user_id', $user_id, PDO::PARAM_STR);
					$query_admin->execute();
					
					$this->messages[] = $result_row->user_name . WORDING_ADMIN_ADDITION;
				}
			}
		}
		else
		{
			$this->errors[] = MESSAGE_DATABASE_ERROR;
		}
	}
	
	/**
	 * Places a prompt into $this->messages for confirmation of admin's choice to reset the accounts
	 */
	public function resetAccountConfirm($user_array)
	{
		for($i = 0; $i < count($user_array); $i++)
		{
			$result_row = $this->getUserDataById($user_array[$i]);

			// Prevents admin from resetting their own account
			if($result_row->user_name == $_SESSION['user_name'])
			{
				$this->messages[] = MESSAGE_RESET_PERSONAL_ACCOUNT_ERROR;
				array_slice($user_array, $i, 1, true);
			}
			else
			{
				$this->messages[] = MESSAGE_RESET_ACCOUNT_CONFIRM . $result_row->user_name;
			}
		}
		
		// variable holds information that a reset_account (action) has been selected (for display of confirm button in html perhaps)
		$this->confirm_action_prompt = true;
		
		// Gives back the array of selected users for resetting account in a $_SESSION variable
		$_SESSION['reset_user_accounts_array'] = $user_array;
	}
	
	/**
	 * Resets a user's account and password upon selection (used when user allows temp password to expire)
	 */
	public function resetAccount($user_id)
	{
		// Need to get user data for the email
		$result_row = $this->getUserDataById($user_id);
		// Creates a new random 10 character password for the user
		$user_password = $this->createRandomPassword();
		
		// check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
		// if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
		$hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

		// crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
		// the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
		// compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
		// want the parameter: as an array with, currently only used with 'cost' => XX.
		$user_password_hash = password_hash($user_password, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
		
		$query = "UPDATE `users` SET `user_password_hash` = :user_password_hash, `user_registration_datetime` = now(), `user_password_change` = 0 WHERE `user_id` = :user_id;";
		$query_reset_account_update = $this->db_connection->prepare($query);
		$query_reset_account_update->bindValue(":user_password_hash", $user_password_hash, PDO::PARAM_STR);
		$query_reset_account_update->bindValue(":user_id", $user_id, PDO::PARAM_STR);
		$query_reset_account_update->execute();
		
		// unsets the variable so that it cannot be re-triggered through refreshing
		unset($_SESSION['reset_user_accounts_array']);
		
		if($query_reset_account_update)
		{
			// Send an email with the user name and password for the account
			if($this->sendResetAccountEmail($result_row->user_name, $result_row->user_email, $user_password))
			{
				// Mail sent successfully
				$this->messages[] = MESSAGE_RESET_ACCOUNT_MAIL_SENT;
			}
			else
			{
				// Error message for inability to send email
				$this->errors[] = MESSAGE_RESET_ACCOUNT_MAIL_ERROR;
			}
		}
		else
		{
			$this->errors[] = MESSAGE_RESET_ACCOUNT_FAILED;
		}
	}
	
	/**
	 * Creates a cryptographically random password
	 */
	private function createRandomPassword()
	{
		// Creates a cryptographically random set of bytes for a random password
		$crypt_strong = false;
		while(!$crypt_strong)
		{
			$random_password_bytes = openssl_random_pseudo_bytes(5, $crypt_strong);
		}
		
		// Converts those bytes to hexadecimals (letter & numbers) for the actual password
		$random_password_str = bin2hex($random_password_bytes);
		
		
		return $random_password_str;
	}
	
	/**
	 * Creates an email for an account reset
	 */
	public function sendResetAccountEmail($user_name, $user_email, $user_password)
	{
		$mail = new PHPMailer;
		
		// please look into the config/config.php for much more info on how to use this!
		// use SMTP or use mail()
		if(EMAIL_USE_SMTP) {
			// Set mailer to use SMTP
			$mail->IsSMTP();
			//useful for debugging, shows full SMTP errors
			//$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
			// Enable SMTP authentication
			$mail->SMTPAuth = EMAIL_SMTP_AUTH;
			// Enable encryption, usually SSL/TLS
			if(defined(EMAIL_SMTP_ENCRYPTION)) {
				$mail->SMPTSecure = EMAIL_SMTP_ENCRYPTION;
			}
			// Specify host server
			$mail->Host = EMAIL_SMTP_HOST;
			$mail->Username = EMAIL_SMTP_USERNAME;
			$mail->Password = EMAIL_SMTP_PASSWORD;
			$mail->Port = EMAIL_SMTP_PORT;
		} else {
			$mail->IsMail();
		}
		
		$mail->From = EMAIL_RESET_ACCOUNT_FROM;
		$mail->FromName = EMAIL_RESET_ACCOUNT_FROM_NAME;
		$mail->AddAddress($user_email);
		$mail->Subject = EMAIL_RESET_ACCOUNT_SUBJECT;
		
		// Body of the email
		$mail->Body = EMAIL_RESET_ACCOUNT_BODY . "\n
			\tUser Name: $user_name
			\tPassword: $user_password";
		
		if(!$mail->Send()) {
			$this->errors[] = MESSAGE_RESET_ACCOUNT_MAIL_NOT_SENT . $mail->ErrorInfo;
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * The sole purpose of this function is to create a prompt for deletion in case of accidental selection
	 * The function simply accepts the array of selected users for deletion and adds them to the $this->messages array
	 * Changes the $this->confirm_action_prompt to true for use in html and returns entire array for use in deleteAccount function
	 */
	public function deleteAccountConfirm($user_array)
	{
		// Creates a warning message for each user selected for deletion
		for($i = 0; $i < count($user_array); $i++)
		{
			$result_row = $this->getUserDataById($user_array[$i]);

			// Prevents an admin from deleting themselves
			if($result_row->user_name == $_SESSION['user_name'])
			{
				$this->errors[] = MESSAGE_DELETE_PERSONAL_ACCOUNT_ERROR;
				array_slice($user_array, $i, 1, true);
			}
			else
			{
				$this->messages[] = MESSAGE_DELETE_USER_CONFIRM . $result_row->user_name;
			}
		}
		
		// variable holds information that a deletion (action) has been selected (for display of confirm button in html perhaps)
		$this->confirm_action_prompt = true;
		
		// Gives back the array of selected users for deletion in a $_SESSION variable
		$_SESSION['delete_users_array'] = $user_array;
	}
	
	/**
	 * Permanently deletes an account
	 */
	private function deleteAccount($user_id)
	{
		$query = "DELETE FROM `users` WHERE `user_id` = :user_id;";
		$query_delete_account = $this->db_connection->prepare($query);
		$query_delete_account->bindValue(":user_id", $user_id, PDO::PARAM_STR);
		$query_delete_account->execute();
		
		// unsets the variable so that it cannot be re-triggered through refreshing
		unset($_SESSION['delete_users_array']);
	}
}