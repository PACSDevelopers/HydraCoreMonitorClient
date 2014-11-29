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

				if (is_file(HC_LOCATION . '/lock.json')) {

					// Get the current lock file
					$contents = file_get_contents(HC_LOCATION . '/lock.json');

                    $contents = json_decode($contents, true);
                    
					// If we got the contet
					if ($contents !== false) {

						// If it's already unlocked, say so
						if ($contents['Status'] === 'Unlocked') {

							echo 'Application Already Unlocked' . PHP_EOL;



							return true;

						} else {
							return $this->unLock($contents);
						}

					} else {
                        $data = [
                            'PID' => getmypid(),
                            'Status' => 'Unlocked'
                        ];
                        
                        return $this->unLock($data);
					}

				} else {

					echo 'Application Was Never Locked' . PHP_EOL;

					return true;

				}



			}



			return false;

		}
        
        public function unLock($contents) {
            // Create the lock file that's unlocked
            $contents['Status'] = 'Unlocked';

            $lockFile = file_put_contents(HC_LOCATION . '/lock.json', json_encode($contents));
            chmod(HC_LOCATION . '/lock.json', 0777);
            
            if ($lockFile !== false) {

                echo 'Unlocked Application' . PHP_EOL;

                return true;

            }
            
            return false;
        }

	}

