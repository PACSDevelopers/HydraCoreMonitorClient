<?hh // decl


	namespace HC;

	/**
	 * Class Intl
	 */

	class Intl extends Core

	{

		/**
		 * @var
		 */

		protected $settings = [
			'resourcePath' => null
		];

		protected $translator;


		/**
		 *
		 */

		public function __construct($settings = [])

		{
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['intl'])) {

				if (is_array($globalSettings['intl'])) {

					$this->settings = $this->parseOptions($settings, $globalSettings['intl']);

				}

			}

			$this->settings = $this->parseOptions($settings, $this->settings);

			$this->settings['resourcePath'] = HC_LOCATION . $this->settings['resourcePath'];

			$locale = \Locale::getDefault();
			
			if(file_exists($this->settings['resourcePath'])){
				try {
					$this->translator = new \ResourceBundle($locale, $this->settings['resourcePath'], true);
				} catch(\Exception $e) {
					throw new \Exception('Resource Failure');
				}
			} else {
				throw new \Exception('Invalid resource path.');
			}


			return true;

		}



		public function __destruct()

		{

			$this->settings = null;

		}

		public function get(){
			return $this->translator;
		}
	}
