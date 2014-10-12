<?hh // decl


	namespace HC;

	/**
	 * Class Compression
	 */

	class Compression extends Core

	{

		/**
		 * @var null
		 */

		protected $settings = null;



		// @todo: Complete Compression Class
		/**
		 * @param array $settings
		 */

		public function __construct($settings = [])

		{

			// Parse global / local settings
			$settings = $this->parseOptions($settings, []);

			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if(isset($globalSettings['compression'])) {

				$settings = $this->parseOptions($settings, $globalSettings['compression']);

			}



			$this->settings = & $settings;



			return true;

		}



		public function __destruct()

		{

			$this->settings = null;

		}



		public function compress($string, $level = 9){

			if($level > 9 || $level < 1) {

				throw new \Exception('Invalid Compression Level');

			}



			return gzcompress($string, $level);

		}



		public function decompress($string) {

			return gzuncompress($string);

		}



		public function compressFile($source, $destination, $level = 9) {

			if((is_file($source) && (!is_file($destination)))) {

				$destination = gzopen($destination, 'wb' . $level);

				$source = fopen($source,'rb');



				while (!feof($source)) {

					gzwrite($destination, fread($source, 24000000)); // Decompress the file in 24 MB chunks
				}

				fclose($source);



				gzclose($destination);



				return true;

			}



    	return false;

		}



		public function decompressFile($source, $destination) {

			if((is_file($source) && (!is_file($destination)))) {

				$source = gzopen($file_name, 'rb');

				$destination = fopen($destination, 'wb');



				while(!gzeof($source)) {

				  fwrite($destination, gzread($source, 24000000)); // Decompress the file in 24 MB chunks
				}



				fclose($destination);

				gzclose($source);



				return true;

			}



			return false;

		}

	}

