<?hh // decl


	namespace HC;



	/**
	 * Class Authenticator
	 */

	class Authenticator extends Core

	{

		protected $settings = [];



		protected $base32LookupTable = [

				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
				'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
				'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
				'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
				'='  																		// padding character
		];



		protected $codeLength = 6;



		public function __construct($settings = [])

		{

			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['authenticator'])) {

				if (is_array($globalSettings['authenticator'])) {

					$this->settings = $this->parseOptions($settings, $globalSettings['authenticator']);

				}

			}



			$this->settings = $this->parseOptions($settings, $this->settings);



			return true;

		}



		public function __destruct()

		{

			$this->settings = null;

		}



		/**
		* @param int $secretLength
		* @return string
		*/

		public function createSecret($secretLength = 32)

		{

				$validChars = $this->base32LookupTable;



				unset($validChars[32]);



				$secret = '';

				for ($i = 0; $i < $secretLength; $i++) {

						$secret .= $validChars[array_rand($validChars)];

				}



				return $secret;

		}



		/**
		* @param string $secret
		* @param int|false $timeSlice
		* @return string
		*/

		public function getCode($secret, $timeSlice = false)

		{

				if ($timeSlice === false) {
					$timeSlice = floor(time() / 30);
				}



				$secretkey = $this->_base32Decode($secret);



				// Pack time into binary string
				$time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);



				// Hash it with users secret key
				$hm = hash_hmac('SHA1', $time, $secretkey, true);



				// Use last char of result as index/offset
				$offset = ord(substr($hm, -1)) & 0x0F;



				// grab 4 bytes of the result
				$hashpart = substr($hm, $offset, 4);
                
				// Unpack binary value
				$value = unpack('N', $hashpart);

				$value = $value[1];



				// Only 32 bits
				$value = $value & 0x7FFFFFFF;



				$modulo = pow(10, $this->codeLength);



				return str_pad($value % $modulo, $this->codeLength, '0', STR_PAD_LEFT);

		}



		/**
		* @param string $name
		* @param string $secret
		* @return string
		*/

		public function getQRCode($name, $secret, $size = 200) {

				return 'https://chart.googleapis.com/chart?chs=' . $size . 'x' . $size . '&chld=M|0&cht=qr&chl='. urlencode('otpauth://totp/' . $name . '?secret='. $secret);

		}



		/**
		* Check if the code is correct. This will accept codes starting from $discrepancy*30sec ago to $discrepancy*30sec from now
		*
		* @param string $secret
		* @param string $code
		* @param int $discrepancy This is the allowed time drift in 30 second units (8 means 4 minutes before or after)
		* @return bool
		*/

		public function verifyCode($secret, $code, $discrepancy = 1)

		{

				$currentTimeSlice = floor(time() / 30);



				for ($i = -$discrepancy; $i <= $discrepancy; $i++) {

						$calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);

						if ($calculatedCode == $code ) {

								return true;

						}

				}



				return false;

		}



		/**
		* Set the code length
		*
		* @param int $length
		* @return bool
		*/

		public function setCodeLength($length)

		{

				if($length < 6) {

					return false;

				}



				$this->codeLength = $length;

				return true;

		}



		/**
		* @param $secret
		* @return bool|string
		*/

		protected function _base32Decode($secret)

		{

				if (empty($secret)) return '';



				$base32chars = $this->base32LookupTable;

				$base32charsFlipped = array_flip($base32chars);



				$paddingCharCount = substr_count($secret, $base32chars[32]);

				$allowedValues = array(6, 4, 3, 1, 0);

				if (!in_array($paddingCharCount, $allowedValues)) return false;

				for ($i = 0; $i < 4; $i++){

						if ($paddingCharCount == $allowedValues[$i] &&

								substr($secret, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i])) return false;

				}

				$secret = str_replace('=','', $secret);

				$secret = str_split($secret);

				$binaryString = "";

				for ($i = 0; $i < count($secret); $i = $i+8) {

						$x = "";

						if (!in_array($secret[$i], $base32chars)) return false;

						for ($j = 0; $j < 8; $j++) {

								$x .= str_pad(base_convert(@$base32charsFlipped[@$secret[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);

						}

						$eightBits = str_split($x, 8);

						for ($z = 0; $z < count($eightBits); $z++) {

								$binaryString .= ( ($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48 ) ? $y:"";

						}

				}

				return $binaryString;

		}



		/**
		* @param string $secret
		* @param bool $padding
		* @return string
		*/

		protected function _base32Encode($secret, $padding = true)

		{

				if (empty($secret)) return '';



				$base32chars = $this->base32LookupTable;



				$secret = str_split($secret);

				$binaryString = "";

				for ($i = 0; $i < count($secret); $i++) {

						$binaryString .= str_pad(base_convert(ord($secret[$i]), 10, 2), 8, '0', STR_PAD_LEFT);

				}

				$fiveBitBinaryArray = str_split($binaryString, 5);

				$base32 = "";

				$i = 0;

				while ($i < count($fiveBitBinaryArray)) {

						$base32 .= $base32chars[base_convert(str_pad($fiveBitBinaryArray[$i], 5, '0'), 2, 10)];

						$i++;

				}

				if ($padding && ($x = strlen($binaryString) % 40) != 0) {

						if ($x == 8) $base32 .= str_repeat($base32chars[32], 6);

						elseif ($x == 16) $base32 .= str_repeat($base32chars[32], 4);

						elseif ($x == 24) $base32 .= str_repeat($base32chars[32], 3);

						elseif ($x == 32) $base32 .= $base32chars[32];

				}



				return $base32;

		}

	}

