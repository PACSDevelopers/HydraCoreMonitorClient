<?hh // decl


	namespace HC\Hooks\PreReceive;



	/**
	 * Class Lock
	 * @package HC\Hooks\PreReceive
	 */

	class Lock extends \HC\Hooks\PreReceive

	{



		/**
		 * @var bool
		 */

		protected $settings;



		/**
		 * @param bool $settings
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

			if ($this->settings) {

				$pidFile = file_put_contents(HC_LOCATION . '/lock.pid', getmypid() . PHP_EOL);

				if ($pidFile !== false) {

					echo 'Locked Application' . PHP_EOL;



					return true;

				}

			}



			return false;

		}

	}

