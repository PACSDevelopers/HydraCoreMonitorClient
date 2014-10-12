<?hh // decl


	namespace HC\Hooks;



	/**
	 * Class PreReceive
	 * @package HC\Hooks
	 */

	class PreReceive extends \HC\Core

	{

		/**
		 * @var array
		 */

		protected $settings = [];



		/**
		 * @param array $settings
		 */

		public function __construct($settings = [])

		{

			$settings = $this->parseOptions($settings, []);



			// Parse global / local settings
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['hooks'])) {

				if (is_array($globalSettings['hooks'])) {

					if (isset($globalSettings['hooks']['preReceive'])) {

						if (is_array($globalSettings['hooks']['preReceive'])) {

							$settings = $this->parseOptions($settings, $globalSettings['hooks']['preReceive']);

						}

					}

				}

			}



			$this->settings = $settings;

		}



		/**
		 * Runs hooks
		 */

		public function run()

		{

			foreach ($this->settings as $key => $value) {

				$hook = new $key($value);

				if (method_exists($hook, 'run')) {

					$cwd = getcwd();

					if (!$hook->run()) {

						chdir($cwd);

						echo 'Hook Failed: ' . PHP_EOL . $key . PHP_EOL;

						break;

					}

					chdir($cwd);

				}

			}

		}

	}
