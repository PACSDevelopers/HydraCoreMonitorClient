<?hh // decl
	namespace HC;



	require_once(HC_CORE_LOCATION . '/modules/data/db.class.php');



	/**
	 * Class User
	 * @package HC
	 */

	class User extends Core

	{

		// Setup class public variables


		// Setup class protected variables
        /**
         * @var \HC\DB
         */

        protected $db;



				/**
				 * @var null
				 */

				protected $userID = null; // Don't change this, ever


        /**
         * @var
         */

        protected $userData = [];



        /**
         * @var
         */

        protected $groups = null;



        /**
         * @var
         */

        protected $permissions = null;



		/**
		 * Constructor
		 *
		 */

		public function __construct($user = [])

		{

			// If no options, stop
			if($user === []) {

				return false;

			}

			// Encrypt the password to match the db
			if(isset($user['password'])) {

				$user['password'] = $this->encryptPassword($user['password']);

			}



			// Get user details
			$this->db = new DB();



			$result = $this->db->read('users', [], $user);



			if ($result) {

				if (isset($result[0])) {

          $this->userID = $result[0]['id'];

					$this->userData = $result[0];

				} else {

					// No row
					return false;

				}

			} else {

				// No result
				return false;

			}



			return true;

		}

		public function checkExists() {
			return !is_null($this->userID);
		}

		protected function getGroupsData() {
			// Get the groups this user is in
			$result = $this->db->query('SELECT
																	`UGM`.`groupID`,
																	`UG`.`groupTitle`
															FROM
																	`user_groups_mapping` `UGM`
															LEFT JOIN
																	`user_groups` `UG` ON (`UG`.`id` = `UGM`.`groupID`)
															WHERE
																	`UGM`.`userID` = ?;', [$this->userID]);

			$groupIDS = [];

			if ($result) {

					foreach ($result as $group) {

							array_push($groupIDS, $group['groupID']);

							$this->groups[$group['groupID']] = $group['groupTitle'];

					}
					return true;
			}

			return false;
		}

		protected function getPermissionsData() {
			// Get permissions
			$result = $this->db->query('SELECT
																	    `P`.`title`
																	FROM
																	    `users` `U`
																		LEFT JOIN
																			`user_groups_mapping` `UGM` ON (`UGM`.`userID` = `U`.`id`)
																		LEFT JOIN
																			`user_groups` `UG` ON (`UG`.`id` = `UGM`.`groupID`)
																			LEFT JOIN
																	    `permissions_mapping` `PM` ON ((`PM`.`itemID` = `U`.`id` AND `PM`.`itemType` = 1) OR (`PM`.`itemID` = `UG`.`id` AND `PM`.`itemType` = 2))
																			LEFT JOIN
																	    `permissions` `P` ON (`P`.`id` = `PM`.`permission`)
																	WHERE
																		`U`.`id` = ?
																	GROUP BY `P`.`id`;', [$this->userID]);
			if ($result) {
					foreach ($result as $permission) {
							$this->permissions[$permission['title']] = true;
					}

					return true;
			}

			return false;
		}

		public function getUserName($escaped = false) {
			if($escaped) {
				return htmlentities($this->userData['firstName'] . ' ' . $this->userData['lastName'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
			} else {
				return $this->userData['firstName'] . ' ' . $this->userData['lastName'];
			}
		}


		public function __destruct()

		{

			$this->userID = null;

			$this->userData = null;

			$this->groups = null;

		}



		// Setup Public Functions
		/**
		 * @return mixed
		 */

		public function getGroups()

		{
			if(is_null($this->groups)) {
				if($this->getGroupsData()) {
					return $this->groups;
				}
			} else {
				return $this->groups;
			}

			return [];
		}

		/**
		 * @param $group
		 *
		 * @return bool
		 */

		public function hasGroup($group)

		{
			if(is_null($this->groups)) {
				if($this->getGroupsData()) {
					return isset($this->groups[$group]);
				}
			} else {
				return isset($this->groups[$group]);
			}

			return false;
		}



    public function hasPermission($permission) {
				if(is_null($this->permissions)) {
					if($this->getPermissionsData()) {
						return isset($this->permissions[$permission]);
					}
				} else {
					return isset($this->permissions[$permission]);
				}

				return false;
    }



		/**
		 * @return null
		 */

		public function getUserID()

		{

			return $this->userID;

		}



		/**
		 * @return null
		 */

		public function getUserData()

		{

			return $this->userData;

		}





		// Setup protected Functions
		/**
		 *
		 */

		public static function endSession()

		{



			if (session_status() == PHP_SESSION_ACTIVE) {
				setcookie(session_name(), session_id(), 0, '/', NULL, true);

				if(isset($_SESSION['user'])) {
					$_SESSION['user'] = null;
					unset($_SESSION['user']);
				}

				$_SESSION = null;
				unset($_SESSION);

                session_unset();
                session_destroy();
                session_write_close();
			}

		}



		/**
		 *
		 */

		public static function startSession()

		{



			if (session_status() != PHP_SESSION_ACTIVE) {

				session_start();

			}

		}



		/**
		 *
		 */

		public static function isSessionActive()

		{

			if (session_status() == PHP_SESSION_ACTIVE) {

				if (isset($_SESSION['user'])) {

					return true;

				}

			}



			return false;

		}



		/**
		 * @return mixed
		 */

		public static function getIP(){

	    if (isset($_SERVER['X_REAL_IP']) && $_SERVER['X_REAL_IP'] !== '127.0.0.1') {
				return $_SERVER['X_REAL_IP'];
			} else if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] !== '127.0.0.1') {
				return $_SERVER['HTTP_CLIENT_IP'];
			} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] !== '127.0.0.1') {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else if(isset($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'] !== '127.0.0.1') {
				return  $_SERVER['HTTP_X_FORWARDED'];
			} else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'] !== '127.0.0.1') {
				return $_SERVER['HTTP_FORWARDED_FOR'];
			} else if(isset($_SERVER['HTTP_FORWARDED']) && $_SERVER['HTTP_FORWARDED'] !== '127.0.0.1'){
				return $_SERVER['HTTP_FORWARDED'];
			} else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
				return $_SERVER['REMOTE_ADDR'];
			}

	    return false;
		}



		/**
		 * @param $password
		 * @param $hash
		 *
		 * @return bool
		 */

		static public function verifyPassword($password, $hash)

		{

			return (\HC\User::encryptPassword($password) == $hash);

		}



		/**
		 * @param $password
		 *
		 * @return string
		 */

		static public function encryptPassword($password)

		{



			$encryption = new \HC\Encryption();

			$userSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			$userSettings = $userSettings['users'];

			return $encryption->hash($password, ['salt' => $userSettings['salt']]);

		}



		public function createPasswordResetKey() {

			$encryption = new \HC\Encryption();

			$key = $encryption->hash(uniqid(mt_rand(0, 1000), true) . microtime(), ['salt' => 'PASSWORD_RESET_SALT', 'hashlength' => 255]);

			if($this->db->update('users', ['id' => $this->userID], ['passwordResetKey' => $key])) {

				return $key;

			}

			return false;

		}

		public static function getUser($settings = []) {
				if(!empty($settings)) {
						$db = new \HC\DB(['useCache' => false]);
						$result = $db->read('users', [], $settings);
						if($result) {
								return $result[0];
						}
				}
				return false;
		}

		public function __set($key, $value)
		{
				$this->userData[$key] = $value;
				return true;
		}

		public function __get($key) {
            if (array_key_exists($key, $this->userData)) {
                    return $this->userData[$key];
            }

            return false;
		}

		public function __isset($key)
		{
				return isset($this->userData[$key]);
		}

		public function __unset($key)
		{
				unset($this->userData[$key]);
		}

	}



    // Start sessions
    if (!User::isSessionActive()) {

        User::startSession();

    }
