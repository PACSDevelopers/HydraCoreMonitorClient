<?hh


  namespace HCMC\Hooks\Cron;

  /**
   * Class ProcessActions
   * @package HC\Hooks\Cron
   */

  class ProcessActions extends \HC\Hooks\Cron

  {
      protected $settings = [];

      /**
       * @param bool $settings
       */

      public function __construct($settings = [])

      {
          if(is_array($settings)) {
              $this->settings = \HC\Core::parseOptions($settings, $this->settings);
          }
          
      }



      /**
       * @return bool
       */

      public function run()

      {
          echo 'Processing Actions' . PHP_EOL;

          if(file_exists(HC_TMP_LOCATION . '/actions/currentActions.sh')) {
              echo 'Skipped Processing Actions' . PHP_EOL;
              return true;
          }


          if(!is_dir(HC_TMP_LOCATION)) {
              mkdir(HC_TMP_LOCATION, 0777);
          }

          if(!is_dir(HC_TMP_LOCATION . '/actions')) {
              mkdir(HC_TMP_LOCATION . '/actions', 0777);
          }
          
          
          echo 'Checking Actions' . PHP_EOL;
          
          $haveAction = false;
          $script = '';
          $newLine = "\n";

          if(file_exists(HC_TMP_LOCATION . '/actions/updateRequested')) {
              $haveAction = true;
              $script .= $newLine . 'apt-get -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" update -y --force-yes';
              $script .= $newLine . 'apt-get -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" dist-upgrade -y --force-yes';
              $script .= $newLine . 'apt-get -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" clean -y --force-yes';
              $script .= $newLine . 'apt-get -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" autoremove -y --force-yes';
              unlink(HC_TMP_LOCATION . '/actions/updateRequested');
          }

          if(file_exists(HC_TMP_LOCATION . '/actions/rebootRequested')) {
              $haveAction = true;
              $script .= $newLine . 'reboot';
              unlink(HC_TMP_LOCATION . '/actions/rebootRequested');
              
              if(file_exists(HC_TMP_LOCATION . '/actions/restartRequested')) {
                    unlink(HC_TMP_LOCATION . '/actions/restartRequested');
              }
          } else if(file_exists(HC_TMP_LOCATION . '/actions/restartRequested')) {
              $haveAction = true;
              $script .= $newLine . 'service hhvm restart';
              unlink(HC_TMP_LOCATION . '/actions/restartRequested');
          }
          
          if($haveAction) {
              $script = '#!/bin/bash' . $newLine . 'export DEBIAN_FRONTEND=noninteractive;' . $newLine . 'rm ' . HC_TMP_LOCATION . '/actions/currentActions.sh;' . $script;
              file_put_contents(HC_TMP_LOCATION . '/actions/currentActions.sh', $script);
              chmod(HC_TMP_LOCATION . '/actions/currentActions.sh', 700);
              
              $output = [];
              $line = exec('(sleep 5 && bash ' . HC_TMP_LOCATION . '/actions/currentActions.sh >> /tmp/c-cron.log) &', $output, $returnCode);
          }
          
          echo 'Processed Actions' . PHP_EOL;
          return true;
      }
  }
