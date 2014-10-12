<?hh


	/**
	 * Class DB Test
	 */

	class HC_DB_Test extends PHPUnit_Framework_TestCase {



		//@todo: Complete HC_DB_Test
		protected $connection;



		public function testDB()

		{



			$this->assertEquals(class_exists('HC\DB'), true);

		}



		/**
		 * @depends testDB
		 */

		public function testDBCreation()

		{



			$this->connection = new HC\DB(['useCache' => false]);

			$this->assertNotEmpty($this->connection);

		}



		/**
		 * @depends testDBCreation
		 */

		public function testDBCreateTable()

		{



			$this->connection  = new HC\DB(['useCache' => false]);

			$createPermissions = 'CREATE TABLE IF NOT EXISTS `permissions` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `name` varchar(255) NOT NULL,
                        `userID` int(15) NOT NULL,
                        PRIMARY KEY (`id`)
                      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';



			$createUsers = 'CREATE TABLE IF NOT EXISTS `users` (
                  `id` int(20) NOT NULL AUTO_INCREMENT,
                  `firstName` varchar(255) NOT NULL,
                  `lastName` varchar(255) NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `password` varchar(255) DEFAULT NULL,
                  `level` varchar(255) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';



			$createTest = 'CREATE TABLE IF NOT EXISTS `test` (
                  `id` int(20) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `value` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';



            $createCache = 'CREATE TABLE `HC_Cache` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `cacheKey` varchar(255) NOT NULL,
                              `cacheValue` longtext NOT NULL,
                              `cacheTimeout` int(11) DEFAULT NULL,
                              PRIMARY KEY (`cacheKey`),
                              UNIQUE KEY `id_UNIQUE` (`id`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';



            $createTables = 'CREATE TABLE `HC_Tables` (
                              `name` varchar(64) NOT NULL,
                              `lastUpdated` int(10) unsigned NOT NULL,
                              PRIMARY KEY (`name`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';



			$this->assertTrue($this->connection->query($createPermissions), 'Could not create permissions table.');

			$this->assertTrue($this->connection->query($createUsers), 'Could not create users table.');

            $this->assertTrue($this->connection->query($createTest), 'Could not create test table.');

            $this->assertTrue($this->connection->query($createCache), 'Could not create cache table.');

            $this->assertTrue($this->connection->query($createTables), 'Could not create tables table.');

		}



		/**
		 * @depends testDBCreateTable
		 */

		public function testDBInsert()

		{



			$this->connection = new HC\DB();

			$test1            = 'INSERT INTO test
                              (
                                name,
                                value
                              )
                              VALUES
                              (
                                \'flatName\',
                                \'flatValue\'
                              );';



			$this->assertTrue($this->connection->query($test1), 'Could not insert flat values.');



			$test2 = 'INSERT INTO test
                              (
                                name,
                                value
                              )
                              VALUES
                              (
                                ?,
                                ?
                              );';



			$this->assertTrue($this->connection->query($test2, [

				'unNamedName',

				'unNamedValue'

			]), 'Could not insert unnamed values.');



			$test3 = 'INSERT INTO test
                              (
                                name,
                                value
                              )
                              VALUES
                              (
                                :namedName,
                                :namedValue
                              );';



			$this->assertTrue($this->connection->query($test3, [

				':namedName'  => 'namedName',

				':namedValue' => 'namedValue'

			]), 'Could not insert named values.');

		}



		/**
		 * @depends testDBInsert
		 */

		public function testDBSelect()

		{



			$this->connection = new HC\DB();

			$test1            = 'SELECT * FROM test;';



			$this->assertNotEmpty($this->connection->query($test1), 'Could not "SELECT * FROM test".');



			$test2 = 'SELECT * FROM test WHERE name = \'flatName\'';



			$this->assertNotEmpty($this->connection->query($test2), 'Could not select flat values.');



			$test3 = 'SELECT * FROM test WHERE name = ? AND value = ?';



			$this->assertNotEmpty($this->connection->query($test3, [

				'unNamedName',

				'unNamedValue'

			]), 'Could not select unnamed values.');



			$test3 = 'SELECT * FROM test WHERE name = :namedName AND value = :namedValue';



			$this->assertNotEmpty($this->connection->query($test3, [

				':namedName'  => 'namedName',

				':namedValue' => 'namedValue'

			]), 'Could not select named values.');

		}

	}

