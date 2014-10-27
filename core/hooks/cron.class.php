<?hh // decl


	namespace HC\Hooks;



	/**
	 * Class Cron
	 * @package HC\Hooks
	 */

	class Cron extends \HC\Core

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

					if (isset($globalSettings['hooks']['cron'])) {

						if (is_array($globalSettings['hooks']['cron'])) {

							$settings = $this->parseOptions($settings, $globalSettings['hooks']['cron']);

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
            $db = new \HC\DB();
            
            $options = getopt('', 'ct:');
            if(!isset($options['ct'])) {
                echo 'Hook Failed: No Cron Timer Option ' . PHP_EOL;
                return;
            }
                        
            $curTime = microtime(true);
            $dateTokens = explode('.', $curTime);
            if(!isset($dateTokens[1])) {
                $dateTokens[1] = 0;
            }
            $currentDate = date('Y-m-d H:i:s', $dateTokens[0]) . '.' . str_pad($dateTokens[1], 4, '0', STR_PAD_LEFT);
            
			foreach ($this->settings as $key => $value) {
                if($value['microtime'] != $options['ct']) {
                    echo 'Cron: Skipped 3 '. $key . PHP_EOL;
                    continue;
                }
                                
                $result = $db->query('SELECT `C`.`id`, `C`.`lastRun` FROM `HC_Cron` `C` WHERE `C`.`title` = ?;', [$key]);
                if($result) {                    
                    $result = $db->update('HC_Cron', ['id' => $result[0]['id'], 'lastRun' => $result[0]['lastRun']], ['lastRun' => $currentDate]);
                    if($result) {
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
                    } else {
                        echo 'Cron: Skipped 2 '. $key . PHP_EOL;
                    }
                    
                } else {
                    echo 'Cron: Skipped '. $key . PHP_EOL;
                }
                
			}

		}

	}
