<?hh // decl


	namespace HC;



	/**
	 * Class File
	 * @package HC
	 */

	class File extends Core

	{

		/**
		 * @var array
		 */

		protected $settings = [];



		// Constructor


		/**
		 * @param (int|string|null)[] $settings
		 */

		public function __construct($settings = [])

		{



			$settings = $this->parseOptions($settings, []);



			// Parse global / local settings
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['file'])) {

				if (is_array($globalSettings['file'])) {

					$settings = $this->parseOptions($settings, $globalSettings['file']);

				}

			}



			$this->settings = $settings;

		}



		/**
		 *
		 */

		public function __destruct()

		{

			$this->settings = null;

		}



		/**
		 * @param string $file
		 * @return string|false
		 */

		public function read($file)

		{

			if (is_file($file)) {

				return file_get_contents($file);

			}



			return false;

		}



		/**
		 * @param string $file
		 * @param string $contents
		 * @return bool
		 */

		public function write($file, $contents)

		{

			$status = file_put_contents($file, $contents);

			if ($status !== false) {

				return true;

			}



			return false;

		}



		/**
		 * @param $file
		 * @return bool
		 */

		public function delete($file)

		{

			if (is_file($file)) {

				return unlink($file);

			}



			return false;

		}



		/**
		 * @param string $src
		 * @param string $dest
		 *
		 * @return bool
		 */

		public function move($src, $dest)

		{

			if (is_file($src)) {

				return rename($src, $dest);

			} elseif (is_dir($dest)) {

				return rename($src, $dest . '/' . basename($src));

			}



			return false;

		}



		/**
		 * @param string $source
		 * @param string $destination
		 *
		 * @return bool
		 */

		public function copy($source, $destination)

		{

			if (is_file($source)) {

				return rename($source, $destination);

			}



			return false;

		}

	}

