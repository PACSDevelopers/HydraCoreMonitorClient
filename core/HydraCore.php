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

			define('HC_VERSION', '0.1.0');

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

        if(is_file(HC_LOCATION . '/vendor/xhp/init.hh')) {
			require_once HC_LOCATION . '/vendor/xhp/init.hh';
		}

		if(is_file(HC_LOCATION . '/vendor/autoload.php')) {
				require_once HC_LOCATION . '/vendor/autoload.php';
		}


		require_once 'core.class.php';



		// No autoloading in cli mode
		if (PHP_SAPI === 'cli') {

            require_once(HC_CORE_LOCATION . '/modules/data/view.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/page.class.php');

			require_once(HC_CORE_LOCATION . '/modules/render/table.class.php');

			require_once(HC_CORE_LOCATION . '/modules/render/forms/form.class.php');

			require_once(HC_CORE_LOCATION . '/modules/render/forms/button.class.php');

			require_once(HC_CORE_LOCATION . '/modules/render/forms/textarea.class.php');

			require_once(HC_CORE_LOCATION . '/modules/render/forms/checkbox.class.php');

			require_once(HC_CORE_LOCATION . '/modules/render/forms/input.class.php');

			require_once(HC_CORE_LOCATION . '/modules/render/forms/select.class.php');

            require_once(HC_CORE_LOCATION . '/modules/data/db.class.php');

            require_once(HC_CORE_LOCATION . '/modules/data/db2.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/user.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/email.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/encryption.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/cache.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/error.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/upload.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/log.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/directory.class.php');

			require_once(HC_CORE_LOCATION . '/modules/data/file.class.php');

            require_once(HC_CORE_LOCATION . '/modules/data/process.class.php');

            require_once(HC_CORE_LOCATION . '/modules/data/compression.class.php');

            require_once(HC_CORE_LOCATION . '/modules/data/authenticator.class.php');

            require_once(HC_CORE_LOCATION . '/modules/data/text.class.php');

            require_once(HC_CORE_LOCATION . '/modules/system/mvc.class.php');

			require_once(HC_CORE_LOCATION . '/hooks/postReceive.class.php');

			require_once(HC_CORE_LOCATION . '/hooks/postReceive/compileResources.class.php');

			require_once(HC_CORE_LOCATION . '/hooks/postReceive/unlock.class.php');

			require_once(HC_CORE_LOCATION . '/hooks/preReceive.class.php');

			require_once(HC_CORE_LOCATION . '/hooks/preReceive/lock.class.php');

            require_once(HC_CORE_LOCATION . '/hooks/cron.class.php');

		}

		// Create the core object
		$GLOBALS['HC_CORE'] = new Core($hydraCoreSettings);



		if (!defined('HC_SKIP_LOCK_CHECK')) {

			if (is_file(HC_LOCATION . '/lock.pid')) {

				$contents = file_get_contents(HC_LOCATION . '/lock.pid');

				if (trim($contents) === 'Unlock') {

					$cache = new Cache();

					$cache->deleteAll();

					unlink(HC_LOCATION . '/lock.pid');

				} else {

					if (PHP_SAPI === 'cli') {

						echo 'Application Locked.' . PHP_EOL;

					} else {

						$error = new \HC\Error();
						$error->generateErrorPage('503-2');
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
