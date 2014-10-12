<?hh // decl


	namespace HC;



	/**
	 * Class Process
	 * @package HC
	 */

	class Process extends Core

	{

		/**
		 * @var array
		 */

		protected $settings = [];



		protected $PID = null;



		protected $processList = [];



		// Constructor


		/**
		 * @param (int|string|null)[] $settings
		 */

		public function __construct($settings = [])

		{



			$settings = $this->parseOptions($settings, []);



			// Parse global / local settings
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['process'])) {

				if (is_array($globalSettings['process'])) {

					$settings = $this->parseOptions($settings, $globalSettings['process']);

				}

			}



			$this->settings = $settings;



			if(!is_dir(HC_TMP_LOCATION)) {

				mkdir(HC_TMP_LOCATION, 0777);

			}



			if(!is_dir(HC_TMP_LOCATION . '/processes')) {

				mkdir(HC_TMP_LOCATION . '/processes', 0777);

			}



			$constants = get_defined_constants(true);

			if($constants['user']) {

			    $constants = $constants['user'];

			} else {

			    $constants = [];

			}



			$this->updateHydraCoreSettings($globalSettings, $constants);

			$this->getList();

		}



		/**
		 *
		 */

		public function __destruct()

		{

			$this->settings = null;

		}



		public function start($name, $command, $workingDirectory = false) {

			$command = trim($command);



			// Check if we should check against the file version of process list
			if(isset($this->processList[$name])) {

				if($this->processList[$name] !== null) {

					// Get the latest list
					$this->getList();

					if(isset($this->processList[$name])) {

						// Process already running
						return false;

					}

				}

			}



			$orgWorkingDirectory = getcwd();



			if(!$workingDirectory) {

			    $workingDirectory = $orgWorkingDirectory;

			}



			$this->PID = shell_exec(sprintf('cd ' . $workingDirectory . ' && %s > ' . $workingDirectory . '/' . $name . '.log 2>&1 & echo $!', $command));



			chdir($orgWorkingDirectory);



		    if($this->PID != null) {

		    	// Check if the process is running
		    	if($this->isRunningPID($this->PID)) {

		    		// Update the process list
			    	$this->processList[$name] = [trim($this->PID), $command, $workingDirectory];

			    	$this->updateList();

			   	    return true;

		    	}



		    }



	        return(false);

		}





		public function stop($name) {

			// Get the latest list
			if($this->isRunning($name)) {

				if($this->stopPID($this->processList[$name][0])) {

					$this->processList[$name] = null;

					unset($this->processList[$name]);

					return $this->updateList();

				}

			}



			return false;

		}



		public function restart($name) {

			// Get the latest list
			if($this->isRunning($name)) {

				return $this->restartPID($this->processList[$name], $name);

			}



			return false;

		}



		public function isRunning($name) {

			// Get the latest list
			if($this->getList()) {

				if(isset($this->processList[$name])) {

					if($this->isRunningPID($this->processList[$name][0])) {

						return true;

					} else {

						$this->processList[$name] = null;

						unset($this->processList[$name]);

					}

				}

			}



			return false;

		}



		public function sendMessage($name, $port, $message, $address = 'localhost') {

			if($this->isRunning($name)) {

				$socket = \socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

				if ($socket) {

					$connection = \socket_connect($socket, $address, $port);

					if($connection) {

						$length = \mb_strlen($message);

						$sent = 0;

						while ($sent < $length) {

				        $sent = \socket_write($socket, $message, $length);

				        if ($sent === false) {

				            break;

				        }



				        if ($sent < $length) {

				            $message = \mb_substr($message, $sent);

				            $length -= $sent;

				        } else {

				            break;

				        }

				    }



						\socket_shutdown($socket);

						\socket_close($socket);

						return true;

					}

				}

			}



			return false;

		}



		private function stopPID($processID) {

			if($this->isRunningPID($processID)) {

				shell_exec('kill ' . $processID);

				if($this->isRunningPID($processID)) {

				    sleep(1);

				    $result = shell_exec('kill -9' . $processID);

				    if ($result === null) {

    				    return true;

    				}

				}

			}



			return false;

		}



		private function restartPID($process, $name) {

			if($this->isRunningPID($process[0])) {

				if($this->stopPID($process[0])) {

					$this->processList[$name] = null;

					$this->updateList();

					if($this->start($name, $process[1], $process[2])) {

						return true;

					}

				}

			}



			return false;

		}



		private function isRunningPID($processID) {

			exec(sprintf('ps %d', $processID), $result);

	        if(count($result) >= 2) {

	            return true;

	        }

	        return false;

		}



		private function updateHydraCoreSettings($settings, $constants) {

		    $settings['constants'] = $constants;



			// Update HydraCore settings
			$fp = fopen(HC_TMP_LOCATION . '/processes/settings.json', 'w+');

			if($fp) {

				fwrite($fp, json_encode($settings));

				fclose($fp);

				return true;

			}



			return false;

		}



		private function updateList() {

			// Write the newest process list
			$fp = fopen(HC_TMP_LOCATION . '/processes/processes.json', 'w');



			if($fp) {

				fwrite($fp, json_encode($this->processList));

				fclose($fp);

				return true;

			}



			return false;

		}



		private function getList() {

			// Get the active process $processList
			if(is_file(HC_TMP_LOCATION . '/processes/processes.json')) {

				$fp = fopen(HC_TMP_LOCATION . '/processes/processes.json', 'r');

				if($fp) {

					$result = '';

					while (!feof($fp)) {

					  $result .= fread($fp, 8192);

					}



					fclose($fp);



					if($result) {

						$result = json_decode($result);

						if($result) {

							return $this->validateList((array)$result);

						}

					}

				}

			}



			return false;

		}



		private function validateList($list) {

			if(is_array($list)) {

				$orgList = $list;

				foreach($list as $key => $value) {

					if(!$this->isRunningPID($value[0])) {

						$list[$key] = null;

						unset($list[$key]);

					}

				}



				// Update the list
				$this->processList = $list;

				if($orgList != $list) {

					// Update the file with the validated list
					$this->updateList();

				}



				return $list;

			}



			return false;

		}



	}

