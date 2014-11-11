<?hh // decl


	namespace HC\Hooks\PostReceive;



	/**
	 * Class GenerateErrorPages
	 * @package HC\Hooks\PostReceive
	 */

	class GenerateErrorPages extends \HC\Hooks\PostReceive

	{



		/**
		 * @var bool|array
		 */

		protected $settings = false;



		/**
		 * @param bool|array $settings
		 */

		public function __construct($settings = false)

		{

			$this->settings = $settings;

		}



		/**
		 * @return bool
		 */

		public function run()

		{

			// If we have settings
			if ($this->settings) {
                echo 'Generating Error Pages' . PHP_EOL;

                $errorDirectory = \HC_LOCATION . '/public/errors';
                
                if(!is_dir($errorDirectory)) {
                    mkdir($errorDirectory, 0777);
                }

                chmod($errorDirectory, 0777);
                chown($errorDirectory, 'www-data');
                
                $error = new \HC\Error();
                
                foreach($error::$errorTitle as $key => $value) {
                    if(is_int($key) && ($key != 500 && $key != 503)) {
                        ob_start();
                        $error->generateErrorPage($key, '', '', false);
                        $contents = ob_get_contents();
                        ob_clean();
                        file_put_contents($errorDirectory . '/' . $key . '.html', $contents);
                        chmod($errorDirectory . '/' . $key . '.html', 0777);
                        chown($errorDirectory . '/' . $key . '.html', 'www-data');
                    }
                }
                
                $GLOBALS['skipRender'] = true;
                
                echo 'Generated Error Pages' . PHP_EOL;
                return true;
			}

			return false;

		}

	}

