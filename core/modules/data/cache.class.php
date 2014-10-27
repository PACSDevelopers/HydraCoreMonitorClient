<?hh // decl


	namespace HC;

	/**
	 * Class Cache
	 */

	class Cache extends Core

	{

		/**
		 * @var array
		 */

		protected $settings = [];



		/**
		 * @var DB|null $connection
		 */

		protected $connection = null;



		// Constructor


		/**
		 * @param (int|string|null)[] $settings
		 */

		public function __construct($settings = [])

		{

			// Parse default options
			$settings = $this->parseOptions($settings, ['ttl' => 3600, 'system' => 'auto']);



			// Parse global / local options
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			$settings = $this->parseOptions($settings, $globalSettings['cache']);



			// Temp fix @todo: fix default parsing
			if($settings['system'] === 'auto') {

				if(isset($globalSettings['cache']['system'])) {

					$settings['system'] = $globalSettings['cache']['system'];

				}

			}



			switch ($settings['system']) {

				case 'apc':

					if (!extension_loaded('apc')) {

						throw new \Exception('You do not have the apc extension enabled');

					}

					$settings['system'] = 'apc';

					break;

				case 'database':

					$this->initDB();

					$settings['system'] = 'database';

					break;

				default:

					// Figure out the best method
					if (extension_loaded('apc')) {

						$settings['system'] = 'apc';

					} else {

						$this->initDB();

						$settings['system'] = 'database';

					}

					break;

			}







			$this->settings = $settings;

		}



		public function __destruct()

		{

			$this->settings = null;

			$this->connection = null;

		}



		/**
		 * @param string $key
		 *
		 * @return string|string[]|bool|bool[]
		 */

		public function select($key)

		{

			$key = $this->parseKey($key);

			if (is_array($key)) {

				$result = [];

				foreach ($key as $value) {

					$result[] = $this->selectKey($value);

				}



				return $result;

			} else {

				return $this->selectKey($key);

			}



		}



		/**
		 * @param string $key
		 *
		 * @return bool|bool[]
		 */

		public function exists($key)

		{

			$key = $this->parseKey($key);

			if (is_array($key)) {

				$result = [];

				foreach ($key as $value) {

					$result[] = $this->keyExists($value);

				}



				return $result;

			} else {

				return $this->keyExists($key);

			}

		}



		/**
		 * @return bool
		 */

		public function deleteAll()

		{

			switch ($this->settings['system']) {

				case 'apc':

					return ((apc_clear_cache() === true) && (apc_clear_cache('user') === true)) ? true : false;



				case 'database':

					$result = $this->connection->query('TRUNCATE TABLE `HC_Cache`;', [], -1, true);

					if ($result) {

						return true;

					} else {

						$result = $this->connection->query('DELETE FROM `HC_Cache` WHERE `id` IS NOT NULL;', [], -1, true);

						if ($result) {

							return true;

						}

					}



					return false;

			}



			return false;

		}



		/**
		 * @return integer
		 */

		public function getTTL()

		{

			return $this->settings['ttl'];

		}



		/**
		 * @param string|array $key
		 *
		 * @return bool|bool[]
		 */

		public function delete($key)

		{

			$key = $this->parseKey($key);

			if (is_array($key)) {

				$result = [];

				foreach ($key as $value) {

					$result[] = $this->deleteKey($value);

				}



				return $result;

			} else {

				return $this->deleteKey($key);

			}

		}



		/**
		 * @param string|string[] $key
		 *
		 * @return string|bool|(string|bool)[]
		 */

		protected function selectKey($key)

		{

			$key = $this->parseKey($key);

			switch ($this->settings['system']) {

				case 'apc':

					if (is_array($key)) {

						$results = [];

						foreach ($key as $value) {

							$result = false;

							$value = apc_fetch($value, $result);



							if($result) {

								if($this->is_serialized($value)) {

									$results[] = unserialize($value);

								} else {

									$results[] = $value;

								}

							} else {

								$results[] = false;

							}

						}



						return $results;

					} else {

						$result = false;

						$value = apc_fetch($key, $result);



						if($result) {

							if($this->is_serialized($value)) {

								return unserialize($value);

							}

							return $value;

						}

					}


                return false;
                
				case 'database':

					$result = $this->connection->query('SELECT `cacheValue` FROM `HC_Cache` WHERE `cacheKey` = ?;', [$key], -1, true);

					// Check for result
					if ($result) {

						if (is_array($result[0])) {

							if (count($result) > 1) {

								$finalResult = [];

								foreach ($result as $row) {

									if (isset($row['cacheValue'])) {

										if($this->is_serialized($row['cacheValue'])) {

											$finalResult[] = unserialize($row['cacheValue']);

										} else {

											$finalResult[] = $row['cacheValue'];

										}

									} else {

										$finalResult[] = false;

									}

								}



								return $finalResult;

							} else {

								if (isset($result[0]['cacheValue'])) {

									if($this->is_serialized($result[0]['cacheValue'])) {

										return unserialize($result[0]['cacheValue']);

									} else {

										return $result[0]['cacheValue'];

									}



								}

							}

						}

					}

			}



			return false;

		}



		/**
		 * @param string $key
		 * @param string $value
		 * @param integer $overrideTTL
		 *
		 * @return bool|string
		 */

		public function selectInsert($key, $value, $overrideTTL = 0)

		{

			$key = $this->parseKey($key);

			// Check if the key exists
			if ($this->keyExists($key)) {

				// Key exists
				return $this->selectKey($key);

			} else {

				// Key does not exist, insert it
				if ($this->insert($key, $value, $overrideTTL)) {

					return $value;

				}

			}



			return false;

		}



		/**
		 * @param $key
		 *
		 * @return bool|string[]
		 */

		protected function keyExists($key)

		{

			$key = $this->parseKey($key);

			switch ($this->settings['system']) {

				case 'apc':

					return apc_exists($key);



				case 'database':

					$result = $this->connection->query('SELECT count(id) as `rowCount` FROM `HC_Cache` WHERE `cacheKey` = ?;', [$key], -1, true);

					if ($result) {

						if (isset($result[0])) {

							if ($result[0]['rowCount'] >= 1) {

								return true;

							}

						}

					}

			}



			return false;

		}



		/**
		 * @param string $key
		 * @param string $value
		 * @param integer $overrideTTL
		 *
		 * @return boolean
		 */

		public function insert($key, $value, $overrideTTL = 0)

		{

			$key = $this->parseKey($key);

			switch ($this->settings['system']) {

				case 'apc':

					return $this->insertKey($key, $value, $overrideTTL);



				case 'database':
					return $this->insertKey($key, $value, $overrideTTL);

			}



			return false;

		}



		/**
		 * @param string $key
		 * @param string $value
		 * @param integer $overrideTTL
		 *
		 * @return bool
		 */

		protected function insertKey($key, $value, $overrideTTL = 0)

		{

			$key = $this->parseKey($key);



			if(is_array($value)) {

				$value = serialize($value);

			}



			switch ($this->settings['system']) {

				case 'apc':

					if ($overrideTTL != 0) {

						return apc_store($key, $value, $overrideTTL);

					} else {

						return apc_store($key, $value);

					}



				case 'database':

					if ($overrideTTL != 0) {

						$result = $this->connection->query('INSERT INTO `HC_Cache` (`cacheKey`, `cacheValue`, `cacheTimeout`) VALUES (?, ?, ?);', [$key, $value, $overrideTTL], -1, true);

					} else {

						$result = $this->connection->query('INSERT INTO `HC_Cache` (`cacheKey`, `cacheValue`) VALUES (?, ?);', [$key, $value], -1, true);

					}

					if ($result) {

						return true;

					}

			}



			return false;

		}



		/**
		 * @param $key
		 *
		 * @return bool|string[]
		 */

		protected function deleteKey($key)

		{

			$key = $this->parseKey($key);

			if ($this->keyExists($key)) {

				switch ($this->settings['system']) {

					case 'apc':

						return apc_delete($key);



					case 'database':

						$result = $this->connection->query('DELETE FROM `HC_Cache` WHERE `cacheKey`= ?;', [$key], -1, true);



						if ($result) {

							return true;

						}

				}

			}



			return false;

		}



		/**
		 * @return bool
		 * @throws \Exception
		 */

		protected function initDB()

		{

			$this->connection = new DB(['useCache' => false]);



			if ($this->connection) {

				return true;

			}



			return false;

		}



		/**
		 * @param $key
		 * @return string
		 */

		protected function parseKey($key)

		{


			if (is_array($key)) {

				foreach ($key as &$value) {

					$value = $this->parseKey($value);

				}



				return $key;

			}



			// Check to make sure it wasn't already parsed
			if (mb_strpos($key, SITE_DOMAIN . SITE_NAME . ENVIRONMENT) !== false) {

				return $key;

			}


			// Make sure keys are unique even if we are using the same server
			return $key . SITE_DOMAIN . SITE_NAME . ENVIRONMENT;

		}



		/**
		 * Tests if an input is valid PHP serialized string.
		 *
		 * Checks if a string is serialized using quick string manipulation
		 * to throw out obviously incorrect strings. Unserialize is then run
		 * on the string to perform the final verification.
		 *
		 * Valid serialized forms are the following:
		 * <ul>
		 * <li>boolean: <code>b:1;</code></li>
		 * <li>integer: <code>i:1;</code></li>
		 * <li>double: <code>d:0.2;</code></li>
		 * <li>string: <code>s:4:"test";</code></li>
		 * <li>array: <code>a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}</code></li>
		 * <li>object: <code>O:8:"stdClass":0:{}</code></li>
		 * <li>null: <code>N;</code></li>
		 * </ul>
		 *
		 * @author		Chris Smith <code+php@chris.cs278.org>
		 * @copyright	Copyright (c) 2009 Chris Smith (http://www.cs278.org/)
		 * @license		http://sam.zoy.org/wtfpl/ WTFPL
		 * @param		string	$value	Value to test for serialized form
		 * @param		mixed	$result	Result of unserialize() of the $value
		 * @return		boolean			True if $value is serialized data, otherwise false
		 */

		private function is_serialized($value, &$result = null)

		{

			// Bit of a give away this one
			if (!is_string($value))

			{

				return false;

			}



			// Serialized false, return true. unserialize() returns false on an
			// invalid string or it could return false if the string is serialized
			// false, eliminate that possibility.
			if ($value === 'b:0;')

			{

				$result = false;

				return true;

			}



			$length	= mb_strlen($value);

			$end	= '';



			switch ($value[0])

			{

				case 's':

					if ($value[$length - 2] !== '"')

					{

						return false;

					}

				case 'b':

				case 'i':

				case 'd':

					// This looks odd but it is quicker than isset()ing
					$end .= ';';

				case 'a':

				case 'O':

					$end .= '}';



					if ($value[1] !== ':')

					{

						return false;

					}



					switch ($value[2])

					{

						case 0:

						case 1:

						case 2:

						case 3:

						case 4:

						case 5:

						case 6:

						case 7:

						case 8:

						case 9:

						break;



						default:

							return false;

					}

				case 'N':

					$end .= ';';



					if ($value[$length - 1] !== $end[0])

					{

						return false;

					}

				break;



				default:

					return false;

			}



			if (($result = @unserialize($value)) === false)

			{

				$result = null;

				return false;

			}

			return true;

		}



	}
