<?hh // decl


	namespace HC;

	/**
	 * Class Encryption
	 */

	class Encryption extends Core

	{

		/**
		 * @var null
		 */

		protected $settings = null;



		/**
		 * @var null
		 */

		protected $incrementalHash = null;



		// @todo: Complete Encryption Class
		/**
		 * @param array $settings
		 */

		public function __construct($settings = [])

		{

			// Parse global / local settings
			$settings = $this->parseOptions($settings, ['hashingMethod' => false, 'encryptionMethod' => false, 'encryptionExtension' => false]);

			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			$settings = $this->parseOptions($settings, $globalSettings['encryption']);



			$this->settings = & $settings;



			if (!$settings['encryptionExtension']) {

				if (extension_loaded('openssl')) {

					$settings['encryptionExtension'] = 'openssl';

				} elseif (extension_loaded('mcrypt')) {

					$settings['encryptionExtension'] = 'mcrypt';

				} else {

					throw new \Exception('OpenSSL / mCrypt need to be installed in order to use encryption.');

				}



			}



			if (!$settings['hashingMethod']) {

				$settings['hashingMethod'] = $this->getBestHashingMethod();

				if (!$settings['hashingMethod']) {

					throw new \Exception('Could not get best best hashing method.');

				}

			}



			if (!$settings['encryptionMethod']) {

				$settings['encryptionMethod'] = $this->getBestEncryptionMethod();

				if (!$settings['encryptionMethod']) {

					throw new \Exception('Could not get best best encryption method.');

				}

			}



			return true;

		}



		public function __destruct()

		{

			$this->settings = null;

			$this->incrementalHash = null;

		}



		/**
		 * @param string $value
		 * @param array $settings
		 *
		 * @return array|bool|string
		 */

		public function hash($value, $settings = [])

		{

			$settings = $this->parseOptions($settings, ['salt' => null, 'advanced' => false, 'hashlength' => 16384]);



			// If no salt is set, generate one
			if ($settings['salt'] === null) {

				// Create salt
				$settings['salt'] = hash($this->settings['hashingMethod'], uniqid('', $settings['advanced']));



				$blockSize = mb_strlen($settings['salt']);



				for($i = 1; $i < ($settings['hashlength'] / $blockSize); $i++) {

						$settings['salt'] .= hash($this->settings['hashingMethod'], $settings['salt'] . $i);

				}



				$hash = hash($this->settings['hashingMethod'], $settings['salt'] . $value . $settings['salt']);



				$blockSize = mb_strlen($hash);



				for($i = 1; $i < ($settings['hashlength'] / $blockSize); $i++) {

					$hash .= hash($this->settings['hashingMethod'], $settings['salt'] . $value . $i . $settings['salt']);

				}



				// Return hash and salt
				return [

					$hash,

					$settings['salt']

				];

			}



			$settings['salt'] = hash($this->settings['hashingMethod'], $settings['salt']);



			$blockSize = mb_strlen($settings['salt']);



			for($i = 1; $i < ($settings['hashlength'] / $blockSize); $i++) {

					$settings['salt'] .= hash($this->settings['hashingMethod'], $settings['salt'] . $i);

			}



			$hash = hash($this->settings['hashingMethod'], $settings['salt'] . $value . $settings['salt']);



			$blockSize = mb_strlen($hash);



			for($i = 1; $i < ($settings['hashlength'] / $blockSize); $i++) {

				$hash .= hash($this->settings['hashingMethod'], $settings['salt'] . $value . $i . $settings['salt']);

			}



			// Return hash
			return $hash;

		}



		/**
		 * @param string $data
		 * @param string $password
		 * @param string $iv
		 * @return string|false
		 * @throws \Exception
		 */

		public function encrypt($data, $password, $iv = '')

		{

			switch ($this->settings['encryptionExtension']) {

				case 'openssl';

					if ($iv == '') {

						$iv = mb_substr(sha1($password) . sha1($password), 0, openssl_cipher_iv_length($this->settings['encryptionMethod']));

					}

					$output = openssl_encrypt($data, $this->settings['encryptionMethod'], $password, 0, $iv);

					break;

				case 'mcrypt';

					if ($iv == '') {

						$iv = mb_substr(sha1($password) . sha1($password), 0, mcrypt_get_iv_size($this->settings['encryptionMethod'], MCRYPT_MODE_CBC));

					}

					$output = base64_encode(mcrypt_encrypt($this->settings['encryptionMethod'], $password, $data, MCRYPT_MODE_CBC, $iv));

					break;

				default:

					throw new \Exception('Extension not defined' . $this->settings['encryptionMethod']);

			}



			if ($output) {

				return $output;

			}



			return false;

		}



		/**
		 * @param string $data
		 * @param string $password
		 * @param string $iv
		 *
		 * @return string|false
		 */

		public function decrypt($data, $password, $iv = '')

		{

			switch ($this->settings['encryptionExtension']) {

				case 'openssl';

					if ($iv == '') {

						$iv = mb_substr(sha1($password) . sha1($password), 0, openssl_cipher_iv_length($this->settings['encryptionMethod']));

					}

					$output = openssl_decrypt($data, $this->settings['encryptionMethod'], $password, 0, $iv);

					break;

				case 'mcrypt';

					if ($iv === false) {

						$iv = mb_substr(sha1($password) . sha1($password), 0, mcrypt_get_iv_size($this->settings['encryptionMethod'], MCRYPT_MODE_CBC));

					}

					$output = rtrim(mcrypt_decrypt($this->settings['encryptionMethod'], $password, base64_decode($data), MCRYPT_MODE_CBC, $iv), '\0');

					break;

				default:

					throw new \Exception('Extension not defined');

			}



			if ($output) {

				return $output;

			}



			return false;

		}



		/**
		 * @param string $file
		 * @param bool $raw
		 *
		 * @return false|string
		 */

		public function hashFile($file, $raw = false)

		{



			// Make sure we have a method
			if ($this->settings['hashingMethod'] === null) {

				return false;

			}



			if (is_file($file)) {

				return hash_file($this->settings['hashingMethod'], $file, $raw);

			}



			return false;

		}



		/**
		 * @return bool
		 */

		public function initIncrementalHash()

		{



			// Make sure we have a method
			if ($this->settings['hashingMethod'] === null) {

				return false;

			}



			// Start the incremental hash
			$this->incrementalHash = hash_init($this->settings['hashingMethod']);



			// If success, return true
			if ($this->incrementalHash) {

				return true;

			}



			return false;

		}



		/**
		 * @param string $value
		 *
		 * @return bool
		 */

		public function incrementHash($value)

		{



			// Make sure we have a method
			if ($this->settings['hashingMethod'] === null) {

				return false;

			}



			// If success
			if ($this->incrementalHash) {

				hash_update($this->incrementalHash, $value);



				return true;

			}



			return false;

		}



		/**
		 * @param string $file
		 *
		 * @return bool
		 */

		public function incrementHashFile($file)

		{



			// Make sure we have a method
			if ($this->settings['hashingMethod'] === null) {

				return false;

			}



			// If success
			if ($this->incrementalHash) {

				hash_update_file($this->incrementalHash, $file);



				return true;

			}



			return false;

		}



		/**
		 * @return string|false
		 */

		public function getIncrementalHash()

		{



			// If we have a incremental hash
			if ($this->incrementalHash !== null) {

				return hash_final($this->incrementalHash);

			}



			return false;

		}



		/**
		 * @return bool|string
		 */

		public function getHashingMethod()

		{



			if ($this->settings['hashingMethod'] !== null) {

				return $this->settings['hashingMethod'];

			}



			return false;

		}



		/**
		 * @return bool|string
		 */

		public function getEncryptionMethod()

		{



			if ($this->settings['encryptionMethod'] !== null) {

				return $this->settings['encryptionMethod'];

			}



			return false;

		}



		/**
		 * @return string|false
		 */

		public function getBestHashingMethod()

		{

			// We need to figure out the best method
			$algos = $this->getAvailableHashingMethods();



			// Desired algorithms in order of strength
			$desiredAlgos = [

				'sha512',

				'whirlpool',

				'sha384',

				'ripemd320',

				'sha256',

				'sha224',

				'sha1'

			];



			// Use the best one we can find, at least one should be
			foreach ($desiredAlgos as $algo) {

				if (in_array($algo, $algos)) {

					return $algo;

				}

			}



			return false;

		}



		/**
		 * @return array
		 */

		public function getAvailableHashingMethods()

		{

			return hash_algos();

		}



		/**
		 * @return array|bool
		 * @throws \Exception
		 */

		public function getAvailableEncryptionMethods()

		{

			$tempMethod = $this->settings['encryptionMethod'];



			switch ($this->settings['encryptionExtension']) {

				case 'openssl';

					$output = openssl_get_cipher_methods();

					break;

				case 'mcrypt';

					$output = mcrypt_list_algorithms();

					break;

				default:

					throw new \Exception('Extension not defined');

			}



			$finalOutput = [];



			foreach ($output as $algo) {

				$this->settings['encryptionMethod'] = $algo;

				$a = uniqid();

				$b = uniqid();

				$encrypted = $this->encrypt($a, $b);

				if ($encrypted) {

					$decrypted = $this->decrypt($encrypted, $b);

					if ($decrypted == $a) {

						$finalOutput[] = $algo;

					}

				}

			}



			$this->settings['encryptionMethod'] = $tempMethod;



			if ($finalOutput != []) {

				return $finalOutput;

			}



			return false;

		}



		/**
		 * @return string|false
		 * @throws \Exception
		 */

		public function getBestEncryptionMethod()

		{

			$algos = $this->getAvailableEncryptionMethods();



			if (!$algos) {

				throw new \Exception('Could not get available encryption methods');

			}



			switch ($this->settings['encryptionExtension']) {

				case 'openssl';

					// Desired algorithms in order of strength
					$desiredAlgos = [

						'AES-256-CBC',

						'CAMELLIA-256-CBC'

					];

					break;

				case 'mcrypt';

					// Desired algorithms in order of strength
					$desiredAlgos = [

						'cast-256',

						'rijndael-256',

						'rijndael-192',

						'tripledes',

						'serpent',

						'blowfish'

					];

					break;

				default:

					throw new \Exception('Extension not defined');

			}



			// Use the best one we can find, at least one should be
			foreach ($desiredAlgos as $algo) {

				if (in_array($algo, $algos)) {

					return $algo;

				}

			}







			return false;

		}

	}

