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
				if(isset($_SESSION['admin_delete_list']) && isset($_SESSION['admin_add_list']))
				{
					$this->modifyAdminStatus($_SESSION['admin_delete_list'], $_SESSION['admin_add_list']);
				}

				if(isset($_SESSION['delete_users_array']))
				{
					foreach($_SESSION['delete_users_array'] as $delete_id)
					{
						$this->deleteAccount($delete_id);
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
		// Stores user_id's of users to have admin status deleted or added respectively
		
		$admin_delete_list = array();
		$admin_add_list = array();

		if($this->databaseConnection())
		{
			/**
			 * This portion creates the list of users to have admin status deleted
			 */
			 
			// Selects all admins in the database
			$query = "SELECT * FROM `users` WHERE `admin` = 1;";
			$query_current_admins = $this->db_connection->prepare($query);
			$query_current_admins->execute();

			while($admin_data = $query_current_admins->fetchObject())
			{
				// Compares the current admins with the array of user submitted for admin status update
				if(!in_array($admin_data->user_id, $admin_confirm_array))
				{
					// User is not allowed to remove their own admin status
					if($admin_data->user_name == $_SESSION['user_name'])
					{
						$this->errors[] = MESSAGE_ADMIN_STATUS_REMOVAL_ERROR;
					}
					else
					{
						// If current admin is not in submitted list, then they have been removed from admin status
						// Note that this is heavily dependent on automatic selection of all admins in the displayed user table
						$this->messages[] = MESSAGE_ADMIN_STATUS_REMOVED . $admin_data->user_name;
						$admin_delete_list[] = $admin_data->user_id;
					}
				}
			}

			/**
			 * This part adds administrative status
			 */
			
			// Goes through the submitted admin list from admin table
			foreach($admin_confirm_array as $user_id)
			{
				$result_row = $this->getUserDataById($user_id);
				
				// If the user is not already an admin ...
				if($result_row->admin == 0)
				{
					// Prompt to make the user an admin
					$this->messages[] = MESSAGE_ADMIN_STATUS_ADDED . $result_row->user_name;
					$admin_add_list[] = $result_row->user_id;
				}
			}

			// variable holds information that a reset_account (action) has been selected (for display of confirm button in html perhaps)
			$this->confirm_action_prompt = true;
		
			// Sets session variables to be used for actual action of deleting or adding admin status
			$_SESSION['admin_delete_list'] = $admin_delete_list;
			$_SESSION['admin_add_list'] = $admin_add_list;
		}
		else
		{
			$this->errors[] = MESSAGE_DATABASE_ERROR;
		}
	}

	/**
	 * Adds/removes admin status to user upon selection
	 * This function depends on the admins having their checkboxes automatically selected upon loading the user table
	 */
	private function modifyAdminStatus($admin_delete_list, $admin_add_list)
	{
		if($this->databaseConnection())
		{
			/**
			 * This part removes administrative status
			 */
			
			foreach($admin_delete_list as $deleted_admin)
			{
				$query = "UPDATE `users` SET `admin` = 0 WHERE `user_id` = :user_id;";
				$query_remove_admin = $this->db_connection->prepare($query);
				$query_remove_admin->bindValue(":user_id", $deleted_admin, PDO::PARAM_STR);
				$query_remove_admin->execute();
			}
			
			/**
			 * This part adds administrative status
			 */
			
			foreach($admin_add_list as $added_admin)
			{
				// Make the user an admin
				$query = "UPDATE `users` SET `admin` = 1 WHERE `user_id` = :user_id;";
				$query_admin = $this->db_connection->prepare($query);
				$query_admin->bindValue(':user_id', $added_admin, PDO::PARAM_STR);
				$query_admin->execute();
			}

			// Unsets variables so that they can't be re-triggered with a page refresh
			unset($_SESSION['admin_delete_list']);
			unset($_SESSION['admin_add_list']);
		}
		else
		{
			$this->errors[] = MESSAGE_DATABASE_ERROR;
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
				array_splice($user_array, $i, 1, true);
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
?>