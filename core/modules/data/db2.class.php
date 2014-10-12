<?hh // decl

namespace HC;

/**
 * Class DB2
 */
class DB2 extends Core
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
        $settings = $this->parseOptions($settings, $globalSettings['database']);

        // Parse default options
        $settings = $this->parseOptions($settings, ['timeout' => 60, 'persistant' => true]);

        $this->settings = $settings;
        $this->connect();

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
            } catch (\PDOException $exception) {
                // Trigger the error handler, based on exception details
                Error::errorHandler($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), 0, $exception->getTrace());
            }

            return true;
        }

        return false;
    }


    /**
     * @param string $sql
     * @param array $values
     * @param integer $fetchType
     *
     * @return false|array
     * @throws \Exception
     */
    public function run(\HC\DB2\Query $query) {
        var_dump($query);
        exit(0);
    }

    public function query($settings = []) {
        return new \HC\DB2\Query($settings);
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

    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        // Unset the connection
        $this->connection = null;
        return ['connection', 'defaultFetchType', 'settings'];
    }

    /**
     *
     */
    public function __wakeup()
    {
        $this->__construct($this->settings);
    }
}
