<?hh // decl


	namespace HC;



	require_once 'modules/data/site.class.php';



	/**
	 * Class Core
	 *
	 * This class pulls all the settings and creates the site object.
	 */

	class Core

	{

		/**
		 * @var Site|null
		 */

		protected $site = null;

		protected $settings = [];



		// Construct the core based on settings
		/**
		 * @param array $settings
		 * @throws \Exception
		 */

		public function __construct($settings = [])

		{
			$this->settings = $settings;

			// Check if this version of PHP is supported
			$this->checkVersion();



			if (is_null($this->site)) {

				$this->site = new Site($settings);

			}

            if(!headers_sent() && !defined('HC_SENT_HEADERS')) {
                @header('X-Powered-By: HydraCore ' . HC_VERSION, false);
                @header('X-Powered-By: ' . SITE_NAME . ' ' . APP_VERSION, false);
                define('HC_SENT_HEADERS', true);
            }

			return true;

		}

		/**
		 *  checkVersion
		 *
		 *  This function will check the current php version, if it is not supported it will exit with 1 and display an error
		 */

		protected function checkVersion()

		{



			// Check what version of PHP we are using
			if (version_compare(PHP_VERSION, '5.4', '<')) {

				throw new \Exception('HydraCore (' . HC_VERSION . ') requires at least PHP 5.4, you are using: ' . PHP_VERSION);

			}

		}



		/**
		 * @return Site|null
		 */

		public function getSite()

		{



			// Return the site object
			if(isset($this->site)) {

				return $this->site;

			}

			return false;
		}



		/**
		 * @param Site $site
		 * @returns bool
		 */

		public function setSite($site)

		{



			// Set the site of the core to a new site
			$this->site = $site;



			return true;

		}



		/**
		 * parseOptions
		 *
		 * This function parses options making sure that each option needed has a defaultValue
		 *
		 * @param (int|bool|array|string|null)[] $settings
		 * @param (int|bool|array|string|null)[] $defaultValues
		 *
		 * @throws \Exception
		 *
		 * @return array
		 */

		public static function parseOptions($settings, $defaultValues)

		{

			// If it's an exact match, just return
			if($settings === $defaultValues) {

				return $settings;

			}



			// Loop through our defined settings
			foreach ($settings as $key => &$value) {

                // Check for a default value
                if (isset($defaultValues[$key])) {

                    // Skip if already matches
                    if($defaultValues[$key] === $value) {

                        continue;

                    }

                } else {

                    // We don't have a default value, use input value
                    $defaultValues[$key] = $value;

                }



                // If they are both arrays and they don't already match, parse
                if (($defaultValues[$key] !== $value) && (is_array($defaultValues[$key]) && is_array($value))) {

                    $value = Core::parseOptions($value, $defaultValues[$key]);

                }

            }



			// If this matches now, just return
			if($defaultValues === $settings) {

				return $settings;

			}



			return array_merge($defaultValues, $settings);

		}



		/**
		 * @param string $class
		 */

		public static function autoLoader($class)

		{

			switch ($class) {



				// Modules
                case 'HC\View':
        
                require_once(HC_CORE_LOCATION . '/modules/data/view.class.php');
    
                break;

                case 'HC\Page':

				require_once(HC_CORE_LOCATION . '/modules/data/page.class.php');

				break;

				case 'HC\Ajax':

				require_once(HC_CORE_LOCATION . '/modules/data/ajax.class.php');

				break;
                
                case 'HC\DB':

                require_once(HC_CORE_LOCATION . '/modules/data/db.class.php');

                break;

                case 'HC\DB2':

                require_once(HC_CORE_LOCATION . '/modules/data/db2.class.php');

                break;

                case 'HC\DB2\Query':

                require_once(HC_CORE_LOCATION . '/modules/data/db2/query.class.php');

                break;

				case 'HC\User':

                require_once(HC_CORE_LOCATION . '/modules/data/user.class.php');

                break;

				case 'HC\Email':

                require_once(HC_CORE_LOCATION . '/modules/data/email.class.php');

                break;

				case 'HC\Encryption':

                require_once(HC_CORE_LOCATION . '/modules/data/encryption.class.php');

                break;

				case 'HC\Cache':

                require_once(HC_CORE_LOCATION . '/modules/data/cache.class.php');

                break;

				case 'HC\Error':

                require_once(HC_CORE_LOCATION . '/modules/data/error.class.php');

                break;

				case 'HC\Upload':

                require_once(HC_CORE_LOCATION . '/modules/data/upload.class.php');

                break;

				case 'HC\Log':

                require_once(HC_CORE_LOCATION . '/modules/data/log.class.php');

                break;

				case 'HC\Directory':

                require_once(HC_CORE_LOCATION . '/modules/data/directory.class.php');

                break;

				case 'HC\File':

                require_once(HC_CORE_LOCATION . '/modules/data/file.class.php');

                break;

				case 'HC\Process':

                require_once(HC_CORE_LOCATION . '/modules/data/process.class.php');

                break;

				case 'HC\MVC':

                require_once(HC_CORE_LOCATION . '/modules/system/mvc.class.php');

                break;

				case 'HC\Compression':

                require_once(HC_CORE_LOCATION . '/modules/data/compression.class.php');

                break;

				case 'HC\Text':

                require_once(HC_CORE_LOCATION . '/modules/data/text.class.php');

                break;

				case 'HC\Authenticator':

                require_once(HC_CORE_LOCATION . '/modules/data/authenticator.class.php');

                break;

			    case 'HC\Intl':
                    
                require_once(HC_CORE_LOCATION . '/modules/data/intl.class.php');
                
                break;



				// Hooks
				case 'HC\Hooks\PostReceive':

                require_once(HC_CORE_LOCATION . '/hooks/postReceive.class.php');

                break;

				case 'HC\Hooks\PostReceive\CompileResources':

                require_once(HC_CORE_LOCATION . '/hooks/postReceive/compileResources.class.php');

                break;

                case 'HC\Hooks\PostReceive\UpdateComposer':

                require_once(HC_CORE_LOCATION . '/hooks/postReceive/updateComposer.class.php');

                break;
                
                case 'HC\Hooks\PostReceive\Unlock':

                require_once(HC_CORE_LOCATION . '/hooks/postReceive/unlock.class.php');

                break;

                case 'HC\Hooks\PostReceive\GenerateErrorPages':

                require_once(HC_CORE_LOCATION . '/hooks/postReceive/generateErrorPages.class.php');

                break;

				case 'HC\Hooks\PreReceive':

                require_once(HC_CORE_LOCATION . '/hooks/preReceive.class.php');

                break;

				case 'HC\Hooks\PreReceive\Lock':

                require_once(HC_CORE_LOCATION . '/hooks/preReceive/lock.class.php');

                break;

                case 'HC\Hooks\Cron':

                require_once(HC_CORE_LOCATION . '/hooks/cron.class.php');

                break;

			}

		}



		public function init(){

			$globalSettings = $this->getSite()->getSettings();

			$rewrites = [];

			if(isset($globalSettings['pages'])) {

				if(isset($globalSettings['pages']['rewrites'])) {

					$rewrites = $globalSettings['pages']['rewrites'];

				}

			}

			if(defined('MODE')) {

				switch(MODE) {

					case 'MVC':

						$mvc = new \HC\MVC(['enabled' => true, 'rewrites' => $rewrites]);

						return true;

					break;

                    case 'API':

                        $mvc = new \HC\MVC(['enabled' => true, 'rewrites' => $rewrites, 'api' => true]);

                        return true;
                    break;

				}

			}



			$mvc = new \HC\MVC(['rewrites' => $rewrites]);

			return true;

		}

	}
