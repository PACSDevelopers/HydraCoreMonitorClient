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
            if(empty($globalSettings['database'])) {
                echo 'Processing Crons Offline' . PHP_EOL;
                $status = $this->offlineRun();
                echo 'Processed Crons Offline' . PHP_EOL;
                return $status;
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
            if(isset($options['ct'])) {
                echo 'Processing Crons Online Strict' . PHP_EOL;
                $status = $this->strictOnlineRun($db, $options);
                echo 'Processed Crons Online Strict' . PHP_EOL;

            } else {
                echo 'Processing Crons Online Soft' . PHP_EOL;
                $status = $this->softOnlineRun($db);
                echo 'Processed Crons Online Soft' . PHP_EOL;
            }

            return $status;
        }

        protected function softOnlineRun($db) {
            foreach ($this->settings as $key => $value) {
                $sendEmail = false;
                if(isset($value['email'])) {
                    if($value['email']) {
                        if($value['email'] === 'onfailure') {
                            $sendEmail = 2;
                        } else {
                            $sendEmail = true;
                        }
                    }
                }

                $curTime = microtime(true);
                $minTime = ($curTime - $value['microtime']);
                $minTime = $minTime + 1;
                $dateTokens = explode('.', $curTime);
                if(!isset($dateTokens[1])) {
                    $dateTokens[1] = 0;
                }

                $currentDate = date('Y-m-d H:i:s', $dateTokens[0]) . '.' . str_pad($dateTokens[1], 4, '0', STR_PAD_LEFT);

                $dateTokens = explode('.', $minTime);
                if(!isset($dateTokens[1])) {
                    $dateTokens[1] = 0;
                }

                $minDate = date('Y-m-d H:i:s', $dateTokens[0]) . '.' . str_pad($dateTokens[1], 4, '0', STR_PAD_LEFT);

                $result = $db->query('SELECT `C`.`id`, `C`.`status`, `C`.`lastRun`  FROM `HC_Cron` `C` WHERE `C`.`title` = ? AND `C`.`status` = ? AND `C`.`lastRun` <= STR_TO_DATE(?,?);', [$key, 1, $minDate, '%Y-%m-%d %H:%i:%s.%f']);
                if($result) {
                    $cronID = $result[0]['id'];
                    $result = $db->update('HC_Cron', $result[0], ['status' => 2]);
                    if($result) {
                        $start = microtime(true);

                        $hook = new $key($value);

                        if (method_exists($hook, 'run')) {

                            $cwd = getcwd();

                            ob_start();
                            if (!$hook->run()) {

                                chdir($cwd);

                                echo 'Hook Failed in ' . (microtime(true) - $start) . ' seconds: ' . PHP_EOL . $key . PHP_EOL;

                                $db->update('HC_Cron', ['id' => $cronID], ['status' => 1, 'lastRun' => $currentDate]);


                                $contents = ob_get_contents();
                                ob_end_clean();
                                echo $contents;
                                if(ERROR_ADDRESS && $sendEmail === true || $sendEmail === 2) {
                                    $email = new \HC\Email();
                                    $email->send(ERROR_ADDRESS, 'Cron Failed: ' . $key, nl2br($contents));
                                }
                                break;

                            }



                            echo 'Hook Success in ' . (microtime(true) - $start) . ' seconds: ' . PHP_EOL . $key . PHP_EOL;

                            chdir($cwd);

                            $db->update('HC_Cron', ['id' => $cronID], ['status' => 1, 'lastRun' => $currentDate]);

                            $contents = ob_get_contents();
                            ob_end_clean();
                            echo $contents;
                            if(ERROR_ADDRESS && $sendEmail === true) {
                                $email = new \HC\Email();
                                $email->send(ERROR_ADDRESS, 'Cron Success: ' . $key, nl2br($contents));
                            }

                        }
                    } else {
                        echo 'Cron: Skipped 2 '. $key . PHP_EOL;
                    }

                } else {
                    $date1 = $minDate;
                    $date2 = $db->read('HC_Cron', ['lastRun'], ['title' => $key])[0]['lastRun'];
                    echo 'Cron: Skipped '. $key . PHP_EOL;
                }

            }

            return true;
        }

        protected function strictOnlineRun($db, $options) {
            foreach ($this->settings as $key => $value) {
                if($value['microtime'] != $options['ct']) {
                    echo 'Cron: Skipped 3 '. $key . PHP_EOL;
                    continue;
                }

                $sendEmail = false;
                if(isset($value['email'])) {
                    if($value['email']) {
                        if($value['email'] === 'onfailure') {
                            $sendEmail = 2;
                        } else {
                            $sendEmail = true;
                        }
                    }
                }

                $curTime = microtime(true);
                $minTime = ($curTime - $value['microtime']);
                $minTime = $minTime + 1;
                $dateTokens = explode('.', $curTime);
                if(!isset($dateTokens[1])) {
                    $dateTokens[1] = 0;
                }

                $currentDate = date('Y-m-d H:i:s', $dateTokens[0]) . '.' . str_pad($dateTokens[1], 4, '0', STR_PAD_LEFT);

                $dateTokens = explode('.', $minTime);
                if(!isset($dateTokens[1])) {
                    $dateTokens[1] = 0;
                }

                $minDate = date('Y-m-d H:i:s', $dateTokens[0]) . '.' . str_pad($dateTokens[1], 4, '0', STR_PAD_LEFT);

                $result = $db->query('SELECT `C`.`id`, `C`.`status`, `C`.`lastRun`  FROM `HC_Cron` `C` WHERE `C`.`title` = ? AND `C`.`status` = ? AND `C`.`lastRun` <= STR_TO_DATE(?,?);', [$key, 1, $minDate, '%Y-%m-%d %H:%i:%s.%f']);
                if($result) {
                    $cronID = $result[0]['id'];
                    $result = $db->update('HC_Cron', $result[0], ['status' => 2]);
                    if($result) {
                        $start = microtime(true);

                        $hook = new $key($value);

                        if (method_exists($hook, 'run')) {

                            $cwd = getcwd();

                            ob_start();
                            if (!$hook->run()) {

                                chdir($cwd);

                                echo 'Hook Failed in ' . (microtime(true) - $start) . ' seconds: ' . PHP_EOL . $key . PHP_EOL;

                                $db->update('HC_Cron', ['id' => $cronID], ['status' => 1, 'lastRun' => $currentDate]);


                                $contents = ob_get_contents();
                                ob_end_clean();
                                echo $contents;
                                if(ERROR_ADDRESS && $sendEmail === true || $sendEmail === 2) {
                                    $email = new \HC\Email();
                                    $email->send(ERROR_ADDRESS, 'Cron Failed: ' . $key, nl2br($contents));
                                }
                                break;

                            }



                            echo 'Hook Success in ' . (microtime(true) - $start) . ' seconds: ' . PHP_EOL . $key . PHP_EOL;

                            chdir($cwd);

                            $db->update('HC_Cron', ['id' => $cronID], ['status' => 1, 'lastRun' => $currentDate]);

                            $contents = ob_get_contents();
                            ob_end_clean();
                            echo $contents;
                            if(ERROR_ADDRESS && $sendEmail === true) {
                                $email = new \HC\Email();
                                $email->send(ERROR_ADDRESS, 'Cron Success: ' . $key, nl2br($contents));
                            }

                        }
                    } else {
                        echo 'Cron: Skipped 2 '. $key . PHP_EOL;
                    }

                } else {
                    $date1 = $minDate;
                    $date2 = $db->read('HC_Cron', ['lastRun'], ['title' => $key])[0]['lastRun'];
                    echo 'Cron: Skipped '. $key . PHP_EOL;
                }

            }

            return true;
        }

	}
