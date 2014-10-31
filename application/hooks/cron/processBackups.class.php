<?hh


  namespace HCMC\Hooks\Cron;

  /**
   * Class ProcessBackups
   * @package HC\Hooks\Cron
   */

  class ProcessBackups extends \HC\Hooks\Cron

  {



      /**
       * @var bool
       */

      protected $settings = [
          'path' => '/data/backups'
      ];
    
      protected $directory;


      /**
       * @param bool $settings
       */

      public function __construct($settings = [])

      {
          if(isset($globalSettings['backups'])) {
              $settings = $this->settings = \HC\Core::parseOptions($globalSettings['backups'], $this->settings);
          }
          
          if(is_array($settings)) {
              $this->settings = \HC\Core::parseOptions($settings, $this->settings);
          }
          
      }



      /**
       * @return bool
       */

      public function run()

      {
          echo 'Processing Backups' . PHP_EOL;
          $this->directory = new \HC\Directory();
          
          // Get available memory
          $memory = 100000;
          $fh = fopen('/proc/meminfo','r');
          while ($line = fgets($fh)) {
              $pieces = array();
              if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
                  $memory = $pieces[1];
                  break;
              }
          }
          fclose($fh);
          
          $memory = floor($memory / 1024 / 2);
          
          if($memory < 100) {
              $memory = 100;
          }
          
          $cores = 2;
          
          $output = exec('getconf _NPROCESSORS_ONLN');
          
          if(is_numeric($output)) {
              $output = (int)$output;
          }
          
          $cores = $cores / 2;
          
          $cwd = getcwd();
          chdir($this->settings['path']);
          
          $backupsDirectory = $this->getLatestBackup();
          if($backupsDirectory){
              $command = 'innobackupex --lock-wait-timeout=300 --parallel=' . $cores . ' --use-memory=' . $memory . 'MB ' . $this->settings['path'];
              $output = [];
              exec($command, $output, $returnCode);
              chdir($cwd);
              if($returnCode == 0) {
                  $newBackup = $this->getLatestBackup();
                  if($newBackup) {
                      $oldFile = $this->settings['path'] . '/' . $backupsDirectory;
                      $newFile = $this->settings['path'] . '/' . $newBackup;
                      
                      $old = parse_ini_file($oldFile . '/xtrabackup_checkpoints');
                      $new = parse_ini_file($newFile . '/xtrabackup_checkpoints');
                      if($old && $new && count($old) && count($new)) {
                          if($old['to_lsn'] === $new['to_lsn']) {
                              $status = $this->directory->delete($newFile);
                              if($status) {
                                  echo 'Processed Backups (no change)' . PHP_EOL;
                                  return true;
                              }
                          } else {
                              $latestBackup = $this->getLatestBackup();
                              // Prepare the backup
                              $command = 'innobackupex --lock-wait-timeout=300 --parallel=' . $cores . ' --use-memory=' . $memory . 'MB --apply-log ' . $this->settings['path'] . '/' . $latestBackup;
                              $output = [];
                              $line = exec($command, $output, $returnCode);
                              if($returnCode == 0) {
                                  $command = 'tar -cf - ' . $this->settings['path'] . '/' . $latestBackup . ' | snzip > ' . $this->settings['path'] . '/' . $latestBackup . '.tar.sz';
                                  $output = [];
                                  $line = exec($command, $output, $returnCode);
                                  if($returnCode == 0) {
                                      // Delete everything we don't need
                                      $file = file_get_contents($newFile . '/xtrabackup_checkpoints');
                                      if($file) {
                                          $status = $this->directory->delete($newFile);
                                          if($status) {
                                              mkdir($newFile);
                                              $status = file_put_contents($newFile . '/xtrabackup_checkpoints', $file);
                                              if($status) {
                                                  echo 'Processed Backups' . PHP_EOL;
                                                  return true;
                                              }
                                          }
                                      }
                                  }
                              }
                          }
                      }
                  }
              }
          } else {
              $command = 'innobackupex --lock-wait-timeout=300 --parallel=' . $cores . ' --use-memory=' . $memory . 'MB ' . $this->settings['path'];
              $output = [];
              exec($command, $output, $returnCode);
              chdir($cwd);
              if($returnCode == 0) {
                  $latestBackup = $this->getLatestBackup();
                  // Prepare the backup
                  $command = 'innobackupex --lock-wait-timeout=300 --parallel=' . $cores . ' --use-memory=' . $memory . 'MB --apply-log ' . $this->settings['path'] . '/' . $latestBackup;
                  $output = [];
                  exec($command, $output, $returnCode);
                  if($returnCode == 0) {
                      $command = 'tar -cf - ' . $this->settings['path'] . '/' . $latestBackup . ' | snzip > ' . $this->settings['path'] . '/' . $latestBackup . '.tar.sz';
                      $output = [];
                      $line = exec($command, $output, $returnCode);
                      if($returnCode == 0) {
                          // Delete everything we don't need
                          $newDir = $this->settings['path'] . '/' . $latestBackup;
                          $file = file_get_contents($newDir . '/xtrabackup_checkpoints');
                          if($file) {
                              $status = $this->directory->delete($newDir);
                              if($status) {
                                  mkdir($newDir);
                                  $status = file_put_contents($newDir . '/xtrabackup_checkpoints', $file);
                                  if($status) {
                                      echo 'Processed Backups New' . PHP_EOL;
                                      return true;
                                  }
                              }
                          }
                      }
                  }
              }
          }

          echo 'Processed Backups Failure' . PHP_EOL;
          return false;

      }
      
      public function getLatestBackup() {
          $backupsDirectory = $this->directory->get($this->settings['path']);
          $count = count($backupsDirectory);
          if($backupsDirectory && $count > 0){
              $directoriesByDate = [];
              foreach($backupsDirectory as $key => $value) {
                  if(is_array($value)) {
                      $newKey = date_create_from_format('Y-m-d_H-i-s', $key);
                      if($newKey) {
                          $directoriesByDate[$newKey->getTimestamp()] = $key;
                      }
                  }
              }

              ksort($directoriesByDate);
              end($directoriesByDate);
              $lastKey = key($directoriesByDate);
              
              return $directoriesByDate[$lastKey];
          }
          
          return false;
      }

  }
