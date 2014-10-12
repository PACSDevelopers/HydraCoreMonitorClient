<?hh // decl

namespace HC;

/**
 * Class DB
 */
class DB extends Core
{
    // Setup class public variables

    // Setup class protected variables
    /**
     * @var \PDO|null $connection
     */
    protected $connection = null;

    /**
     * @var int
     */
    protected $defaultFetchType = 2;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var \HC\Cache|null
     */
    protected $cache = null;

    /**
     * @var \HC\Encryption|null
     */
    protected $encryption = null;

    /**
     * @var string
     */
    protected $uniqueHash = '';

    /**
     * @var array
     */
    protected $tables = [];

    protected $nonCPUBoundTime = 0;
    protected $numberOfQueries = 0;
    protected $numberOfSelects = 0;
    protected $numberOfCacheHits = 0;


    static protected $joinTypes = [
      'I' => 'INNER JOIN',
      'L' => 'LEFT JOIN',
      'R' => 'RIGHT JOIN',
      'J' => 'JOIN'
    ];

    // Setup Public Functions

    /**
     * Database Constructor
     * This sets up the database connection, and selects the database
     *
     * @param array $settings
     */
    public function __construct($settings = [])
    {

        // Parse global / local options
        $globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

        if(!count($globalSettings['database'])) {
            throw new \Exception('Unable to find database settings');
        }

        if(!(bool)count(array_filter(array_keys($globalSettings['database']), 'is_string'))) {
            $found = false;
            if(isset($settings['name'])) {
                foreach($globalSettings['database'] as $database) {
                    if(isset($database['name']) && ($database['name'] === $settings['name'])) {
                        $globalSettings['database'] = $database;
                        $found = true;
                    }
                }

                if(!$found) {
                    throw new \Exception('Unable to find database settings for: ' . $settings['name']);
                }
            }

            if(!$found) {
                $globalSettings['database'] = $globalSettings['database'][0];
            }
        }

        $settings = $this->parseOptions($settings, $globalSettings['database']);

        // Parse default options
        $settings = $this->parseOptions($settings, ['name' => false, 'timeout' => 60, 'useCache' => false, 'persistant' => false]);

        $this->settings = $settings;
        $this->connect();

        if($settings['useCache']) {
            $this->encryption = new \HC\Encryption();
            $this->uniqueHash = $this->encryption->hash(serialize($settings), ['salt' => 'SQL_CACHE', 'hashlength' => 0]);
            $this->cache = new \HC\Cache();
            if($this->cache->exists($this->uniqueHash . 'SHOW_TABLES')) {
                $this->tables = $this->cache->select($this->uniqueHash . 'SHOW_TABLES');
            } else {
                $tables = $this->query('SHOW TABLES', [], \PDO::FETCH_NUM, true);
                if($tables) {
                    $this->tables = $tables;
                    $this->cache->insert($this->uniqueHash . 'SHOW_TABLES', $tables);
                }
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function connect()
    {
        if ($this->connection === null) {
            if (!isset($this->settings['engine'])) {
                throw new \Exception('You must select a database driver');
            }

            try {
                // Create connection from settings defined
                $this->connection = new \PDO($this->settings['engine'] . ':dbname=' . $this->settings['databasename'] . ';host=' . $this->settings['host'], $this->settings['username'], $this->settings['password']);
                $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(\PDO::ATTR_TIMEOUT, $this->settings['timeout']);
                $this->connection->setAttribute(\PDO::ATTR_PERSISTENT, $this->settings['persistant']);
                $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $this->defaultFetchType);
                $this->connection->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES ' . DB_ENCODING);
            } catch (\PDOException $exception) {
                // Trigger the error handler, based on exception details
                Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), 0, $exception->getTrace());
            }

            return true;
        }

        return false;
    }

    protected function cleanUpQuery($sql) {
      if(ENVIRONMENT === 'PRODUCTION') {
        // Clean up the statement
        $sql = trim($sql);
        $lastChar = mb_substr($sql, -1);
        if($lastChar != ';') {
            $sql .= ';';
        }
      }

      return $sql;
    }

