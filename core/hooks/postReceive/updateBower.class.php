<?hh // decl


	namespace HC\Hooks\PostReceive;



	/**
	 * Class UpdateBower
	 * @package HC\Hooks\PostReceive
	 */

	class UpdateBower extends \HC\Hooks\PostReceive

	{
		/**
		 * @var array
		 */

		protected $settings;



        protected $jsComponents = [];
        protected $cssComponents = [];
        
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
            echo 'Updating Components' . PHP_EOL;

            $cwd = getcwd();

            chdir(HC_LOCATION);

            if (0 == posix_getuid()) {
                $command = 'cd ' . HC_LOCATION . ' && npm update -g --unsafe-perm npm --quiet';
                passthru($command, $returnCode);
            } else {
                $returnCode = 0;
            }
            
            if($returnCode === 0) {
                if(file_exists(HC_LOCATION . '/package.json')) {
                    echo 'Installing NPM Packages' . PHP_EOL;
                    $command = 'cd ' . HC_LOCATION . ' && npm install --unsafe-perm --quiet &> /dev/null;';
                    $output = [];
                    exec($command, $output, $returnCode);
                    
                    if($returnCode === 0) {
                        echo 'Installed NPM Packages' . PHP_EOL;
                        echo 'Updating NPM Packages' . PHP_EOL;
                        $command = 'cd ' . HC_LOCATION . ' && npm update --unsafe-perm --quiet --silent &> /dev/null;';
                        $output = [];
                        exec($command, $output, $returnCode);
                        if($returnCode === 0) {
                            echo 'Updated NPM Packages' . PHP_EOL;
                        }
                    }
                } else {
                    echo 'Linked Components (none)' . PHP_EOL;
                    return true;
                }

                if($returnCode === 0) {
                    $tempFile = '<head>
<!-- bower:js -->
<!-- endbower -->
<!-- bower:css -->
<!-- endbower -->
</head>';

                    if(!is_dir(HC_TMP_LOCATION)) {
                        mkdir(HC_TMP_LOCATION, 0777);
                    }

                    if(!is_dir(HC_TMP_LOCATION . '/bower/')) {
                        mkdir(HC_TMP_LOCATION . '/bower/', 0777);
                    }

                    if(!file_exists(HC_TMP_LOCATION . '/bower/bower.html')) {
                        file_put_contents(HC_TMP_LOCATION . '/bower/bower.html', $tempFile);
                    }

                    if(ENVIRONMENT === 'DEV') {
                        $command = 'cd ' . HC_LOCATION . ' && ' . HC_LOCATION . '/node_modules/bower/bin/bower install --allow-root --quiet &> /dev/null;';
                    } else {
                        $command = 'cd ' . HC_LOCATION . ' && ' . HC_LOCATION . '/node_modules/bower/bin/bower install --allow-root --production --quiet &> /dev/null;';
                    }

                    $output = [];
                    exec($command, $output, $returnCode);

                    if($returnCode === 0) {
                        echo 'Updated Components' . PHP_EOL;

                        echo 'Linking Components' . PHP_EOL;

                        $command = 'cd ' . HC_LOCATION . ' && ' . HC_LOCATION . '/node_modules/grunt-cli/bin/grunt wiredep --quiet &> /dev/null;';
                        $output = [];
                        exec($command, $output, $returnCode);

                        if($returnCode === 0) {
                            $this->link();
                            echo 'Linked Components' . PHP_EOL;
                            return true;
                        } else {
                            chdir(HC_LOCATION);
                            echo 'Failed to Link Components' . PHP_EOL;
                            return false;
                        }
                    }
                }                
            }

            chdir(HC_LOCATION);
            echo 'Failed to update Components' . PHP_EOL;
            
            
            return false;
		}

        public function link() {
            $html = str_replace('../../public', '', file_get_contents(HC_TMP_LOCATION . '/bower/bower.html'));
            $object = new \DomDocument();
            $object->loadHTML($html);
            $this->parseHTMLNode($object);
            
            $bowerHH = '<?hh
$hydraCoreSettings[\'pages\'][\'components\'] = [\'css\' => ' . var_export($this->cssComponents, true) . ', \'js\' => ' . var_export($this->jsComponents, true) . '];';

            file_put_contents(HC_LOCATION . '/settings/bower.hh', $bowerHH);
            return true;
        }
        
        protected function parseHTMLNode($node, $parent = null) {
            switch($node->nodeName) {
                case 'script':
                    $this->jsComponents[] = $node->getAttribute('src');
                    break;
                case 'link':
                    $this->cssComponents[] = $node->getAttribute('href');
                    break;
            }
            
            if($node->hasChildNodes()) {
                foreach ($node->childNodes as $childNode) {
                    $childNode = $this->parseHTMLNode($childNode, $node);
                }
            }
            
            return true;
        }
	}
