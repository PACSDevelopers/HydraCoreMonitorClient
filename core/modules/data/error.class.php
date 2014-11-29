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
            0   => 'Failure',
            1   => 'Success',
            200 => 'Success',
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
            '503-2' => 'Service Unavailable',
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
            $newArray = [];
            
            $redactedKeys = ['Salt', 'salt', 'pass', 'Pass', 'password', 'Password', 'key', 'Key'];
            
            foreach($array as $key => $value) {
                if(is_object($value)) {
                    $newArray[$key] = serialize($value);
                    continue;
                }
                
                foreach($redactedKeys as $redactedKey) {
                    if(mb_strpos($key, $redactedKey) !== false) {
                        $newArray[$key] = '*REDACTED*';
                        break;
                    }
                }
                
                if(is_array($value)) {
                    $newArray[$key] = $value = self::protectArray($value);
                } else {
                    $newArray[$key] = $value;
                }
            }
            
            return $newArray;
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

            if ($isException) {
                if($customError) {
                    $errorDesc .= '[' . $errno . '] Uncaught Exception "' . $customError . '" with message "' . $errstr . '" on line ';
                } else {
                    $errorDesc .= '[' . $errno . '] Uncaught Exception with message "' . $errstr . '" on line ';
                }
            } else {
                if($customError) {
                    $errorDesc .= '[' . $errno . '] ' . $customError . ', ' . $errstr . ' on line ';
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

            $globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();
            if(isset($globalSettings['keys']) && isset($globalSettings['keys']['github'])) {
                $client = new \Github\Client();
                $client->authenticate($globalSettings['keys']['github'], NULL, \Github\Client::AUTH_HTTP_TOKEN);

                $errorDetails['Change Log'] = [];
                $commits = $client->api('repo')->commits()->all(REPO_USER, REPO_NAME, array('sha' => 'master', 'path' => str_replace(HC_LOCATION . '/', '', $errfile)));

                foreach($commits as $key => $val) {
                    $errorDetails['Change Log'][] = $val['commit']['author']['name'] . ' - ' . $val['commit']['author']['date'] . ' - ' . $val['commit']['message'];
                }
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

            if (!\HC\Site::checkProductionAccess()) {

                // Encrypt the output
                $encryption = new Encryption();

                $data = $errorDetails;
                unset($data['Timestamp']);
                
                $data = $encryption->encrypt(json_encode($data), 'HC_ERROR_' . $errorDetails['ID']);

                // If could be encrypted
                if ($data) {

                    // Format it
                    $data = chunk_split($data, 50, PHP_EOL);

                    $errorDetails = ['ID' => $errorDetails['ID'], 'Timestamp' => $errorDetails['Timestamp'], 'Encrypted Error Information' => <pre>{$data}</pre>];

                }

            } else if(ENVIRONMENT !== 'DEV' && ERROR_ALERTS && ERROR_ADDRESS) {
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
                    Error::exceptionHandler($exception);
                }
            }

            return true;
        }

        protected static function getErrorLine($file, $line) {
            if(file_exists($file)) {
                $fileLines = file($file);
                if(isset($fileLines[$line - 1])) {
                    return ' ' . $file . ':' . $line . ' ' . trim($fileLines[$line - 1]);
                }
            }

            return ' ' . $file . ':' . $line;
        }

        <<__Memoize>>
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

        <<__Memoize>>
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
            Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $skipTrace, $exception->getTrace(), true, get_class($exception));
            return true;

        }

        public static function checkPHPSyntax($file, $throwError = true) {

            // Check it's valid syntax
            $file = realpath($file);
            $return_var = -1;
            $output = [];
            exec('hhvm -l ' . escapeshellarg($file), $output, $return_var);

            if($return_var === 1) {
                if($throwError) {
                    $lastnum = 0;
                    $result = implode($output);

                    if(preg_match_all('/\d+/', $result, $numbers)) {
                        $lastnum = end($numbers[0]);
                    }

                    $result = str_replace('Fatal error: syntax error, ', '', $result);
                    $result = str_replace(' in ' . $file . ' on line ' . $lastnum, '', $result);
                    $result = ucfirst($result);

                    Error::errorHandler(E_PARSE, $result, $file, $lastnum, 2, [], false, 'Syntax Error');
                }
                
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
                if(isset($this->errorDescription[$code])) {
                    $errorDescription = $this->errorDescription[$code];
                }
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
                Error::exceptionHandler($exception);
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

        public static function curl_strerror($errorno) {
            $error_description = '';

            if (!is_int($errorno) || is_null($errorno) || empty($errorno))
                return 'Unknown Error';

            switch($errorno) {
                case 0:
                    /*
                     * CURLE_OK (0)
                     */
                    $error_description = 'CURLE_OK: All fine. Proceed as usual.';
                    break;
                case 1:
                    /*
                     * CURLE_UNSUPPORTED_PROTOCOL (1)
                     */
                    $error_description = 'CURLE_UNSUPPORTED_PROTOCOL: The URL you passed to libcurl used a protocol that this libcurl does not support. The support might be a compile-time option that you didn\'t use, it can be a misspelled protocol string or just a protocol libcurl has no code for.';
                    break;
                case 2:
                    /*
                     * CURLE_FAILED_INIT (2)
                     */
                    $error_description = 'CURLE_FAILED_INIT: Very early initialization code failed. This is likely to be an internal error or problem, or a resource problem where something fundamental couldn\'t get done at init time.';
                    break;
                case 3:
                    /*
                     * CURLE_URL_MALFORMAT (3)
                     */
                    $error_description = 'CURLE_URL_MALFORMAT: The URL was not properly formatted.';
                    break;
                case 4:
                    /*
                     * CURLE_NOT_BUILT_IN (4)
                     */
                    $error_description = 'CURLE_NOT_BUILT_IN: A requested feature, protocol or option was not found built-in in this libcurl due to a build-time decision. This means that a feature or option was not enabled or explicitly disabled when libcurl was built and in order to get it to function you have to get a rebuilt libcurl.';
                    break;
                case 5:
                    /*
                     * CURLE_COULDNT_RESOLVE_PROXY (5)
                     */
                    $error_description = 'CURLE_COULDNT_RESOLVE_PROXY: Couldn\'t resolve proxy. The given proxy host could not be resolved.';
                    break;
                case 6:
                    /*
                     * CURLE_COULDNT_RESOLVE_HOST (6)
                     */
                    $error_description = 'CURLE_COULDNT_RESOLVE_HOST: Couldn\'t resolve host. The given remote host was not resolved.';
                    break;
                case 7:
                    /*
                     * CURLE_COULDNT_CONNECT (7)
                     */
                    $error_description = 'CURLE_COULDNT_CONNECT: Failed to connect() to host or proxy.';
                    break;
                case 8:
                    /*
                     * CURLE_FTP_WEIRD_SERVER_REPLY (8)
                     */
                    $error_description = 'CURLE_FTP_WEIRD_SERVER_REPLY: After connecting to a FTP server, libcurl expects to get a certain reply back. This error code implies that it got a strange or bad reply. The given remote server is probably not an OK FTP server.';
                    break;
                case 9:
                    /*
                     * CURLE_REMOTE_ACCESS_DENIED (9)
                     */
                    $error_description = 'CURLE_REMOTE_ACCESS_DENIED: We were denied access to the resource given in the URL. For FTP, this occurs while trying to change to the remote directory.';
                    break;
                case 10:
                    /*
                     * CURLE_FTP_ACCEPT_FAILED (10)
                     */
                    $error_description = 'CURLE_FTP_ACCEPT_FAILED: While waiting for the server to connect back when an active FTP session is used, an error code was sent over the control connection or similar.';
                    break;
                case 11:
                    /*
                     * CURLE_FTP_WEIRD_PASS_REPLY (11)
                     */
                    $error_description = 'CURLE_FTP_WEIRD_PASS_REPLY: After having sent the FTP password to the server, libcurl expects a proper reply. This error code indicates that an unexpected code was returned.';
                    break;
                case 12:
                    /*
                     * CURLE_FTP_ACCEPT_TIMEOUT (12)
                     */
                    $error_description = 'CURLE_FTP_ACCEPT_TIMEOUT: During an active FTP session while waiting for the server to connect, the CURLOPT_ACCEPTTIMOUT_MS(3) (or the internal default) timeout expired.';
                    break;
                case 13:
                    /*
                     * CURLE_FTP_WEIRD_PASV_REPLY (13)
                     */
                    $error_description = 'CURLE_FTP_WEIRD_PASV_REPLY: libcurl failed to get a sensible result back from the server as a response to either a PASV or a EPSV command. The server is flawed.';
                    break;
                case 14:
                    /*
                     * CURLE_FTP_WEIRD_227_FORMAT (14)
                     */
                    $error_description = 'CURLE_FTP_WEIRD_227_FORMAT: FTP servers return a 227-line as a response to a PASV command. If libcurl fails to parse that line, this return code is passed back.';
                    break;
                case 15:
                    /*
                     * CURLE_FTP_CANT_GET_HOST (15)
                     */
                    $error_description = 'CURLE_FTP_CANT_GET_HOST: An internal failure to lookup the host used for the new connection.';
                    break;
                case 17:
                    /*
                     * CURLE_FTP_COULDNT_SET_TYPE (17)
                     */
                    $error_description = 'CURLE_FTP_COULDNT_SET_TYPE: Received an error when trying to set the transfer mode to binary or ASCII.';
                    break;
                case 18:
                    /*
                     * CURLE_PARTIAL_FILE (18)
                     */
                    $error_description = 'CURLE_PARTIAL_FILE: A file transfer was shorter or larger than expected. This happens when the server first reports an expected transfer size, and then delivers data that doesn\'t match the previously given size.';
                    break;
                case 19:
                    /*
                     * CURLE_FTP_COULDNT_RETR_FILE (19)
                     */
                    $error_description = 'CURLE_FTP_COULDNT_RETR_FILE: This was either a weird reply to a \'RETR\' command or a zero byte transfer complete.';
                    break;
                case 21:
                    /*
                     * CURLE_QUOTE_ERROR (21)
                     */
                    $error_description = 'CURLE_QUOTE_ERROR: When sending custom "QUOTE" commands to the remote server, one of the commands returned an error code that was 400 or higher (for FTP) or otherwise indicated unsuccessful completion of the command.';
                    break;
                case 22:
                    /*
                     * CURLE_HTTP_RETURNED_ERROR (22)
                     */
                    $error_description = 'CURLE_HTTP_RETURNED_ERROR: This is returned if CURLOPT_FAILONERROR is set TRUE and the HTTP server returns an error code that is >= 400.';
                    break;
                case 23:
                    /*
                     * CURLE_WRITE_ERROR (23)
                     */
                    $error_description = 'CURLE_WRITE_ERROR: An error occurred when writing received data to a local file, or an error was returned to libcurl from a write callback.';
                    break;
                case 25:
                    /*
                     * CURLE_UPLOAD_FAILED (25)
                     */
                    $error_description = 'CURLE_UPLOAD_FAILED: Failed starting the upload. For FTP, the server typically denied the STOR command. The error buffer usually contains the server\'s explanation for this.';
                    break;
                case 26:
                    /*
                     * CURLE_READ_ERROR (26)
                     */
                    $error_description = 'CURLE_READ_ERROR: There was a problem reading a local file or an error returned by the read callback.';
                    break;
                case 27:
                    /*
                     * CURLE_OUT_OF_MEMORY (27)
                     */
                    $error_description = 'CURLE_OUT_OF_MEMORY: A memory allocation request failed. This is serious badness and things are severely screwed up if this ever occurs.';
                    break;
                case 28:
                    /*
                     * CURLE_OPERATION_TIMEDOUT (28)
                     */
                    $error_description = 'CURLE_OPERATION_TIMEDOUT: Operation timeout. The specified time-out period was reached according to the conditions.';
                    break;
                case 30:
                    /*
                     * CURLE_FTP_PORT_FAILED (30)
                     */
                    $error_description = 'CURLE_FTP_PORT_FAILED: The FTP PORT command returned error. This mostly happens when you haven\'t specified a good enough address for libcurl to use. See CURLOPT_FTPPORT.';
                    break;
                case 31:
                    /*
                     * CURLE_FTP_COULDNT_USE_REST (31)
                     */
                    $error_description = 'CURLE_FTP_COULDNT_USE_REST: The FTP REST command returned error. This should never happen if the server is sane.';
                    break;
                case 33:
                    /*
                     * CURLE_RANGE_ERROR (33)
                     */
                    $error_description = 'CURLE_RANGE_ERROR: The server does not support or accept range requests.';
                    break;
                case 34:
                    /*
                     * CURLE_HTTP_POST_ERROR (34)
                     */
                    $error_description = 'CURLE_HTTP_POST_ERROR: This is an odd error that mainly occurs due to internal confusion.';
                    break;
                case 35:
                    /*
                     * CURLE_SSL_CONNECT_ERROR (35)
                     */
                    $error_description = 'CURLE_SSL_CONNECT_ERROR: A problem occurred somewhere in the SSL/TLS handshake. You really want the error buffer and read the message there as it pinpoints the problem slightly more. Could be certificates (file formats, paths, permissions), passwords, and others.';
                    break;
                case 36:
                    /*
                     * CURLE_BAD_DOWNLOAD_RESUME (36)
                     */
                    $error_description = 'CURLE_BAD_DOWNLOAD_RESUME: The download could not be resumed because the specified offset was out of the file boundary.';
                    break;
                case 37:
                    /*
                     * CURLE_FILE_COULDNT_READ_FILE (37)
                     */
                    $error_description = 'CURLE_FILE_COULDNT_READ_FILE: A file given with FILE:// couldn\'t be opened. Most likely because the file path doesn\'t identify an existing file. Did you check file permissions?';
                    break;
                case 38:
                    /*
                     * CURLE_LDAP_CANNOT_BIND (38)
                     */
                    $error_description = 'CURLE_LDAP_CANNOT_BIND: LDAP cannot bind. LDAP bind operation failed.';
                    break;
                case 39:
                    /*
                     * CURLE_LDAP_SEARCH_FAILED (39)
                     */
                    $error_description = 'CURLE_LDAP_SEARCH_FAILED: LDAP search failed.';
                    break;
                case 41:
                    /*
                     * CURLE_FUNCTION_NOT_FOUND (41)
                     */
                    $error_description = 'CURLE_FUNCTION_NOT_FOUND: Function not found. A required zlib function was not found.';
                    break;
                case 42:
                    /*
                     * CURLE_ABORTED_BY_CALLBACK (42)
                     */
                    $error_description = 'CURLE_ABORTED_BY_CALLBACK: Aborted by callback. A callback returned "abort" to libcurl.';
                    break;
                case 43:
                    /*
                     * CURLE_BAD_FUNCTION_ARGUMENT (43)
                     */
                    $error_description = 'CURLE_BAD_FUNCTION_ARGUMENT: Internal error. A function was called with a bad parameter.';
                    break;
                case 45:
                    /*
                     * CURLE_INTERFACE_FAILED (45)
                     */
                    $error_description = 'CURLE_INTERFACE_FAILED: Interface error. A specified outgoing interface could not be used. Set which interface to use for outgoing connections\' source IP address with CURLOPT_INTERFACE.';
                    break;
                case 47:
                    /*
                     * CURLE_TOO_MANY_REDIRECTS (47)
                     */
                    $error_description = 'CURLE_TOO_MANY_REDIRECTS: Too many redirects. When following redirects, libcurl hit the maximum amount. Set your limit with CURLOPT_MAXREDIRS.';
                    break;
                case 48:
                    /*
                     * CURLE_UNKNOWN_OPTION (48)
                     */
                    $error_description = 'CURLE_UNKNOWN_OPTION: An option passed to libcurl is not recognized/known. Refer to the appropriate documentation. This is most likely a problem in the program that uses libcurl. The error buffer might contain more specific information about which exact option it concerns.';
                    break;
                case 49:
                    /*
                     * CURLE_TELNET_OPTION_SYNTAX (49)
                     */
                    $error_description = 'CURLE_TELNET_OPTION_SYNTAX: A telnet option string was Illegally formatted.';
                    break;
                case 51:
                    /*
                     * CURLE_PEER_FAILED_VERIFICATION (51)
                     */
                    $error_description = 'CURLE_PEER_FAILED_VERIFICATION: The remote server\'s SSL certificate or SSH md5 fingerprint was deemed not OK.';
                    break;
                case 52:
                    /*
                     * CURLE_GOT_NOTHING (52)
                     */
                    $error_description = 'CURLE_GOT_NOTHING: Nothing was returned from the server, and under the circumstances, getting nothing is considered an error.';
                    break;
                case 53:
                    /*
                     * CURLE_SSL_ENGINE_NOTFOUND (53)
                     */
                    $error_description = 'CURLE_SSL_ENGINE_NOTFOUND: The specified crypto engine wasn\'t found.';
                    break;
                case 54:
                    /*
                     * CURLE_SSL_ENGINE_SETFAILED (54)
                     */
                    $error_description = 'CURLE_SSL_ENGINE_SETFAILED: Failed setting the selected SSL crypto engine as default!';
                    break;
                case 55:
                    /*
                     * CURLE_SEND_ERROR (55)
                     */
                    $error_description = 'CURLE_SEND_ERROR: Failed sending network data.';
                    break;
                case 56:
                    /*
                     * CURLE_RECV_ERROR (56)
                     */
                    $error_description = 'CURLE_RECV_ERROR: Failure with receiving network data.';
                    break;
                case 58:
                    /*
                     * CURLE_SSL_CERTPROBLEM (58)
                     */
                    $error_description = 'CURLE_SSL_CERTPROBLEM: Problem with the local client certificate.';
                    break;
                case 59:
                    /*
                     * CURLE_SSL_CIPHER (59)
                     */
                    $error_description = 'CURLE_SSL_CIPHER: Couldn\'t use specified cipher.';
                    break;
                case 60:
                    /*
                     * CURLE_SSL_CACERT (60)
                     */
                    $error_description = 'CURLE_SSL_CACERT: Peer certificate cannot be authenticated with known CA certificates.';
                    break;
                case 61:
                    /*
                     * CURLE_BAD_CONTENT_ENCODING (61)
                     */
                    $error_description = 'CURLE_BAD_CONTENT_ENCODING: Unrecognized transfer encoding.';
                    break;
                case 62:
                    /*
                     * CURLE_LDAP_INVALID_URL (62)
                     */
                    $error_description = 'CURLE_LDAP_INVALID_URL: Invalid LDAP URL.';
                    break;
                case 63:
                    /*
                     * CURLE_FILESIZE_EXCEEDED (63)
                     */
                    $error_description = 'CURLE_FILESIZE_EXCEEDED: Maximum file size exceeded.';
                    break;
                case 64:
                    /*
                     * CURLE_USE_SSL_FAILED (64)
                     */
                    $error_description = 'CURLE_USE_SSL_FAILED: Requested FTP SSL level failed.';
                    break;
                case 65:
                    /*
                     * CURLE_SEND_FAIL_REWIND (65)
                     */
                    $error_description = 'CURLE_SEND_FAIL_REWIND: When doing a send operation curl had to rewind the data to retransmit, but the rewinding operation failed.';
                    break;
                case 66:
                    /*
                     * CURLE_SSL_ENGINE_INITFAILED (66)
                     */
                    $error_description = 'CURLE_SSL_ENGINE_INITFAILED: Initiating the SSL Engine failed.';
                    break;
                case 67:
                    /*
                     * CURLE_LOGIN_DENIED (67)
                     */
                    $error_description = 'CURLE_LOGIN_DENIED: The remote server denied curl to login (Added in 7.13.1)';
                    break;
                case 68:
                    /*
                     * CURLE_TFTP_NOTFOUND (68)
                     */
                    $error_description = 'CURLE_TFTP_NOTFOUND: File not found on TFTP server.';
                    break;
                case 69:
                    /*
                     * CURLE_TFTP_PERM (69)
                     */
                    $error_description = 'CURLE_TFTP_PERM: Permission problem on TFTP server.';
                    break;
                case 70:
                    /*
                     * CURLE_REMOTE_DISK_FULL (70)
                     */
                    $error_description = 'CURLE_REMOTE_DISK_FULL: Out of disk space on the server.';
                    break;
                case 71:
                    /*
                     * CURLE_TFTP_ILLEGAL (71)
                     */
                    $error_description = 'CURLE_TFTP_ILLEGAL: Illegal TFTP operation.';
                    break;
                case 72:
                    /*
                     * CURLE_TFTP_UNKNOWNID (72)
                     */
                    $error_description = 'CURLE_TFTP_UNKNOWNID: Unknown TFTP transfer ID.';
                    break;
                case 73:
                    /*
                     * CURLE_REMOTE_FILE_EXISTS (73)
                     */
                    $error_description = 'CURLE_REMOTE_FILE_EXISTS: File already exists and will not be overwritten.';
                    break;
                case 74:
                    /*
                     * CURLE_TFTP_NOSUCHUSER (74)
                     */
                    $error_description = 'CURLE_TFTP_NOSUCHUSER: This error should never be returned by a properly functioning TFTP server.';
                    break;
                case 75:
                    /*
                     * CURLE_CONV_FAILED (75)
                     */
                    $error_description = 'CURLE_CONV_FAILED: Character conversion failed.';
                    break;
                case 76:
                    /*
                     * CURLE_CONV_REQD (76)
                     */
                    $error_description = 'CURLE_CONV_REQD: Caller must register conversion callbacks.';
                    break;
                case 77:
                    /*
                     * CURLE_SSL_CACERT_BADFILE (77)
                     */
                    $error_description = 'CURLE_SSL_CACERT_BADFILE: Problem with reading the SSL CA cert (path? access rights?)';
                    break;
                case 78:
                    /*
                     * CURLE_REMOTE_FILE_NOT_FOUND (78)
                     */
                    $error_description = 'CURLE_REMOTE_FILE_NOT_FOUND: The resource referenced in the URL does not exist.';
                    break;
                case 79:
                    /*
                     * CURLE_SSH (79)
                     */
                    $error_description = 'CURLE_SSH: An unspecified error occurred during the SSH session.';
                    break;
                case 80:
                    /*
                     * CURLE_SSL_SHUTDOWN_FAILED (80)
                     */
                    $error_description = 'CURLE_SSL_SHUTDOWN_FAILED: Failed to shut down the SSL connection.';
                    break;
                case 81:
                    /*
                     * CURLE_AGAIN (81)
                     */
                    $error_description = 'CURLE_AGAIN: Socket is not ready for send/recv wait till it\'s ready and try again. This return code is only returned from curl_easy_recv and curl_easy_send (Added in 7.18.2)';
                    break;
                case 82:
                    /*
                     * CURLE_SSL_CRL_BADFILE (82)
                     */
                    $error_description = 'CURLE_SSL_CRL_BADFILE: Failed to load CRL file (Added in 7.19.0)';
                    break;
                case 83:
                    /*
                     * CURLE_SSL_ISSUER_ERROR (83)
                     */
                    $error_description = 'CURLE_SSL_ISSUER_ERROR: Issuer check failed (Added in 7.19.0)';
                    break;
                case 84:
                    /*
                     * CURLE_FTP_PRET_FAILED (84)
                     */
                    $error_description = 'CURLE_FTP_PRET_FAILED: The FTP server does not understand the PRET command at all or does not support the given argument. Be careful when using CURLOPT_CUSTOMREQUEST, a custom LIST command will be sent with PRET CMD before PASV as well. (Added in 7.20.0)';
                    break;
                case 85:
                    /*
                     * CURLE_RTSP_CSEQ_ERROR (85)
                     */
                    $error_description = 'CURLE_RTSP_CSEQ_ERROR: Mismatch of RTSP CSeq numbers.';
                    break;
                case 86:
                    /*
                     * CURLE_RTSP_SESSION_ERROR (86)
                     */
                    $error_description = 'CURLE_RTSP_SESSION_ERROR: Mismatch of RTSP Session Identifiers.';
                    break;
                case 87:
                    /*
                     * CURLE_FTP_BAD_FILE_LIST (87)
                     */
                    $error_description = 'CURLE_FTP_BAD_FILE_LIST: Unable to parse FTP file list (during FTP wildcard downloading).';
                    break;
                case 88:
                    /*
                     * CURLE_CHUNK_FAILED (88)
                     */
                    $error_description = 'CURLE_CHUNK_FAILED: Chunk callback reported error.';
                    break;
                case 89:
                    /*
                     * CURLE_NO_CONNECTION_AVAILABLE (89) - Added for completeness.
                     */
                    $error_description = 'CURLE_NO_CONNECTION_AVAILABLE: (For internal use only, will never be returned by libcurl) No connection available, the session will be queued. (added in 7.30.0)';
                    break;
                default:
                    $error_description = 'UNKNOWN CURL ERROR NUMBER: This error code is not mapped to any known error.  Possibly a system error?';
                    break;
            }

            return $error_description;
        }
    }
