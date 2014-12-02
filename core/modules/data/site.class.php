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


            if(\HC\Site::checkProductionAccess()) {
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

            // Shutdown handling
            if (defined('REGISTER_SHUTDOWN')) {

                if (REGISTER_SHUTDOWN) {
                    register_shutdown_function('HC\Site::shutDown');
                }
            }

            if(PHP_SAPI !== 'cli') {
                http_response_code(200);
                ob_start();
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
                        set_error_handler('HC\Error::errorHandler', -1);
                        set_exception_handler('HC\Error::exceptionHandler');

                        return true;



                    case 'FATAL':

                        error_reporting(E_ERROR | E_PARSE);
                        set_error_handler('HC\Error::errorHandler', -1);
                        set_exception_handler('HC\Error::exceptionHandler');


                        return true;
                    
                    case 'NONE':

                        error_reporting(0);
                        set_error_handler('HC\Error::errorHandler', -1);
                        set_exception_handler('HC\Error::exceptionHandler');


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
            foreach ($GLOBALS as $key => $value) {

                // If it's an object
                if (is_object($value)) {
                    // Only destruct the page class - or anything that extends it
                    if (self::extendsHydraCoreClass($value, 'Page')) {

                        // Call the destructor
                        $GLOBALS[$key] = null;
                        unset($GLOBALS[$key]);

                    }
                }

            }

            
            if(function_exists('getallheaders')) {
                if(http_response_code() === 200) {
                    $headers = \getallheaders();
                    $contents = ob_get_contents();
                    if($contents) {
                        $md5 = md5($contents);
                        if(isset($headers['If-None-Match'])) {
                            if($headers['If-None-Match'] === $md5) {
                                ob_end_clean();
                                header('HTTP/1.1 304 Not Modified');
                            }
                        }

                        header('Pragma: public', true);
                        header('Content-Length: ' . strlen($contents), true);
                        header('ETag: '. $md5, true);
                        header_remove('cache-control');
                        header_remove('expires');
                        header_remove('last-modified');
                    }
                }
            }

            $GLOBALS['skipShutdown'] = true;

            register_postsend_function('HC\Site::postSend');

            return true;

        }

        /**
         * @return bool
         */

        public static function postSend() {


            if (isset($GLOBALS['skipPostSend'])) {

                if ($GLOBALS['skipPostSend']) {

                    return false;

                }

            }

            $endTime = microtime(true);

            if(function_exists('getallheaders')) {
                $headers = \getallheaders();
                if(!isset($headers['X-Hc-Skip-App-Stats'])) {
                    $siteObject = $GLOBALS['HC_CORE']->getSite();
                    $timeCPUBound = $siteObject->getTimeCPUBound();
                    $queries = $siteObject->getNumberOfQueries();

                    $startTime = $siteObject->getStartTime();
                    $responseTime = ($endTime - $startTime);

                    if(apc_exists('HC_APP_STATS_REQUESTS') && apc_exists('HC_APP_STATS_TIME') && apc_exists('HC_APP_STATS_TIME_CPUBOUND') && apc_exists('HC_APP_STATS_QPM') && apc_exists('HC_APP_STATS_TIMECODE')) {
                        $timecode = apc_fetch('HC_APP_STATS_TIMECODE');
                        $requests = apc_fetch('HC_APP_STATS_REQUESTS');
                        $avgRespTime = apc_fetch('HC_APP_STATS_TIME');
                        $oldQueries = apc_fetch('HC_APP_STATS_QPM');
                        $avgTimeCPUBound = apc_fetch('HC_APP_STATS_TIME_CPUBOUND');
                        $expire = (time() - 60);
                        if($timecode > $expire) {
                            // In range

                            // Calculate new average
                            $newAvgRespTime = (($avgRespTime * $requests) + $responseTime) / ($requests + 1);
                            $newAvgTimeCPUBound = (($avgTimeCPUBound * $requests) + $timeCPUBound) / ($requests + 1);

                            // Update the values with new averages / counts
                            apc_store('HC_APP_STATS_TIME', $newAvgRespTime);
                            apc_store('HC_APP_STATS_TIME_CPUBOUND', $newAvgTimeCPUBound);
                            apc_store('HC_APP_STATS_REQUESTS', ($requests + 1));
                            apc_store('HC_APP_STATS_QPM', ($queries + $oldQueries));
                        } else {
                            // Out of range

                            // Store the values as last increment
                            apc_store('HC_APP_STATS_REQUESTS_LAST', $requests);
                            apc_store('HC_APP_STATS_TIME_LAST', $avgRespTime);
                            apc_store('HC_APP_STATS_TIME_CPUBOUND_LAST', $avgTimeCPUBound);
                            apc_store('HC_APP_STATS_QPM_LAST', $oldQueries);
                            apc_store('HC_APP_STATS_TIMECODE_LAST', time());

                            // Start values again
                            apc_store('HC_APP_STATS_REQUESTS', 1);
                            apc_store('HC_APP_STATS_TIME', $responseTime);
                            apc_store('HC_APP_STATS_TIME_CPUBOUND', $timeCPUBound);
                            apc_store('HC_APP_STATS_QPM', $queries);
                            apc_store('HC_APP_STATS_TIMECODE', time());
                        }
                    } else {
                        apc_store('HC_APP_STATS_REQUESTS', 1);
                        apc_store('HC_APP_STATS_TIME', $responseTime);
                        apc_store('HC_APP_STATS_TIME_CPUBOUND', $timeCPUBound);
                        apc_store('HC_APP_STATS_QPM', $queries);
                        apc_store('HC_APP_STATS_TIMECODE', time());
                    }
                }
            }
            
            $GLOBALS['skipPostSend'] = true;

            // Loop through all globals
            foreach ($GLOBALS as $key => $value) {

                // If it's an object
                if (is_object($value)) {
                    if(get_class($value) !== 'HC\\Site') {
                        // Only destruct hydracore objects - or objects that extend hydracore
                        if (self::extendsHydraCore($value)) {
                            // Call the destructor
                            $GLOBALS[$key] = null;
                            unset($GLOBALS[$key]);
                        }
                    }
                }

            }

            return true;

        }

        public static function checkProductionAccess() {
            if(ENVIRONMENT === 'PRODUCTION') {
                if(isset($_SESSION) && isset($_SESSION['HC_USER_HAS_PRODUCTION_ACCESS'])) {
                    return true;
                }
                
                if(function_exists('getallheaders') && isset($GLOBALS['HC_CORE'])) {
                    $globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();
                    if(isset($globalSettings['monitor-client']) && isset($globalSettings['monitor-client']['key'])) {
                        $headers = \getallheaders();
                        if(isset($headers['X-Hc-Auth-Code'])) {
                            $authenticator = new \HC\Authenticator();
                            $authenticator->setCodeLength(9);
                            if($authenticator->verifyCode($globalSettings['monitor-client']['key'], $headers['X-Hc-Auth-Code'])) {
                                return true;
                            }
                        }
                    }
                    return false;
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
