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
			// Parse global / local settings
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['hooks'])) {

				if (is_array($globalSettings['hooks'])) {

					if (isset($globalSettings['hooks']['cron'])) {

						if (is_array($globalSettings['hooks']['cron'])) {

                            $this->settings = $this->parseOptions($this->settings, $globalSettings['hooks']['cron']);

						}

					}

				}

			}

            $this->settings = $settings = $this->parseOptions($settings, $this->settings);
		}



		/**
		 * Runs hooks
		 */

		public function run()

		{
            $globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();
            if(!count($globalSettings['database'])) {
                return $this->offlineRun();
            } else {
                return $this->onlineRun();
            }
		}
        
        public function offlineRun() {
            $options = getopt('', 'ct:');
            if(!isset($options['ct'])) {
                echo 'Hook Failed: No Cron Timer Option ' . PHP_EOL;
                return;
            }

            foreach ($this->settings as $key => $value) {
                if($value['microtime'] != $options['ct']) {
                    echo 'Cron: Skipped 3 '. $key . PHP_EOL;
                    continue;
                }

                $start = microtime(true);

                $hook = new $key($value);

                if (method_exists($hook, 'run')) {

                    $cwd = getcwd();

                    if (!$hook->run()) {

                        chdir($cwd);

                        echo 'Hook Failed in ' . (microtime(true) - $start) . ' seconds: ' . PHP_EOL . $key . PHP_EOL;

                        break;

                    }

                    echo 'Hook Success in ' . (microtime(true) - $start) . ' seconds: ' . PHP_EOL . $key . PHP_EOL;

                    chdir($cwd);

                }
            }
        }
        
        public function onlineRun() {
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
                        $start = microtime(true);

                        $hook = new $key($value);

                        if (method_exists($hook, 'run')) {

                            $cwd = getcwd();

                            if (!$hook->run()) {

                                chdir($cwd);

                                echo 'Hook Failed in ' . (microtime(true) - $start) . ' seconds: ' . PHP_EOL . $key . PHP_EOL;

                                break;

                            }

                            echo 'Hook Success in ' . (microtime(true) - $start) . ' seconds: ' . PHP_EOL . $key . PHP_EOL;

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
