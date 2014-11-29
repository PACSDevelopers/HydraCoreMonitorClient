<?hh // decl


	namespace HC\Hooks\PostReceive;



	/**
	 * Class UpdateComposer
	 * @package HC\Hooks\PostReceive
	 */

	class UpdateComposer extends \HC\Hooks\PostReceive

	{
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
            if(!is_file('/tmp/composer.phar')) {
                echo 'Installing Composer' . PHP_EOL;
                
                $fp = fopen('/tmp/composer.phar', 'w+');
                
                $ch = curl_init('https://getcomposer.org/composer.phar');
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                $result = curl_exec($ch);
                curl_close($ch);
                
                fclose($fp);

                if($result === false) {
                    echo 'Failed to install Composer' . PHP_EOL;
                    return true;
                }
            } else {
                echo 'Updating Composer' . PHP_EOL;
                $command = 'hhvm /tmp/composer.phar self-update &> /dev/null;';
                $output = [];
                exec($command, $output, $returnCode);
                
                if($returnCode !== 0) {
                    echo 'Failed to update Composer' . PHP_EOL;
                    return false;
                }
            }

            chmod('/tmp/composer.phar', 0777);
            chown('/tmp/composer.phar', 'www-data');
            
            echo 'Updating Dependencies' . PHP_EOL;
            
            $cwd = getcwd();
            
            chdir(HC_LOCATION);
            
            if(ENVIRONMENT === 'DEV') {
                $command = 'cd ' . HC_LOCATION . ' && hhvm -v ResourceLimit.SocketDefaultTimeout=30 -v Http.SlowQueryThreshold=30000 /tmp/composer.phar update --prefer-dist -n &> /dev/null;';
            } else {
                $command = 'cd ' . HC_LOCATION . ' && hhvm -v ResourceLimit.SocketDefaultTimeout=30 -v Http.SlowQueryThreshold=30000 /tmp/composer.phar update --prefer-dist -n --no-dev &> /dev/null;';
            }
            
            $output = [];
            exec($command, $output, $returnCode);
    
            chdir($cwd);
            
            if($returnCode === 0) {
                echo 'Updated Dependencies' . PHP_EOL;
                $command = 'hhvm ' . HC_LOCATION . '/hooks/post-receive.php --skip-hook-placement=HC\\\Hooks\\\PostReceive\\\UpdateComposer';
                passthru($command);
                return -1;
            } else {
                echo 'Failed to update dependencies' . PHP_EOL;
            }
            
            return false;
		}

	}
