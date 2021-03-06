<?hh // decl


	namespace HC;



	/**
	 * Class Site
	 *
	 * This class defines the site object based on settings provided, loading other classes that are needed.
	 */

	class Site extends Core

	{

		// Setup class public variables


		// Setup class protected variables
		/**
		 * @var array
		 */

		protected $settings = [

			'database' => [],

			'users' => [

				'salt' => 'USER_SALT'

			],

			'compilation' => [

				'languages' => [

					'js' => false,

                    'jsx' => false,

					'scss' => false,

					'less' => false

				],

				'path' => '/resources/'

			],

			'email' => [

				'mailSystem' => 'default', // MailGun - SendGrid - default
				'sendGridUser' => false,

				'sendGridPass' => false,

				'emailType' => 'html',

				'defaults' => [

					'sentFromAddress' => 'example@hydracore.io',

					'sentFromName' => 'HydraCore'

				]

			],

			'pages' => [

				'views' => [],

				'resources' => [],

				'cacheViews' => false,

				'authentication' => false

			],

			'encryption' => [],

			'cache' => [],

			'errors' => [

				// If any of the values in this array are contained within the error message, it will be ignored
				'ignore' => [



				]

			],

		];



		/**
		 * @var float|string
		 */

        protected $startTime = 0;
        protected $rUsage = 0;
        protected $nonCPUBoundTime = 0;
        protected $numberOfQueries = 0;
				protected $numberOfSelects = 0;
        protected $numberOfCacheHits = 0;
        protected $sleepTime = 0;



		// Setup Constructor


		/**
		 * @param array $settings
		 */

		public function __construct(&$settings = [])

		{

			$this->settings = $settings = $this->parseOptions($settings, $this->settings);

            mb_internal_encoding(ENCODING);
            mb_http_output();
            if(PHP_SAPI != 'cli') {
                header('Content-Type: text/html; charset=' . ENCODING);
            }

            date_default_timezone_set(TIMEZONE);

			if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				$locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
			} else {
				$locale = 'en-GB';
			}

			\Locale::setDefault($locale);

			// Setup Page Timer
			if(isset($_SERVER['REQUEST_TIME_FLOAT'])) {
				$this->startTime = $_SERVER['REQUEST_TIME_FLOAT'];
			} else {
				$this->startTime = microtime(true);
			}


            if(ENVIRONMENT !== 'PRODUCTION') {
                // Setup the data required to get cpu usage
                $data = getrusage();
                $this->rUsage = $data["ru_utime.tv_sec"]*1e6+$data["ru_utime.tv_usec"];
                $this->startMemoryUsage = $this->getCurrentMemoryUsage(false);
                $this->startMemoryUsageReal = $this->getCurrentMemoryUsage(true);
            }

			// Autoloading
			spl_autoload_register('HC\Core::autoLoader');



			// Error Handling
			$this->errorReporting();

			set_error_handler('HC\Error::errorHandler', -1);

			set_exception_handler('HC\Error::exceptionHandler');

            // Shutdown handling
            if (defined('REGISTER_SHUTDOWN')) {

                if (REGISTER_SHUTDOWN) {
                    register_shutdown_function('HC\Site::shutDown');
                }
            }

			return true;

		}





		public function __destruct()

		{

			$this->settings = null;
			$this->startTime = null;
            restore_error_handler();
            restore_exception_handler();

		}



		/**
		 * @return array
		 */

		public function getSettings()

		{



			// Return the settings used to create the site object
			return $this->settings;

		}



		/**
		 * @return float|string
		 */

		public function getStartTime()

		{



			// Return the time the site was started
			return $this->startTime;

		}


		/**
		 * @return bool
		 */

		public static function errorReporting()

		{

			if (defined('ERROR_LOGGING')) {

				switch (ERROR_LOGGING) {

					case 'ALL':

						error_reporting(E_ALL);

						return true;



					case 'FATAL':

						error_reporting(E_ERROR | E_PARSE);



						return true;



					case 'NONE':

						error_reporting(0);



						return true;

				}

			}



			return false;

		}

		/**
		 * @return bool
		 */

		public static function shutDown() {


            if (isset($GLOBALS['skipShutdown'])) {

                if ($GLOBALS['skipShutdown']) {

                    return false;

                }

            }



            // Loop through all globals
            foreach ($GLOBALS as &$global) {



                // If it's an object
                if (is_object($global)) {

                    // Only destruct hydracore objects - or objects that extend hydracore
                    if ((mb_strpos(get_class($global), 'HC\\') !== false) || (self::extendsHydraCore($global))) {

                        if (method_exists($global, '__destruct')) {

                            // Call the destructor
                            $global->__destruct();

                        }

                    }

                }

            }

			return true;

		}



		public static function extendsHydraCore($class) {

			$parentClass = get_parent_class($class);

			if($parentClass) {

				if(mb_strpos($parentClass, 'HC\\') !== false) {

					return true;

				} else {

					return self::extendsHydraCore($parentClass);

				}

			}

			return false;

		}



		public static function extendsHydraCoreClass($class, $desiredClass) {

			$parentClass = get_parent_class($class);

			if($parentClass) {

				if(mb_strpos($parentClass, 'HC\\' . $desiredClass) !== false) {

					return true;

				} else {

					return self::extendsHydraCoreClass($parentClass, $desiredClass);

				}

			}

			return false;

		}

		/**
		 * @return string
		 */

		public static function getLinuxDistro()

		{

			if (PHP_OS != 'Linux') {

				return '';

			}

			// Define what we know of the distributions
			$distros = [

				'Arch' => 'arch-release',

				'Debian' => 'debian_version',

				'Fedora' => 'fedora-release',

				'Redhat' => 'redhat-release',

				'CentOS' => 'centos-release',

				'Ubuntu' => 'lsb-release'

			];



			// Scan etc
			$etcList = array_reverse(scandir('/etc'));



			//Loop through /etc results
			$OSDistro = '';

			foreach ($etcList as $file) {

				//Loop through list of distributions
				foreach ($distros as $distroReleaseFile) {

					//Match was found.
					if ($distroReleaseFile === $file) {

						// Find distribution
						$OSDistro = array_search($distroReleaseFile, $distros);

						break 2;

					}

				}

			}



			return $OSDistro;

		}

        public function getServerMemoryLimit($pretty = false) {
            $memory = $this->getScriptMemoryLimit(false, false);

            if (PHP_OS === 'Linux') {
                $fh = fopen('/proc/meminfo','r');
                while ($line = fgets($fh)) {
                    $pieces = array();
                    if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                        $memory = $pieces[1] * 1000;
                        break;
                    }
                }
                fclose($fh);
            }

            if($pretty) {
                return $this->formatBytes($memory);
            } else {
                return $memory;
            }
        }

        public function getTimeCPUBound($pretty = false) {
						$currentTime = microtime(true);
						$timeSpent = $currentTime - $this->startTime;

            $bound = ($timeSpent - $this->nonCPUBoundTime) / $timeSpent * 100;

            if($pretty) {
                return floor($bound);
            } else {
                return (float)$bound;
            }
        }

        public function addNonCPUBoundTime($microseconds) {
            $this->nonCPUBoundTime += $microseconds;
            return $this->nonCPUBoundTime;
        }

        public function addNumberOfQueries($queries) {
            $this->numberOfQueries += $queries;
            return $this->numberOfQueries;
        }

				public function addNumberOfSelects($selects) {
					$this->numberOfSelects += $selects;
					return $this->numberOfSelects;
				}

        public function addNumberOfCacheHits($hits) {
            $this->numberOfCacheHits += $hits;
            return $this->numberOfCacheHits;
        }

        public function getNumberOfQueries() {
            return $this->numberOfQueries;
        }

				public function getNumberOfNonSelects() {
					return $this->numberOfQueries - $this->numberOfSelects;
				}

				public function getNumberOfSelects() {
						return $this->numberOfSelects;
				}

				public function getCacheEfficiency($pretty = false) {

					if($this->numberOfCacheHits === 0 || $this->numberOfSelects === 0) {
						$efficiency = 0;
					} else {
						$efficiency = $this->numberOfCacheHits / $this->numberOfSelects * 100;
					}

					if($pretty) {
							return floor($efficiency);
					} else {
							return (float)$efficiency;
					}
				}

        public function getNumberOfCacheHits($pretty = false) {
            if($this->numberOfQueries) {
                if($pretty) {
                    $cacheHits = 100 - (($this->numberOfQueries - $this->numberOfCacheHits) / $this->numberOfQueries * 100);

                    if($cacheHits > 1) {
                        $cacheHits = floor($cacheHits);
                    }

                    if($cacheHits === 0) {
                        return 0;
                    } else {
                        return (float)$cacheHits;
                    }
                } else {
                    return $this->numberOfCacheHits;
                }
            }

            return 0;
        }

        public function getCPUUsage($pretty = false) {
            $data = getrusage();
            $data["ru_utime.tv_usec"] = ($data["ru_utime.tv_sec"]*1e6 + $data["ru_utime.tv_usec"]) - $this->rUsage;
            $time = (microtime(true) - $this->startTime) * 1000000;
            $cpu = 0;

            if($time > 0) {
                $cpu = sprintf("%01.2f", ($data["ru_utime.tv_usec"] / $time) * 100);
                if($cpu > 100) {
                    $cpu = 100;
                }
            }

            if($pretty) {
                if($cpu > 1) {
                    $cpu = floor($cpu);
                }
                return str_pad($cpu, 2);
            } else {
                return (float)$cpu;
            }
        }

        public function getTotalCPUUsage($pretty = false) {
            $data = sys_getloadavg();

            if($pretty) {
                if($data[0] > 1) {
                    $data[0] = floor($data[0]);
                }

                return str_pad((float)$data[0], 2);
            } else {
                return (float)$data[0];
            }
        }

        public function getCurrentMemoryUsage($real = true, $pretty = false) {
            if($pretty) {
                return $this->formatBytes(memory_get_usage($real));
            }

            return memory_get_usage($real);
        }

        public function getPeakMemoryUsage($real = true, $pretty = false) {
            if($pretty) {
                return $this->formatBytes(memory_get_peak_usage($real));
            }

            return memory_get_peak_usage($real);
        }

        public function getScriptMemoryLimit($real = true, $pretty = false) {
            $limit = $this->returnBytes(ini_get('memory_limit'));

            if($real) {
                $serverLimit = $this->getServerMemoryLimit();
                if($limit > $serverLimit) {
                    $limit = $serverLimit;
                }
            }

            if($pretty) {
                return $this->formatBytes($limit);
            } else {
                return $limit;
            }
        }

        public function getStartMemoryUsage($real = true, $pretty = false) {
            if($real) {
                return $this->formatBytes($this->startMemoryUsageReal);
            } else {
                return $this->formatBytes($this->startMemoryUsage);
            }
        }

        private function returnBytes($val) {
            $val = trim($val);
            $last = mb_strtolower($val[mb_strlen($val)-1]);
            switch($last) {
                case 'g':
                    $val *= 1024;
                case 'm':
                    $val *= 1024;
                case 'k':
                    $val *= 1024;
            }

            return $val;
        }

        private function formatBytes($bytes, $precision = 1) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');

            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= (1 << (10 * $pow));

            if($pow < 2) {
                $precision = 0;
            }

            return round($bytes, $precision) . ' ' . $units[$pow];
        }

	}
