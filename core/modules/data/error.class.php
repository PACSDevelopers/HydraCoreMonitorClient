<?hh // decl
	namespace HC;

    /**
     * Class Error
     * @package HC
     */

    class Error extends Core

    {

        // Setup class public variables


        // Setup class protected variables
        protected $errorTitle = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            422 => 'Unprocessable Entity (WebDAV)',
            423 => 'Locked (WebDAV)',
            424 => 'Failed Dependency (WebDAV)',
            425 => 'Unordered Collection',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            444 => 'No Response',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            499 => 'Client Closed Request',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage (WebDAV)',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
            '503-2' => 'Deployment In Progress'
        ];
        // Setup the corresponding descriptions
        protected $errorDescription = [
            400 => 'The request cannot be fulfilled due to bad syntax.',
            401 => 'Similar to 403 Forbidden, but specifically for use when authentication is possible but has failed or not yet been provided.',
            403 => 'The request was a legal request, but the server is refusing to respond to it.',
            404 => 'The requested resource could not be found but may be available again in the future.',
            405 => 'A request was made of a resource using a request method not supported by that resource.',
            406 => 'The requested resource is only capable of generating content not acceptable according to the Accept headers sent in the request.',
            408 => 'The server timed out waiting for the request.',
            409 => 'Indicates that the request could not be processed because of conflict in the request, such as an edit conflict.',
            410 => 'Indicates that the resource requested is no longer available and will not be available again.',
            411 => 'The request did not specify the length of its content, which is required by the requested resource.',
            412 => 'The server does not meet one of the preconditions that the requester put on the request.',
            413 => 'The request is larger than the server is willing or able to process.',
            414 => 'The URI provided was too long for the server to process.',
            415 => 'The request entity has a media type which the server or resource does not support.',
            416 => 'The client has asked for a portion of the file, but the server cannot supply that portion.',
            417 => 'The server cannot meet the requirements of the Expect request-header field.',
            422 => 'The request was well-formed but was unable to be followed due to semantic errors.',
            423 => 'The resource that is being accessed is locked.',
            424 => 'The request failed due to failure of a previous request.',
            425 => 'The client should switch to a different protocol such as TLS/1.0.',
            428 => 'The origin server requires the request to be conditional.',
            429 => 'The user has sent too many requests in a given amount of time.',
            431 => 'The server is unwilling to process the request because either an individual header field, or all the header fields collectively, are too large.',
            444 => 'The server returns no information to the client and closes the connection.',
            449 => 'The request should be retried after performing the appropriate action.',
            450=> 'This error is given when Windows Parental Controls are turned on and are blocking access to the given webpage.',
            499 => 'This code is introduced to log the case when the connection is closed by client while HTTP server is processing its request, making server unable to send the HTTP header back.',
            500 => 'A generic error message, given when no more specific message is suitable.',
            501 => 'The server either does not recognise the request method, or it lacks the ability to fulfill the request.',
            502 => 'The server was acting as a gateway or proxy and received an invalid response from the upstream server.',
            503 => 'The server is currently unavailable (because it is overloaded or down for maintenance).',
            504 => 'The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.',
            505 => 'The server does not support the HTTP protocol version used in the request.',
            506 => 'Transparent content negotiation for the request results in a circular reference.',
            507 => 'The server is unable to store the representation needed to complete the request.',
            509 => 'The server\'s bandwidth limit was exceeded.',
            510 => 'Further extensions to the request are required for the server to fulfill it.',
            511 => 'The client needs to authenticate to gain network access.',
            '503-2' => 'The server is currently unavailable (because it is down for a deployment).',
        ];

        protected $errorHeaders = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended',
            '503-2' => 'Deployment In Progress',
        ];

        /**
         * Constructor
         *
         */

        public function __construct()

        {

        }



        /**
         * Getting backtrace
         *
         * @param string $endLine
         * @param int $skip
         *
         * @return string
         */

        public static function getBacktrace($traceArray, $endLine, $skip = 0)

        {

            if (!is_array($traceArray)) {

                return '';

            }



            if (!is_string($endLine)) {

                return '';

            }



            if (!is_int($skip)) {

                return '';

            }



            // Define trace
            $trace = '';



            // Get the debug trace, reversed and without the last $skip calls
            $traceArray = array_slice($traceArray, $skip);



            // Loop through the trace
            foreach ($traceArray as $key => $value) {
                $key++;

                // Define the row based on key
                $row = '[' . ($key) . ']';



                // If we know the file, display
                if (isset($value['file'])) {

                    $row .= ' ' . $value['file'];

                }



                // If we know the line, display
                if (isset($value['line'])) {

                    $row .= ':' . $value['line'];

                }



                // If we know the class, display
                if (isset($value['class'])) {

                    $row .= ' ' . $value['class'] . '::';



                    // If we know the function, display without a space as it's within a class
                    if (isset($value['function'])) {

                        $row .= $value['function'];

                    }

                } else {



                    // If we know the function, display with a space as it's not within a class
                    if (isset($value['function'])) {

                        $row .= ' ' . $value['function'];

                    }

                }



                // If we know the arguments
                if (isset($value['args'])) {

                    // Start the argument row
                    $row .= '(';



                    // Figure out how many arguments we have
                    $count = count($value['args']);



                    $redactedValues = ['salt', 'password', 'sendGridPass'];

                    // Loop through each argument
                    foreach ($value['args'] as $argKey => $argValue) {

                        // Check for recursion
                        if (Error::isRecursive($argValue)) {

                            // Give it recursive value
                            $row .= '*RECURSION*';

                        } else {

                            // Get the type appropriate string
                            $exportedValue = var_export($argValue, true);

                            foreach($redactedValues as $value) {

                                if(mb_strpos($exportedValue, $value) !== false) {

                                    $exportedValue = '*REDACTED*';

                                    break;

                                }

                            }



                            $row .= $exportedValue;



                        }

                        // If not the last argument, append the separator
                        if ($count !== ($argKey + 1)) {

                            $row .= ', ';

                        }

                    }



                    // End the row
                    $row .= ')';

                }



                // Append to the trace, encoding html if appropriate
                if ($endLine == '<br>') {

                    $trace .= <x:frag>{$row}</x:frag> . $endLine . $endLine;

				} else {

                    $trace .= $row . $endLine . $endLine;

                }



            }



            return $trace;

        }

        protected static function isErrorOfErrorSystem($trace) {
            foreach($trace as $key => $row) {
                if(isset($row['file'])) {
                    if(mb_strpos($row['file'], 'error.class.php')) {
                        return true;
                    }
                }
            }
            return false;
        }

        /**
         * @param $errno
         * @param $errstr
         * @param $errfile
         * @param $errline
         * @param int $skipTrace
         * @param array $traceArray
         * @param bool $isException
         * @return false|null
         */

        public static function errorHandler($errno = 1, $errstr = '?', $errfile = '?', $errline = '?', $skipTrace = 0, $traceArray = [], $isException = false, $customError = false)

        {
            if (ERROR_LOGGING === 'NONE' || error_reporting() === 0) {
                return true;
            }

            // Get site settings
            if(isset($GLOBALS['HC_CORE'])) {

                if(method_exists($GLOBALS['HC_CORE'], 'getSite')) {

                    $site = $GLOBALS['HC_CORE']->getSite();

                    if($site instanceof \HC\Site) {

                        $siteSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

                        if(isset($siteSettings)) {

                            if (isset($siteSettings['errors'])) {

                                if (isset($siteSettings['errors']['ignore'])) {

                                    if (is_array($siteSettings['errors']['ignore'])) {

                                        foreach ($siteSettings['errors']['ignore'] as $ignore) {

                                            // // Check for ignored errors
                                            if (mb_strpos($errstr, $ignore) !== false) {

                                                return true;

                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }







            // Force number from $skipTrace
            if (!is_int($skipTrace)) {

                $skipTrace = 1;

            }



            // Make sure we skip shutdown, so no pages are rendered after this
            $GLOBALS['skipShutdown'] = true;



            // Define the output and wrapper
            $output = '';

            // Define the string to use at the end of every line
            $endLine = (PHP_SAPI == 'cli') ? PHP_EOL : '<br>';



            // Define the version of PHP / HHVM
            $phpVersion = (defined('HHVM_VERSION')) ? PHP_VERSION . ' (HHVM: ' . HHVM_VERSION . ')' : PHP_VERSION;

            // Define operating system
            $operatingSystem = PHP_OS;
            $documentMode = 0;

            $isErrorOfErrorSystem = self::isErrorOfErrorSystem($traceArray);

            if($endLine === '<br>' && !$isErrorOfErrorSystem) {
                $documentMode = 1;
            }

            if ($operatingSystem === 'Linux') {

                $operatingSystem = Site::getLinuxDistro() . ' ' . $operatingSystem;

            }

            // Setup details
            $details = $endLine . $endLine . 'Timestamp: ' . time() . $endLine . 'HydraCore Version: ' . HC_VERSION .

                $endLine . 'PHP Version: ' . $phpVersion . $endLine . 'OS: ' . $operatingSystem . $endLine;


            $output .= self::friendlyErrorType($errno) . $endLine;

            if($customError) {
                $output .= '[' . $errno . '] ' . $errstr . $endLine . $customError . ' on line ';
            } else {
                if ($isException) {

                    $output .= '[' . $errno . '] ' . $errstr . $endLine . 'Uncaught Exception on line ';

                } else {

                    $output .= '[' . $errno . '] ' . $errstr . $endLine . ' on line ';

                }
            }



            $output .= $errline . ' of ' . $errfile . $details;



            // Define default trace
            $trace = '';



            // If we have a defined stack trace
            if (empty($traceArray)) {
                // Get the formatted trace string based on the debug backtrace
                $traceArray = debug_backtrace();
            }

            if ($traceArray) {
                $traceFirstLine =  self::getErrorLine($errfile, $errline, $endLine);
                $trace = $traceFirstLine . Error::getBacktrace($traceArray, $endLine, $skipTrace);
            }

            // Add the trace
            $output .= $endLine . $trace;



            // If we know the environment
            if (defined('ENVIRONMENT')) {

                // And it's a production environment
                if (ENVIRONMENT === 'PRODUCTION') {

                    // Encrypt the output
                    $encryption = new Encryption();

                    $data = $encryption->encrypt($output, 'HC_ERROR');

                    // If could be encrypted
                    if ($data) {

                        // Format it
                        $data = chunk_split($data, 50, $endLine);

                        // Add it to output
                        $output = 'Error, information below: ' . $endLine . $endLine . $data;

                    }

                }

            }

            // Clean all previous input
            if (ob_get_length()) {

                ob_clean();

            }

            $GLOBALS['skipShutdown'] = true;

            // Write to the error log
            error_log($output);

            // Close off the html if needed
            if ($documentMode === 1) {
                try {
                    // Display output with page
                    $error = new \HC\Error();
                    $error->generateErrorPage(500, $output);
                } catch (\Exception $exception) {
                    Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), 0, $exception->getTrace(), true);
                }
            } else {
                echo $output;
            }

            return true;
        }

        protected static function getErrorLine($file, $line, $endLine) {
            if(file_exists($file)) {
                $fileLines = file($file);
                return '[0] ' . $file . ':' . $line . ' ' . $fileLines[$line - 1] . $endLine . $endLine;
            } else {
                return '[0] ' . $file . ':' . $line . ' ' . $endLine . $endLine;
            }
        }

        protected static function friendlyErrorType($type)
        {
            $return = '';
            if($type & E_ERROR) // 1 //
                $return.='& HC_Error ';
            if($type & E_WARNING) // 2 //
                $return.='& HC_Warning ';
            if($type & E_PARSE) // 4 //
                $return.='& HC_Parse ';
            if($type & E_NOTICE) // 8 //
                $return.='& HC_Notice ';
            if($type & E_CORE_ERROR) // 16 //
                $return.='& HC_Core_Error ';
            if($type & E_CORE_WARNING) // 32 //
                $return.='& HC_Core_Warning ';
            if($type & E_COMPILE_ERROR) // 64 //
                $return.='& HC_Compile_Error ';
            if($type & E_COMPILE_WARNING) // 128 //
                $return.='& HC_Compile_Warning ';
            if($type & E_USER_ERROR) // 256 //
                $return.='& HC_User_Error ';
            if($type & E_USER_WARNING) // 512 //
                $return.='& HC_User_Warning ';
            if($type & E_USER_NOTICE) // 1024 //
                $return.='& HC_User_Notice ';
            if($type & E_STRICT) // 2048 //
                $return.='& HC_Strict ';
            if($type & E_RECOVERABLE_ERROR) // 4096 //
                $return.='& HC_Recoverable_Error ';
            if($type & E_DEPRECATED) // 8192 //
                $return.='& HC_Deprecated ';
            if($type & E_USER_DEPRECATED) // 16384 //
                $return.='& HC_User_Deprecated ';

            return mb_substr($return,2);
        }

        /**
         * @param $exception
         * @return bool
         */

        public static function exceptionHandler($exception, $skipTrace = 0)

        {

            // Trigger the error handler, based on exception details
            Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $skipTrace, $exception->getTrace(), true);

            return true;

        }

        public static function checkPHPSyntax($file) {

            // Check it's valid syntax
            $file = realpath($file);
            $return_var = -1;
            $output = [];
            exec('hhvm -l ' . escapeshellarg($file), $output, $return_var);

            if($return_var === 1) {
                $lastnum = 0;
                $result = implode($output);

                if(preg_match_all('/\d+/', $result, $numbers)) {
                    $lastnum = end($numbers[0]);
                }

                $result = str_replace('Fatal error: syntax error, ', '', $result);
                $result = str_replace(' in ' . $file . ' on line ' . $lastnum, '', $result);
                $result = ucfirst($result);

                Error::errorHandler(E_PARSE, $result, $file, $lastnum, 2, [], false, 'Syntax Error');
                return false;
            }

            return true;
        }


        /**
         * @param $value
         * @return bool
         */

        protected static function isRecursive($value)

        {

            $dump = print_r($value, true);

            if (mb_strpos($dump, '*RECURSION*') !== false) {

                return true;

            }



            return false;

        }

        public function generateErrorPage($code = 500, $error = '') {
            if(!isset($this->errorTitle[$code])) {
                $code = 500;
            }

            $actualCode = \strtok($code, '-');


            if(defined('MODE') && MODE === 'API') {
                $pageSettings = [
                    'views' => [
                        'body'   => true
                    ]
                ];

                $errorPage = new \HC\Ajax($pageSettings);
                $errorPage->body = ['status' => $code, 'message' => $this->errorTitle[$code], 'errorDescription' => $this->errorDescription[$code], 'errorDetail' => $error];
            } else {
                $pageSettings = [
                    'views' => [
                        'header' => [
                            'pageName' => 'Error',
                            'scss' => [
                                'main' => true
                            ],
                            'js' => [
                                'main' => true
                            ]
                        ],
                        'body'   => true,
                        'footer' => true
                    ]
                ];

                $errorPage = new \HC\Page($pageSettings);

                $errorPage->body = <div class="container">
                                        <div class="row">
                                            <h1>Error - {$actualCode} - {$this->errorTitle[$code]}</h1>
                                            <div>
                                                    <p>{$this->errorDescription[$code]}</p>
                                                    <p>{POTENTIAL_XSS_HOLE($error)}</p>
                                            </div>
                                        </div>
                                    </div>;
            }



            // Clean all previous input
            if (ob_get_length()) {
                ob_clean();
            }

            $GLOBALS['skipShutdown'] = true;

            try {
                echo $errorPage->render();
            } catch (\Exception $exception) {
                Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), 0, $exception->getTrace(), true);
            }

            $GLOBALS['skipRender'] = true;

            if(PHP_SAPI !== 'cli') {
                http_response_code($actualCode);
                if(isset($this->errorHeaders[$actualCode])) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' ' . $actualCode . ' ' . $this->errorHeaders[$actualCode], true, $actualCode);
                }
            }

            return true;
        }
    }
