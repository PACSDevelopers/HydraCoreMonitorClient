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
                
                $data = [];
                
                if(is_file(HC_LOCATION . '/lock.json')) {
                    $data = json_decode(file_get_contents(HC_LOCATION . '/lock.json'), true);
                    if($data) {
                        if($data['Status'] === 'Locked') {
                            echo 'Application Already Locked' . PHP_EOL;
                        }
                    } else {
                        $data = [];
                    }
                }

                $data['PID'] = getmypid();
                $data['Status'] = 'Locked';

                $options = getopt('', ['skip-hook-placement::', 'start-rev::', 'end-rev::']);
                if(isset($options['skip-hook-placement'])) {
                    foreach($this->settings as $key => $value) {
                        unset($this->settings[$key]);
                        if($key === $options['skip-hook-placement']) {
                            break;
                        }
                    }
                }

                if(isset($options['start-rev'])) {
                    $data['From Commit'] = $options['start-rev'];
                }

                if(isset($options['end-rev'])) {
                    $data['To Commit'] = $options['end-rev'];
                }
                
                if((isset($data['To Commit']) ||isset($data['From Commit'])) && (defined('REPO_NAME') && defined('REPO_USER')) && class_exists('\Github\Client')) {
                    $globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();
                    if(isset($globalSettings['keys']) && isset($globalSettings['keys']['github'])) {
                        $client = new \Github\Client();
                        $client->authenticate($globalSettings['keys']['github'], NULL, \Github\Client::AUTH_HTTP_TOKEN);
                        if(isset($data['To Commit'])) {
                            $toCommit = $client->api('repo')->commits()->show(REPO_USER, REPO_NAME, $data['To Commit']);
                            if($toCommit && $toCommit['commit'] && $toCommit['commit']['author']) {
                                $data['To Message'] = $toCommit['commit']['message'];
                                $data['To User'] = $toCommit['commit']['author']['name'];
                            }
                        }

                        if(isset($data['From Commit'])) {
                            $fromCommit = $client->api('repo')->commits()->show(REPO_USER, REPO_NAME, $data['From Commit']);
                            if($fromCommit && $fromCommit['commit'] && $fromCommit['commit']['author']) {
                                $data['From Message'] = $fromCommit['commit']['message'];
                                $data['From User'] = $fromCommit['commit']['author']['name'];
                            }
                        }
                    }
                }
                
                $lockFile = file_put_contents(HC_LOCATION . '/lock.json', json_encode($data));
                chmod(HC_LOCATION . '/lock.json', 0777);

				if ($lockFile !== false) {

					echo 'Locked Application' . PHP_EOL;
					return true;

				}

			}



			return false;

		}

	}

