<?hh // decl


	namespace HC\Hooks\PostReceive;



	/**
	 * Class CompileResources
	 * @package HC\Hooks\PostReceive
	 */

	class CompileResources extends \HC\Hooks\PostReceive

	{

		/**
		 * @var \HC\Directory
		 */

		protected $directoryHandler;

		/**
		 * @var array
		 */

		protected $settings;
        
        protected $cache;

        
		/**
		 * @param array $settings
		 */

		public function __construct($settings = [])

		{

			$this->directoryHandler = new \HC\Directory();

			$this->settings = $settings;

		}



		/**
		 * @return bool
		 */

		public function run()

		{

			// Get the resource path
			if (isset($this->settings)) {

				if (isset($this->settings['path'])) {

					$realDirectoyPath = realpath(HC_LOCATION . '/public/' . $this->settings['path']);

				} else {

					$realDirectoyPath = realpath(HC_LOCATION . '/public/resources/');

				}



				// Create the temp working directory
				mkdir('tmp', 0777, true);



				// Move into that directory
				chdir('tmp');



                // Get the tmp path
                $realTMPPath = HC_LOCATION . '/hooks/tmp';



				// Copy resources into tmp
				$this->directoryHandler->copy($realDirectoyPath, $realTMPPath);



				echo 'Copying resources into temporary working directory' . PHP_EOL;



				if (isset($this->settings['languages'])) {

					// Process according to settings
					foreach ($this->settings['languages'] as $row => $value) {

						// If this option is enabled
						if ($value === true) {

							// Process the type
							if ($this->processTypes($row, $realDirectoyPath, $realTMPPath, $realDirectoyPath) === false) {

								// Tell us if it failed
								echo 'Failed' . PHP_EOL;



								return false;

							}

						}

					}

					chdir('../');



					echo 'Updating public with new compiled resources' . PHP_EOL;



					// Replace live files with the new ones in tmp
					$this->directoryHandler->copy($realTMPPath, $realDirectoyPath);



					// Remove the temp directory
					$this->directoryHandler->delete($realTMPPath);

				} else {

					echo 'No languages defined.' . PHP_EOL;

				}

			} else {

				echo 'Not enabled.' . PHP_EOL;

			}



			return true;

		}

    
        public function checkFileChanged($file) {
            if(!isset($this->cache)) {
                $this->cache = [];
                if(file_exists(HC_TMP_LOCATION . '/compilation/cache.json')) {
                    $this->cache = json_decode(file_get_contents(HC_TMP_LOCATION . '/compilation/cache.json'), true);
                } else {
                    if(!is_dir(HC_TMP_LOCATION)) {
                        mkdir(HC_TMP_LOCATION, 0777);
                    }
                    mkdir(HC_TMP_LOCATION . '/compilation', 0777);
                }
            }

            $newMD5 = md5(preg_replace('/\s+/', '', file_get_contents($file)));
            if(isset($this->cache[$file])) {
                if($this->cache[$file] === $newMD5) {
                    $this->cache[$file] = $newMD5;
                    file_put_contents(HC_TMP_LOCATION . '/compilation/cache.json', json_encode($this->cache));
                    return false;
                }
            }

            $this->cache[$file] = $newMD5;
            file_put_contents(HC_TMP_LOCATION . '/compilation/cache.json', json_encode($this->cache));
            
            return true;
        }

		// This will process all files according to type
		/**
		 * @param string $type
		 * @param string $path
		 * @param string $tmpPath
		 * @param string $realDirectoyPath
		 *
		 * @return bool
		 */

		public function processTypes($type, $path, $tmpPath, $realDirectoyPath)

		{

			// Output what type is processing
			echo 'Processing ' . mb_strtoupper($type) . PHP_EOL;

			// Process files according to type
			switch ($type) {

				case 'js';

					if (!is_dir($path . '/.js')) {

						// Create the directory if it doesn't exist
						mkdir($path . '/.js/', 0777, true);

					}

					if (!is_dir($tmpPath . '/js')) {

						// Create the directory if it doesn't exist
						mkdir($tmpPath . '/js/', 0777, true);

					}

					// Get all the files we need to process
					$result = $this->processFiles($this->directoryHandler->get($path . '/.js/'));



					// Sort those files by length (!IMPORTANT if not sorted by length, you do not create directories before you process the file)
					usort($result, function ($a, $b) {

						return mb_strlen($a) - mb_strlen($b);

					});



					// Loop through each file
					foreach ($result as $row => $value) {

                        if(substr(basename($value), 0, 1) === '_') {
                            continue;
                        }


						// Define where the new compiled file is going
						$oldPath = '.js/' . $value;

						$newPath = 'js/' . str_replace('.js', '.min.js', $value);

						$sourceMapName = str_replace($tmpPath, '.', $newPath) . '.map';

						$oldTruePath = $realDirectoyPath . '/' . $oldPath;

						$newTruePath = $realDirectoyPath . '/' . $newPath;
                        

						if (file_exists($newTruePath) && !$this->checkFileChanged($oldTruePath)) {

							continue;

						}
                        
                        
						// Check that the directory exists
						$directory = dirname($newPath) . '/';



						if (!is_dir($directory)) {

							// Create the directory if it doesn't exist
							mkdir($directory, 0777, true);

						}



						// Output that we are processing that file
						echo '	Processing File (With SourceMap): ' . $value . PHP_EOL;



						// Process the file
						$output = $this->processFile('java -jar /bin/closure-compiler.jar --language_in ECMASCRIPT5 --formatting SINGLE_QUOTES --js ' . $oldPath . ' --js_output_file ' . $newPath . ' --source_map_format=V3 --create_source_map "' . $sourceMapName . '"');

						// If the output has generated an error, stop
						if ($output === false) {

							return false;

						}

					}

					break;

				case 'scss':

					if (!is_dir($path . '/.scss')) {

						// Create the directory if it doesn't exist
						mkdir($path . '/.scss/', 0777, true);

					}

					if (!is_dir($tmpPath . '/scss')) {

						// Create the directory if it doesn't exist
						mkdir($tmpPath . '/scss/', 0777, true);

					}

					// Get all the files we need to process
					$result = $this->processFiles($this->directoryHandler->get($path . '/.scss/'));



					// Sort those files by length (!IMPORTANT if not sorted by length, you do not create directories before you process the file)
					usort($result, function ($a, $b) {

						return mb_strlen($a) - mb_strlen($b);

					});



					// Loop through each file
					foreach ($result as $row => $value) {

                        if(substr(basename($value), 0, 1) === '_') {
                            continue;
                        }
                        
						// Define where the new compiled file is going
						$oldPath = '.scss/' . $value;

						$newPath = 'scss/' . str_replace('.scss', '.scss.css', $value);

						$oldTruePath = $realDirectoyPath . '/' . $oldPath;

						$newTruePath = $realDirectoyPath . '/' . $newPath;


                        if (file_exists($newTruePath) && !$this->checkFileChanged($oldTruePath)) {

                            continue;

                        }



						// Check that the directory exists
						$directory = dirname($newPath) . '/';



						if (!is_dir($directory)) {

							// Create the directory if it doesn't exist
							mkdir($directory, 0777, true);

						}



						// Output that we are processing that file
						echo '	Processing File (With SourceMap): ' . $value . PHP_EOL;



						// Process the file
						$output = $this->processFile('scss --style compressed ' . $oldPath . ' ' . $newPath);



						// If the output has generated an error, stop
						if ($output === false) {

							return false;

						}

					}

					break;

				case 'less':

					if (!is_dir($path . '/.less')) {

						// Create the directory if it doesn't exist
						mkdir($path . '/.less/', 0777, true);

					}

					if (!is_dir($tmpPath . '/less')) {

						// Create the directory if it doesn't exist
						mkdir($tmpPath . '/less/', 0777, true);

					}



					// Move into the directory, the command line for less isn't friendly
					chdir($path . '/.less');



					// Get all the files we need to process
					$result = $this->processFiles($this->directoryHandler->get($path . '/.less/'));



					// Sort those files by length (!IMPORTANT if not sorted by length, you do not create directories before you process the file)
					usort($result, function ($a, $b) {

						return mb_strlen($a) - mb_strlen($b);

					});



					// Loop through each file
					foreach ($result as $row => $value) {

                        if(substr(basename($value), 0, 1) === '_') {
                            continue;
                        }



						// Define where the new compiled file is going
						$oldPath = $value;

						$newPath = str_replace('.less', '.less.css', $value);

						$sourceMapName = $newPath . '.map';

						$oldTruePath = $realDirectoyPath . '/.less/' . $oldPath;

						$newTruePath = $realDirectoyPath . '/less/' . $newPath;



                        if (file_exists($newTruePath) && !$this->checkFileChanged($oldTruePath)) {

                            continue;

                        }



						// Check that the directory exists
						$directory = dirname($newPath) . '/';



						if (!is_dir($directory)) {

							// Create the directory if it doesn't exist
							mkdir($directory, 0777, true);

						}



						// Output that we are processing that file
						echo '	Processing File (With SourceMap): ' . $value . PHP_EOL;



						// Process the file
						$output = $this->processFile('lessc --source-map=' . basename($sourceMapName) . ' --compress ' . basename($oldPath) . ' ' . basename($newPath));



						// If the output has generated an error, stop
						if ($output === false) {

							return false;

						}

					}



					// Move back up and copy the files into the viewable directory
					chdir('../');

					$this->directoryHandler->move('./.less', './less');

					break;

				default:

					// @todo: Error output
					break;

			}



			return true;

		}



		/**
		 * @param string $command
		 *
		 * @return bool
		 */

		public function processFile($command)

		{

			// Set the default returnCode
			$returnCode = 0;

			// Set the output array
			$output = [];



			// Run the command
			$line = exec($command, $output, $returnCode);
            
			// Return if the command failed
			if ($returnCode !== 0) {
                var_dump($command, $line, $output, $returnCode);
				return false;

			}



			// Everything ran fine
			return true;

		}



		/**
		 * @param array <integer,array|string> $files
		 *
		 * @return array
		 */

		public function processFiles($files)

		{



			if (!is_array($files)) {

				return [];

			}

			// Setup the array where we will store the flat paths
			$flatPaths = [];



			$skipFiles = ['.map', '.css', '.min.js'];

			// Loop through each file
			foreach ($files as $row => $value) {

				// If this is a directory
				if (is_array($value)) {



					// Process each file in the array
					$tempArray = $this->processFiles($value);



					// Add each file to our output
					foreach ($tempArray as $row2 => $value2) {

						$addToList = true;

						foreach ($skipFiles as $skipFile) {

							if (mb_strpos($value2, $skipFile) !== false) {

								$addToList = false;

							}

						}

						if ($addToList) {

							$flatPaths[] = $row . '/' . $value2;

						}

					}

				} else {

					// This is a file, we can just add it to the output
					$addToList = true;

					foreach ($skipFiles as $skipFile) {

						if (mb_strpos($value, $skipFile) !== false) {

							$addToList = false;

						}

					}

					if ($addToList) {

						$flatPaths[] = $value;

					}

				}

			}



			// Return our flat paths
			return $flatPaths;

		}

	}
