<?hh // decl


	namespace HC;

	/**
	 * Class View
	 */

	class View extends Core

	{

		// Setup class public variables
		/**
		 * @var string
		 */

		public $body = '';


		// Setup class protected variables
		/**
		 * @var array
		 */

		protected $settings = [
			'pageName' => ''
		];



		// Setup public functions
		/**
		 * @param (int|bool|array|string|null)[] $settings
		 */

		public function __construct(&$settings = []) {
			$settings = $this->parseOptions($settings, $this->settings);

			// Parse global / local options
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();
            if(isset($globalSettings['views'])) {
                $settings = $this->parseOptions($settings, $globalSettings['views']);
            }

			$this->settings = $settings;
			return true;

		}

		public function __destruct()
        {
            $this->settings = null;
        }

		public function render() {
			return $this->body;
		}
	}

