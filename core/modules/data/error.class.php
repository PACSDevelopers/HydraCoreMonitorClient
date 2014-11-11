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
        static public $errorTitle = [
            0   => 'Failure',
            1   => 'Success',
            200 => 'Success',
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
            '503-2' => 'The server is currently unavailable during a deployment.',
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

        public static function getBacktrace($traceArray, $skip = 0)

        {

            // Define trace
            $trace = [];
            
            if (!is_array($traceArray) || !is_int($skip)) {
                return $trace;
            }
            
            // Get the debug trace, reversed and without the last $skip calls
            $traceArray = array_slice($traceArray, $skip);
            
            // Loop through the trace
            foreach ($traceArray as $key => $value) {
                $key++;

                $row = '';

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
                    
                    $redactedValues = ['Salt', 'salt', 'pass', 'Pass', 'password', 'Password', 'key', 'Key'];
                    
                    // Loop through each argument
                    foreach ($value['args'] as $argKey => $argValue) {

                        // Check for recursion
                        if (Error::isRecursive($argValue)) {

                            // Give it recursive value
                            $row .= '*RECURSION*';

                        } else {

                            // Get the type appropriate string
                            $exportedValue = str_replace('),' . PHP_EOL, ')' . PHP_EOL, var_export($argValue, true));

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



                // Append to the trace
                $trace[] = $row . ';';

            }
            
            return $trace;

        }
        
        protected static function protectArray($array) {
            $redactedKeys = ['Salt', 'salt', 'pass', 'Pass', 'password', 'Password', 'key', 'Key'];
            
            foreach($array as $key => $value) {
                foreach($redactedKeys as $redactedKey) {
                    if(mb_strpos($key, $redactedKey) !== false) {
                        $array[$key] = '*REDACTED*';
                        break;
                    }
                }
                
                if(is_array($value)) {
                    $array[$key] = $value = self::protectArray($value);
                }
            }
            return $array;
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
            $errorTypeInteger = self::friendlyErrorTypeInt($errno);
            
            if(($errorTypeInteger !== E_ERROR && $errorTypeInteger !== E_PARSE) && !$isException) {
                if (ALLOW_ERRORS || error_reporting() === 0) {
                    return true;
                }
            }

            // Force number from $skipTrace
            if (!is_int($skipTrace)) {

                $skipTrace = 1;

            }



            // Make sure we skip shutdown, so no pages are rendered after this
            $GLOBALS['skipShutdown'] = true;


            // Determinte if we need to render html, or text
            $isCLI = (PHP_SAPI == 'cli');
            
            $isErrorOfErrorSystem = self::isErrorOfErrorSystem($traceArray);
            
            if($isErrorOfErrorSystem) {
                $isCLI = true;
            }
            
            $errorDesc = self::friendlyErrorType($errno);
            
            if($customError) {
                $errorDesc .= '[' . $errno . '] ' . $customError . ', ' . $errstr . ' on line ';
            } else {
                if ($isException) {
                    $errorDesc .= 'Uncaught Exception with message "' . $errstr . '" on line ';

                } else {
                    $errorDesc .= '[' . $errno . '] ' . $errstr . ' on line ';
                }
            }


            $errorDesc .= $errline . ' of ' . $errfile;

            $errorDetails = [];
            
            $errorDetails['Description'] = $errorDesc;
            
            $errorDetails['ID'] = crc32(var_export(func_get_args(), true));
            
            if(isset($_SERVER['REQUEST_URI'])) {
                $errorDetails['URL'] = PROTOCOL . '://' . SITE_DOMAIN . $_SERVER['REQUEST_URI'];
            } else {
                $errorDetails['URL'] = PROTOCOL . '://' . SITE_DOMAIN;
            }
            
            $errorDetails['Timestamp'] = time();
            
            $errorDetails['HydraCore Version'] = HC_VERSION;
            
            $errorDetails['Application Version'] = APP_VERSION;
            
            $errorDetails['PHP Version'] = defined('HHVM_VERSION') ? PHP_VERSION . ' (HHVM: ' . HHVM_VERSION . ')' : PHP_VERSION;
            
            if(PHP_OS === 'Linux') {
                $errorDetails['Operating System'] = Site::getLinuxDistro() . ' Linux';
            } else {
                $errorDetails['Operating System'] = PHP_OS;
            }

            $errorDetails['Trace'] = [];
            
            // If we have a defined stack trace
            if (empty($traceArray)) {
                // Get the formatted trace string based on the debug backtrace
                $traceArray = debug_backtrace();
            }

            if ($traceArray) {
                $errorDetails['Trace'] = Error::getBacktrace($traceArray, $skipTrace);

                $traceFirstLine = self::getErrorLine($errfile, $errline);
                array_unshift($errorDetails['Trace'], $traceFirstLine);
            }
            
            if(isset($_SESSION)) {
                $safeSession = self::protectArray($_SESSION);
                
                foreach($safeSession as $key => $value) {
                    if(is_array($value)) {
                        $safeSession[$key] = json_encode($value, JSON_PRETTY_PRINT);
                    }
                }
                
                $errorDetails['Session'] = $safeSession;
            }

            if((ERROR_LOGGING === 'ALL') || (ERROR_LOGGING === 'FATAL' && ($errorTypeInteger === E_ERROR || $errorTypeInteger === E_PARSE))) {
                $logFile = ini_get('hhvm.log.file');
                if(!$logFile || $logFile == '') {
                    $logFile = '/var/log/hhvm/error.log';
                }

                if(is_file($logFile)) {
                    if(is_writable($logFile)) {
                        file_put_contents($logFile, json_encode($errorDetails) . PHP_EOL, \FILE_APPEND);
                    }
                } else {
                    file_put_contents($logFile, json_encode($errorDetails) . PHP_EOL, \FILE_APPEND);
                }
            }
            
            if(ENVIRONMENT !== 'DEV' && ERROR_ALERTS && ERROR_ADDRESS) {
                $data = [];
                $data['Error Status'] = 500;
                $data['Error Message'] = 'Internal Server Error';
                $data['Error Description'] = 'A generic error message, given when no more specific message is suitable.';
                foreach($errorDetails as $key => $value) {
                    if(is_array($value)) {
                        $tempVal = <small></small>;
                        foreach($value as $key2 => $value2) {
                            $tempVal->appendChild(<x:frag>[{$key2}]{$value2}<br /></x:frag>);
                        }
                        $data['Error Details ' . $key] = $tempVal;
                    } else {
                        $data['Error Details ' . $key] = $value;
                    }
                }

                $tableBody = <tbody></tbody>;
            
                foreach($data as $key => $value) {
                    $tableBody->appendChild(<tr>
                        <td>{$key}</td>
                        <td>{$value}</td>
                    </tr>);
                }
                
                $message = <table style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                {$tableBody}
                            </table>;
                
                $message = $message->__toString();
                          
                $mail = new \HC\Email();
                $mail->send(ERROR_ADDRESS, SITE_DOMAIN . ': ' . 'Failed (500 - Internal Server Error)', $message);
            }

            if (!\HC\Site::checkProductionAccess()) {

                // Encrypt the output
                $encryption = new Encryption();

                $data = $errorDetails;
                unset($data['Timestamp']);
                
                $data = $encryption->encrypt(json_encode($data), 'HC_ERROR_' . $errorDetails['Hash']);

                // If could be encrypted
                if ($data) {

                    // Format it
                    $data = chunk_split($data, 50, PHP_EOL);

                    $errorDetails = ['ID' => $errorDetails['ID'], 'Timestamp' => $errorDetails['Timestamp'], 'Encrypted Error Information' => <pre>{$data}</pre>];

                }

            }
            
            // Clean all previous input
            if (ob_get_length()) {

                ob_clean();

            }

            $GLOBALS['skipShutdown'] = true;
            
            if ($isCLI) {
                var_dump($errorDetails);
            } else {
                try {
                    // Display output with page
                    $error = new \HC\Error();
                    $error->generateErrorPage(500, $errorDetails);
                } catch (\Exception $exception) {
                    Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), 0, $exception->getTrace(), true);
                }
            }

            return true;
        }

        protected static function getErrorLine($file, $line) {
            if(file_exists($file)) {
                $fileLines = file($file);
                return ' ' . $file . ':' . $line . ' ' . trim($fileLines[$line - 1]);
            } else {
                return ' ' . $file . ':' . $line;
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

        protected static function friendlyErrorTypeInt($type)
        {
            if($type & E_ERROR) // 1 //
                return E_ERROR;
            if($type & E_WARNING) // 2 //
                return E_WARNING;
            if($type & E_PARSE) // 4 //
                return E_PARSE;
            if($type & E_NOTICE) // 8 //
                return E_NOTICE;
            if($type & E_CORE_ERROR) // 16 //
                return E_CORE_ERROR;
            if($type & E_CORE_WARNING) // 32 //
                return E_CORE_WARNING;
            if($type & E_COMPILE_ERROR) // 64 //
                return E_COMPILE_ERROR;
            if($type & E_COMPILE_WARNING) // 128 //
                return E_COMPILE_WARNING;
            if($type & E_USER_ERROR) // 256 //
                return E_USER_ERROR;
            if($type & E_USER_WARNING) // 512 //
                return E_USER_WARNING;
            if($type & E_USER_NOTICE) // 1024 //
                return E_USER_NOTICE;
            if($type & E_STRICT) // 2048 //
                return E_STRICT;
            if($type & E_RECOVERABLE_ERROR) // 4096 //
                return E_RECOVERABLE_ERROR;
            if($type & E_DEPRECATED) // 8192 //
                return E_DEPRECATED;
            if($type & E_USER_DEPRECATED) // 16384 //
                return E_USER_DEPRECATED;

            return 0;
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

        public function generateErrorPage($code = 500, $errorDetails = [], $errorDescription = '', $skips = true, $return = false) {
            if(!isset(self::$errorTitle[$code])) {
                $code = 500;
            }

            $actualCode = \strtok($code, '-');

            if($errorDescription != '') {
                $errorDescription = $errorDescription;
            } else {
                $errorDescription = $this->errorDescription[$code];
            }
            
            if((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || defined('MODE') && MODE === 'API') {
                $pageSettings = [
                    'views' => [
                        'body'   => true
                    ]
                ];
                
                $errorPage = new \HC\Ajax($pageSettings);
                $errorPage->body = ['status' => $code, 'message' => self::$errorTitle[$code], 'errorDescription' => $errorDescription, 'errorDetails' => $errorDetails];
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

                $devInfo = '';
                if($errorDetails) {
                    
                    $errorInfo = <div></div>;
                    
                    foreach($errorDetails as $key => $value) {
                        if(is_array($value)) {
                            $temp = <pre></pre>;
                            
                            foreach($value as $key2 => $value2) {
                                $temp->appendChild(<x:frag>{'[' . $key2 . '] ' . $value2 . PHP_EOL . PHP_EOL}</x:frag>);
                            }
                            
                            $errorInfo->appendChild(<x:frag><span>{$key}: </span>{$temp}<br /></x:frag>);
                        } else {
                            $errorInfo->appendChild(<x:frag><span>{$key}: </span>{$value}<br /></x:frag>);
                        }
                    }

                    $devInfo = <x:frag><h2>Error Details</h2>{$errorInfo}</x:frag>;
                }
                
                $errorPage->body = <div class="container">
                                        <div class="row">
                                            <h1>Error - {$actualCode} - {self::$errorTitle[$code]}</h1>
                                            <div>
                                                    <p>{$errorDescription}</p>
                                                    {$devInfo}
                                            </div>
                                        </div>
                                    </div>;
            }



            // Clean all previous input
            if (ob_get_length()) {
                ob_clean();
            }
            
            if($skips) {
                $GLOBALS['skipShutdown'] = true;
            }
            
            try {
                if($return) {
                    return $errorPage;
                } else {
                    echo $errorPage->render();
                }
            } catch (\Exception $exception) {
                Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), 0, $exception->getTrace(), true);
            }
            
            if($skips) {
                $GLOBALS['skipRender'] = true;
            }
            
            if(PHP_SAPI !== 'cli') {
                http_response_code($actualCode);
                if(isset($errorDetails['ID'])) {
                    header('x-hc-error: ' . $errorDetails['ID']);
                }
                if(isset($this->errorHeaders[$actualCode])) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' ' . $actualCode . ' ' . $this->errorHeaders[$actualCode], true, $actualCode);
                }
            }

            return true;
        }
    }
