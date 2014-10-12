<?hh // decl


	namespace HC;



	/**
	 * Class Log
	 * @package HC
	 */

	class Log extends Core

	{

		/**
		 * @var array
		 */

		protected $settings = [];



		/**
		 * @var DB|null
		 */

		protected $database = null;



		// Constructor


		/**
		 * @param (int|string|null)[] $settings
		 */

		public function __construct($settings = [])

		{



			$settings = $this->parseOptions($settings, []);



			// Parse global / local settings
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['log'])) {

				if (is_array($globalSettings['log'])) {

					$settings = $this->parseOptions($settings, $globalSettings['log']);

				}

			}



			$this->settings = $settings;

			$this->database = new DB();

		}



		/**
		 *
		 */

		public function __destruct()

		{

			$this->settings = null;

			$this->database = null;

		}



		/**
		 * @param array $options
		 * @return array|bool
		 */

		public function write($options = [])

		{

			return $this->database->write('log', $options);

		}



		/**
		 * @param array $options
		 * @return array|bool
		 */

		public function read($options = [])

		{

			return $this->database->read('log', [], $options);

		}



	}
