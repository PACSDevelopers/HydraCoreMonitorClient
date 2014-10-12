<?hh // decl


	namespace HC\Hooks;



	/**
	 * Class PostReceive
	 * @package HC\Hooks
	 */

	class PostReceive extends \HC\Core

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

					if (isset($globalSettings['hooks']['postReceive'])) {

						if (is_array($globalSettings['hooks']['postReceive'])) {

							$settings = $this->parseOptions($settings, $globalSettings['hooks']['postReceive']);

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
