<?hh
    try {

        if (!defined('HC_TEST_LOCATION')) {

            define('HC_TEST_LOCATION', dirname(__DIR__) . '/core');

        }

        require_once(HC_TEST_LOCATION . '/HydraCore.php');



        // Define settings just for travis ci


        require_once(HC_CORE_LOCATION . '/modules/data/site.class.php');

        $settings = $GLOBALS['HC_CORE']->getSite()->getSettings();



        $settings['database'] = [

            'engine'       => 'mysql',

            'host'         => '127.0.0.1',

            'username'     => 'travis',

            'password'     => '',

            'databasename' => 'HydraCore'

        ];



        $GLOBALS['HC_CORE']->setSite(new HC\Site($settings));



        require_once 'components/assertHTMLValidate.php';



        echo 'HydraCore Bootstrap Loaded' . PHP_EOL;

    } catch (Exception $e) {

        echo 'Bootstrap Failed to load: ' . $e->getMessage() . PHP_EOL;

        exit(1);

    }

