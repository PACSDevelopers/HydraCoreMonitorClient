<?hh // decl


	namespace HC\Hooks\PostReceive;



	/**
	 * Class InvalidateCache
	 * @package HC\Hooks\PostReceive
	 */

	class InvalidateCache extends \HC\Hooks\PostReceive

	{

		/**
		 * @var \HC\Directory
		 */

		protected $directoryHandler;

		/**
		 * @var array
		 */

		protected $settings;



		/**
		 * @param array $settings
		 */

		public function __construct($settings = [])

		{

			$this->settings = $settings;

		}



		/**
		 * @return bool
		 */

		public function run()

		{
			$cache = new \HC\Cache();
			$db = new \HC\DB();
			$result1 = $cache->deleteAll();
			$result2 = $db->provideDefaultTableData();

			return ($result && $result2);
		}

	}
