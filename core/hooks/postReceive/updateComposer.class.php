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
                $composer = fopen('https://getcomposer.org/composer.phar', 'r');

                $bytesWritten = file_put_contents('/tmp/composer.phar', $composer);

                fclose($composer);

                if($bytesWritten === false) {
                    echo 'Failed to install Composer' . PHP_EOL;
                    return true;
                }
            } else {
                echo 'Updating Composer' . PHP_EOL;
                $command = 'hhvm /tmp/composer.phar self-update';
                passthru($command, $returnCode);
                
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
            $command = 'cd ' . HC_LOCATION . ' && hhvm /tmp/composer.phar update';
    
            passthru($command, $returnCode);
    
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
