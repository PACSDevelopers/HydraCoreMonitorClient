<?php
    
    namespace HCMC;
    
    /**
     * Class Core
     *
     * This class pulls all the settings and creates the site object.
     */
    class Core
    {
        protected $settings = [];
    
        // Construct the core based on settings
        public function __construct($settings = [])
        {
            $this->settings = \HC\Core::parseOptions($settings, $this->settings);
    
            spl_autoload_register('\HCMC\Core::autoLoader');
    
            return true;
        }
    
        /**
         * @param string $class
         */
        public static function autoLoader($class)
        {
            switch ($class) {
                /* Usable classes */
                case 'HCMC\Authenticator':
                    require_once(HC_APPLICATION_LOCATION . '/modules/data/authenticator.class.php');
                    break;
                case 'HCMC\Stats':
                    require_once(HC_APPLICATION_LOCATION . '/modules/data/stats.class.php');
                    break;

                /* Hooks */
                case 'HCMC\Hooks\Cron\ProcessBackups':
                    require_once(HC_APPLICATION_LOCATION . '/hooks/cron/processBackups.class.php');
                    break;
                case 'HCMC\Hooks\Cron\ProcessActions':
                    require_once(HC_APPLICATION_LOCATION . '/hooks/cron/processActions.class.php');
                    break;
            }
        }
    }
    $GLOBALS['HCMC_CORE'] = new Core();