    /**
     * @param string $sql
     * @param array $values
     * @param integer $fetchType
     *
     * @return false|array
     * @throws \Exception
     */
    public function query($sql, $values = [], $fetchType = -1, $bypassCache = false, $tableModifications = false) {
        // Clean up the query
        $sql = $this->cleanUpQuery($sql);

        // If this is a select
        $isSelect = $this->isSelect($sql);
        if($isSelect) {
          // Check the cache
          if(!$bypassCache) {
              if($this->settings['useCache']) {
                  return $this->cachedQuery($sql, $values, $fetchType);
              }
          }
          $tableModifications = false;
        } else {
          if(!$tableModifications) {
            // Prepare the table modifications
            $tableModifications = $this->prepareTableModification($sql);
          }
        }

        // Run the query
        try {
            $timeBefore = microtime(true);
            $query = $this->connection->prepare($sql);
            $success = $query->execute($values);
            $this->nonCPUBoundTime += (microtime(true) - $timeBefore);
        } catch (\PDOException $exception) {
            // Trigger the error handler, based on exception details
            Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), 0, $exception->getTrace());
        }

        // If we have any table modifications, run them
        if($tableModifications) {
            $this->executeTableModification($tableModifications);
        }

        $result = false;

        // Set the fetch type if defined
        if ($fetchType == -1) {
            $fetchType = & $this->defaultFetchType;
        }

        // Try get the rows
        try {
            $result = $query->fetchAll($fetchType);
        } catch (\PDOException $exception) {
            // There was no rows, use status values
        }

        if (!is_array($result)) {
            if (isset($success)) {
                $result = $success;
            }
        }

        $this->modifyNumberOfQueries(1);
        if($isSelect) {
          $this->modifyNumberOfSelects(1);
        }

        $query = null;
        unset($query);

        if (empty($result)) {
            return false;
        }

        return $result;
    }

    public function beginTransaction() {
      return $this->connection->beginTransaction();
    }

    public function commit() {
      return $this->connection->commit();
    }

    public function rollback() {
      return $this->connection->rollback();
    }

    private function cachedQuery($sql, $values = [], $fetchType = -1, $skipDataCheck = false)
    {
        $tables = [];

        foreach($this->tables as $actualTable) {
            if (mb_strpos($sql, '`' . $actualTable[0] . '`') !== false) {
                array_push($tables, $this->quote($actualTable[0]));
            }
        }

        if(!empty($tables)) {
            // Get hashed query
            $hash = $this->encryption->hash($this->uniqueHash . $sql . json_encode($values), ['salt' => 'HC_QUERY', 'hashlength' => 0]);

            // Check the cache
            if(!($this->cache->exists($hash) && $this->cache->exists($hash . '-t'))) {
                // We don't have it in cache, run the query, cache and return it
                $results = $this->query($sql, $values, $fetchType, true);
                $this->cache->insert($hash . '-t', time());
                $this->cache->insert($hash, $results);
                return $results;
            } else {
                // We have the cached results
                $time = $this->cache->select($hash . '-t');

                if(!$time) {
                    $time = 0;
                }

                if(!$skipDataCheck) {

                    // Figure out if the data has changed
                    $results = $this->query('SELECT COUNT(`name`) FROM `HC_Tables` WHERE `name` IN(' . implode(',', $tables)  . ') AND `lastUpdated` > :now;', ['now' => $time], \PDO::FETCH_NUM, true);

                    if($results) {
                        if($results[0][0] > 0) {
                            // The data has changed, run the query, cache and return it
                            $results = $this->query($sql, $values, $fetchType, true);
                            $this->cache->insert($hash . '-t', time());
                            $this->cache->insert($hash, $results);
                            return $results;
                        }
                    }
                }

                $this->modifyNumberOfCacheHits(1);
                // The data has not changed, we can return the cached results
                return $this->cache->select($hash);
            }
        } else {
            // We can't cache this as we don't have the table listed, return non cached results
            return $this->query($sql, $values, $fetchType, true);
        }

        return false;
    }

    private function isSelect($sql) {
      return (bool)preg_match('/SELECT/mi', $sql);
    }

    private function prepareTableModification($sql) {
        $shouldReturn = false;
        $returnSQL = [];

        foreach($this->tables as $actualTable) {
            if (mb_strpos($sql, '`' . $actualTable[0] . '`') !== false) {
                $shouldReturn = true;
                array_push($returnSQL, [
                        'UPDATE `HC_Tables` SET `lastUpdated`= :lastUpdated WHERE `name` = :tableName AND `lastUpdated` < :lastUpdated;',
                        [
                            'tableName' => $actualTable[0]
                        ]
                    ]
                );
            }
        }

        if($shouldReturn) {
            return $returnSQL;
        }

        return false;
    }

    private function executeTableModification($modifications) {
        if($modifications) {
            if(is_array($modifications)) {
                $time = time();
                $db = new DB(['useCache' => false]);
                $db->beginTransaction();
                foreach($modifications as $modification) {
                    $modification[1]['lastUpdated'] = $time;
                    $db->query($modification[0], $modification[1], -1, true, true);
                }
                $db->commit();
                $db = null;
                unset($db);

                return true;
            }
        }

        return false;
    }

    public function provideDefaultTableData() {
      $this->beginTransaction();
      foreach($this->tables as $actualTable) {
          $this->query('INSERT INTO `HC_Tables` (name, lastUpdated) VALUES (:tableName, :lastUpdated) ON DUPLICATE KEY UPDATE lastUpdated=:lastUpdated;',
          [
              'tableName' => $actualTable[0],
              'lastUpdated' => 0
          ], -1, true, true);
      }

      $this->commit();

      return true;
    }


    /**
     * @param int|bool $fetchType
     *
     * @return bool
     */
    public function setDefaultFetchType($fetchType = false)
    {
        if($fetchType === false) {
            $fetchType = \PDO::FETCH_ASSOC;
        }

        $this->defaultFetchType = $fetchType;
        try {
            $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $this->defaultFetchType);
        } catch (\PDOException $exception) {
            // Trigger the error handler, based on exception details
            Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), 0, $exception->getTrace());
        }

        return true;
    }

    /**
     * @param string $table
     * @param array $options
     * @return array|bool
     */
    public function write($table, $options = [])
    {
        $optionCount = count($options);
        if ($optionCount > 0) {
            $startSQL = 'INSERT INTO `' . $table . '` (';
            $endSQL = ' VALUES (';
            $values = [];
            $count = 0;
            foreach ($options as $optionKey => $optionValue) {
                $count++;
                $startSQL .= ' `' . $optionKey . '`';
                $endSQL .= '?';
                if ($count != $optionCount) {
                    $startSQL .= ',';
                    $endSQL .= ',';
                }
                $values[] = $optionValue;
            }
            $endSQL .= ')';
            $startSQL .= ')';

            return $this->query($startSQL . $endSQL . ';', $values);
        }

        return false;
    }

    /**
     * @param string $table
     * @param array $options
     * @return array|bool
     */
    public function delete($table, $options = [])
    {
        $optionCount = count($options);
        if ($optionCount > 0) {
            $startSQL = 'DELETE FROM ' . $table .' WHERE';
            $values = [];
            $count = 0;
            foreach ($options as $optionKey => $optionValue) {
                $count++;

                if (is_array($optionValue)) {
                    $startSQL .= ' `' . $optionKey . '` IN (' . rtrim(str_repeat('?,', count($optionValue)), ',')  . ')';

                    foreach ($optionValue as $value) {
                        $values[] = $value;
                    }
                } else {
                    $startSQL .= ' `' . $optionKey . '` = ?';
                    $values[] = $optionValue;
                }

                if ($count != $optionCount) {
                    $startSQL .= ' AND ';
                }
            }
            $startSQL .= ';';
            return $this->query($startSQL, $values);
        }

        return false;
    }

    /**
     * @param string $table
     * @param array $options
     * @param array $data
     *
     * @return array|bool
     */
    public function update($table, $options = [], $data = [])
    {
        $optionCount = count($options);
        $dataCount = count($data);
        if (($optionCount > 0) && ($dataCount > 0)) {
            $startSQL = 'UPDATE `' . $table . '` SET ';
            $values = [];
            $count = 0;
            foreach ($data as $dataKey => $dataValue) {
                $count++;
                $startSQL .= ' `' . $dataKey . '` = ?';
                if ($count != $dataCount) {
                    $startSQL .= ',';
                }
                $values[] = $dataValue;
            }
            $startSQL .= ' WHERE';
            $count = 0;
            foreach ($options as $optionKey => $optionValue) {
                $count++;

                if (is_array($optionValue)) {
                    $startSQL .= ' `' . $optionKey . '` IN (' . rtrim(str_repeat('?,', count($optionValue)), ',')  . ')';

                    foreach ($optionValue as $value) {
                        $values[] = $value;
                    }
                } else {
                    $startSQL .= ' `' . $optionKey . '` = ?';
                    $values[] = $optionValue;
                }

                if ($count != $optionCount) {
                    $startSQL .= ' AND ';
                }
            }
            $startSQL .= ';';
            return $this->query($startSQL, $values);
        }

        return false;
    }

    /**
     * @param string $table
     * @param array $options
     * @param int|bool $limit
     * @return array|bool
     */
    public function read($table, $data = [], $options = [], $startLimit = false, $endLimit = false)
    {
        $sql = 'SELECT ';
        $dataCount = count($data);
        if($dataCount) {
          $count = 0;
          foreach($data as $key => $value) {
            $count++;
            if(is_int($key)) {
              $value = explode('.', $value, 2);
              if(count($value) > 1) {
                $sql .= '`' . $value[0] . '`.`' . $value[1] . '`';
              } else {
                $sql .= '`' . $value[0] . '`';
              }
            } else {
              $key = explode('.', $key, 2);
              if(count($key) > 1) {
                $sql .=  '`' . $key[0] . '`.`' . $key[1] . '` as `' . $value . '`';
              } else {
                $sql .=  '`' . $key[0] . '` as `' . $value . '`';
              }
            }

            if ($count != $dataCount) {
                $sql .= ', ';
            }
          }
        } else {
          $sql .= '*';
        }

        if(is_array($table)) {
          $isFirst = true;
          foreach($table as $key => $value) {
            if($isFirst) {
              $sql .= ' FROM `' . $key . '` `' . $value . '`';
              $isFirst = false;
            } else {
              $key = explode('.', $key, 3);
              if(count($key) === 3) {
                $sql .= ' ' . self::$joinTypes[$key[0]] . ' `' . $key[2] . '` `' . $key[1] . '` ON (';
              } else {
                $sql .= ' ' . self::$joinTypes['J'] . ' `' . $key[1] . '` `' . $key[0] . '` ON (';
              }

              $count = 0;
              $valueCount = count($value);
              foreach($value as $key2 => $value2) {
                $count++;
                $key2 = explode('.', $key2, 2);
                $value2 = explode('.', $value2, 2);
                $sql .= '`' . $key2[0] . '`.`'. $key2[1] . '` = `' . $value2[0] . '`.`'. $value2[1] . '`';
                if($count != $valueCount) {
                  $sql .= ' AND ';
                }
              }

              $sql .= ')';
            }
          }
        } else {
          $sql .= ' FROM `' . $table . '`';
        }




        $values = [];
        $optionCount = count($options);
        if ($optionCount) {
            $sql .= ' WHERE';
            $count = 0;
            foreach ($options as $optionKey => $optionValue) {
                $count++;
                $optionKey = explode('.', $optionKey, 2);
                if(count($optionKey) > 1) {
                  $optionKey = '`' . $optionKey[0] . '`.`' . $optionKey[1] . '`';
                } else {
                  $optionKey = '`' . $optionKey[0] . '`';
                }

                if (is_array($optionValue)) {
                    $sql .= ' ' . $optionKey . ' IN (' . rtrim(str_repeat('?,', count($optionValue)), ',')  . ')';

                    foreach ($optionValue as $value) {
                        $values[] = $value;
                    }
                } else {
                    $sql .= ' ' . $optionKey . ' = ?';
                    $values[] = $optionValue;
                }

                if ($count != $optionCount) {
                    $sql .= ' AND ';
                }
            }
        }

        if(is_int($startLimit) && is_int($endLimit)) {
            if(($startLimit === 0 && $endLimit === 0) || ($startLimit > $endLimit)) {
              return false;
            }

            $sql .= ' LIMIT ' . $startLimit . ',' . $endLimit;
        }

        return $this->query($sql .';', $values);
    }

    public function getLastID(){
        return $this->connection->lastInsertId();
    }

    public function quote($var){
        return $this->connection->quote($var);
    }

    /**
     * Database Destructor
     * This closes the database connection, and unsets variables
     */
    public function __destruct()
    {
        // Unset the connection
        $this->connection = null;
        $this->defaultFetchType = null;
        $this->settings = null;
        $this->updateSiteProperties();
        $this->nonCPUBoundTime = null;
        $this->numberOfQueries = null;
        $this->numberOfCacheHits = null;
        $this->numberOfSelects = null;

    }

    public function updateSiteProperties() {
      if(ENVIRONMENT !== 'PRODUCTION') {
        $GLOBALS['HC_CORE']->getSite()->addNonCPUBoundTime($this->nonCPUBoundTime);
        $this->nonCPUBoundTime = 0;
        $GLOBALS['HC_CORE']->getSite()->addNumberOfQueries($this->numberOfQueries);
        $this->numberOfQueries = 0;
        $GLOBALS['HC_CORE']->getSite()->addNumberOfSelects($this->numberOfSelects);
        $this->numberOfSelects = 0;
        $GLOBALS['HC_CORE']->getSite()->addNumberOfCacheHits($this->numberOfCacheHits);
        $this->numberOfCacheHits = 0;
        return true;
      }

      return false;
    }

    public function modifyNumberOfQueries($modifier) {
      if(ENVIRONMENT !== 'PRODUCTION') {
        $this->numberOfQueries = $this->numberOfQueries + $modifier;
      }
    }

    public function modifyNumberOfSelects($modifier) {
      if(ENVIRONMENT !== 'PRODUCTION') {
        $this->numberOfSelects = $this->numberOfSelects + $modifier;
      }
    }

    public function modifyNumberOfCacheHits($modifier) {
      if(ENVIRONMENT !== 'PRODUCTION') {
        $this->numberOfCacheHits = $this->numberOfCacheHits + $modifier;
      }
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        // Unset the connection
        $this->connection = null;
        $this->updateSiteProperties();
        return ['connection', 'defaultFetchType', 'settings', 'cache', 'encryption', 'uniqueHash', 'tables', 'nonCPUBoundTime', 'numberOfQueries', 'numberOfSelects', 'numberOfCacheHits'];
    }

    /**
     *
     */
    public function __wakeup()
    {
        $this->__construct($this->settings);
    }
}
