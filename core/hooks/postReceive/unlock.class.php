<?hh // decl


	namespace HC\Hooks\PostReceive;



	/**
	 * Class Unlock
	 * @package HC\Hooks\PostReceive
	 */

	class Unlock extends \HC\Hooks\PostReceive

	{



		/**
		 * @var bool|array
		 */

		protected $settings = false;



		/**
		 * @param bool|array $settings
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

			// If we have settings
			if ($this->settings) {

				if (is_file(HC_LOCATION . '/lock.pid')) {

					// Get the current lock file
					$contents = file_get_contents(HC_LOCATION . '/lock.pid');

					// If we got the contet
					if ($contents !== false) {

						// If it's already unlocked, say so
						if ($contents === 'Unlock') {

							echo 'Application Already Unlocked' . PHP_EOL;



							return true;

						} else {

							// Create the lock file that's unlocked
							$pidFile = file_put_contents(HC_LOCATION . '/lock.pid', 'Unlock');

							if ($pidFile !== false) {

								echo 'Unlocked Application' . PHP_EOL;



								return true;

							}

						}

					} else {

						// Create the lock file that's unlocked
						$pidFile = file_put_contents(HC_LOCATION . '/lock.pid', 'Unlock');

						if ($pidFile !== false) {

							echo 'Unlocked Application' . PHP_EOL;



							return true;

						}

					}

				} else {

					echo 'Application Was Never Locked' . PHP_EOL;



					return true;

				}



			}



			return false;

		}

	}

