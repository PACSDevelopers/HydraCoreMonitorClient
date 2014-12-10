<?hh
	namespace HC;



	/**
	* @param (int|bool|array|string|null)[] $hydraCoreSettings
	* @returns true
	*/

	function initializeHydraCore($hydraCoreSettings)

	{

		// Set current release number
		if (!defined('HC_VERSION')) {

			define('HC_VERSION', '0.1.2');

		}



		// Setup locations
		if (!defined('HC_CORE_LOCATION')) {

			define('HC_CORE_LOCATION', __DIR__);

		}



		if (!defined('HC_LOCATION')) {

			define('HC_LOCATION', dirname(HC_CORE_LOCATION));

		}



        if (!defined('HC_SETTINGS_LOCATION')) {

                define('HC_SETTINGS_LOCATION', HC_LOCATION . '/settings');

        }



        if (!defined('HC_TMP_LOCATION')) {

                define('HC_TMP_LOCATION', HC_LOCATION . '/tmp');

        }



		if (!defined('HC_APPLICATION_LOCATION')) {

			define('HC_APPLICATION_LOCATION', HC_LOCATION . '/application');

		}

		if(is_file(HC_LOCATION . '/vendor/autoload.php')) {
				require_once HC_LOCATION . '/vendor/autoload.php';
		}


		require_once 'core.class.php';
        
		// Create the core object
		$GLOBALS['HC_CORE'] = new Core($hydraCoreSettings);



		if (!defined('HC_SKIP_LOCK_CHECK')) {

			if (is_file(HC_LOCATION . '/lock.json')) {

				$contents = json_decode(file_get_contents(HC_LOCATION . '/lock.json'), true);
                
                if($contents) {
                    if ($contents['Status'] === 'Unlocked') {

                        $cache = new Cache();

                        $cache->deleteAll();

                        unlink(HC_LOCATION . '/lock.json');

                    } else {

                        if (PHP_SAPI === 'cli') {

                            echo 'Application Locked.' . PHP_EOL;

                        } else {
                            
                            
                            if(!\HC\Site::checkProductionAccess()) {
                                $excludedKeys = ['To Message', 'From Message', 'From User', 'From Commit'];
                                foreach($excludedKeys as $value) {
                                    if(isset($contents[$value])) {
                                        unset($contents[$value]);
                                    }
                                }
                            }
                            $error = new \HC\Error();
                            $error->generateErrorPage('503-2', $contents);
                            exit(0);
                        }

                    }
                }

			}

		}



		// Initialize the application
		require_once(HC_APPLICATION_LOCATION . '/application.php');

		return true;

	}



	$hydraCoreSettings = [];

    require_once(__DIR__ . '/settings/include.hh');

	// Include the settings page
    $entryFile = dirname(__DIR__) . '/settings/include.hh';
    if(is_file($entryFile)) {
        require_once($entryFile);
        require_once(__DIR__ . '/settings/constants.hh');
        initializeHydraCore($hydraCoreSettings);
    } else {
        die('Unable to find settings file: ' . $entryFile);
    }
