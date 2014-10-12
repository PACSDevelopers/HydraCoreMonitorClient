<?hh // decl


	namespace HC;



	/**
	 * Class Directory
	 * @package HC
	 */

	class Directory extends Core

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

			if (isset($globalSettings['directory'])) {

				if (is_array($globalSettings['directory'])) {

					$settings = $this->parseOptions($settings, $globalSettings['directory']);

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
		 * @param string $dir
		 * @return array
		 */

		public function get($dir)

		{

			if (!is_dir($dir)) {

				return false;

			}



			$dir = new \DirectoryIterator($dir);



			// Directory array
			$data = [];

			// Loop through each directory as a node
			foreach ($dir as $node) {

				// If we are dealing with a directory and not a dot / dotdot
				if ($node->isDir() && !$node->isDot()) {

					// Recursively store each file
					$data[$node->getFilename()] = $this->get($node->getPathname());

				} // If we are dealing with a file
				else if ($node->isFile()) {

					// Store the file in the array
					$data[] = $node->getFilename();

				}

			}



			return $data;

		}



		/**
		 * @param string $dir
		 * @return bool
		 */

		public function delete($dir)

		{



			if (!is_dir($dir)) {

				return false;

			}



			// Get just the files/folders we need to remove
			$files = array_diff(scandir($dir), [

				'.',

				'..'

			]);

			// Loop through them
			foreach ($files as $file) {

				// Delete them
				(is_dir("$dir/$file")) ? $this->delete("$dir/$file") : unlink("$dir/$file");

			}



			// Remove the final directory
			return rmdir($dir);

		}



		/**
		 * @param string $src
		 * @param string $dest
		 *
		 * @return bool
		 */

		public function move($src, $dest)

		{



			// If source is not a directory stop processing
			if (!is_dir($src)) {

				return false;

			}



			// If the destination directory does not exist create it
			if (!is_dir($dest)) {

				if (!mkdir($dest)) {

					// If the destination directory could not be created stop processing
					return false;

				}

			}



			// Open the source directory to read in files
			$i = new \DirectoryIterator($src);

			foreach ($i as $f) {

				// If this is a filke
				if ($f->isFile()) {

					// Check if the file already exists
					if (is_file("$dest/" . $f->getFilename())) {



						// Get the hash of both files
						$srcMD5 = md5_file($f->getRealPath());

						$destMD5 = md5_file("$dest/" . $f->getFilename());



						// If they don't match
						if ($srcMD5 != $destMD5) {

							// Rename it
							rename($f->getRealPath(), "$dest/" . $f->getFilename());

						}

					} else {

						// Just rename it
						rename($f->getRealPath(), "$dest/" . $f->getFilename());

					}

				} elseif (!$f->isDot() && $f->isDir()) {

					// This is a directory, move it recursively
					$this->move($f->getRealPath(), "$dest/$f");

					// Cleanup
					rmdir($f->getRealPath());

				}

			}

			// Cleanup
			unset($src);



			return true;

		}



		/**
		 * @param string $source
		 * @param string $destination
		 *
		 * @return bool
		 */

		public function copy($source, $destination)

		{

			// If source is not a directory stop processing
			if (!is_dir($source)) {

				return false;

			}



			// If the destination directory does not exist create it
			if (!is_dir($destination)) {

				if (!mkdir($destination)) {

					// If the destination directory could not be created stop processing
					return false;

				}

			}



			// Open the source directory to read in files
			$i = new \DirectoryIterator($source);

			foreach ($i as $f) {

				// if this is a file
				if ($f->isFile()) {

					// Check if the file already exists
					if (is_file("$destination/" . $f->getFilename())) {



						// Get the hash of both files
						$srcMD5 = md5_file($f->getRealPath());

						$destMD5 = md5_file("$destination/" . $f->getFilename());



						// If they don't match
						if ($srcMD5 != $destMD5) {

							// Copy it
							copy($f->getRealPath(), "$destination/" . $f->getFilename());

						}

					} else {

						// Copy it
						copy($f->getRealPath(), "$destination/" . $f->getFilename());

					}



				} elseif (!$f->isDot() && $f->isDir()) {

					// Recursively copy it
					$this->copy($f->getRealPath(), "$destination/$f");

				}

			}



			return true;

		}



	}

