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
                
            }
        }
    }
    $GLOBALS['HCMC_CORE'] = new Core();
