<?hh


	/**
	 * Class HC_Cache_Test
	 */

	class HC_Cache_Test extends PHPUnit_Framework_TestCase {



		// @todo: Complete HC_Cache_Test


		public function testCache()

		{



			$this->assertEquals(class_exists('HC\Cache'), true, 'HC\Cache Class does not exist.');

		}



        /**
         * @depends testCache
         */

        public function testCacheCreation()

        {



            $this->assertNotEmpty(new HC\Cache(), 'Object of HC\Cache could not be created.');

        }



        /**
         * @depends testCacheCreation
         */

        public function testCacheValue()

        {

            // Get cache
            $cache = new HC\Cache();



            // Make sure we got the object
            $this->assertNotEmpty($cache, 'Object of HC\Cache could not be created.');



            // Store a value
            $this->assertTrue($cache->insert('TestKey', 'TestValue'), 'Could not insert value into cache');



            // Check key exists
            $this->assertTrue($cache->exists('TestKey'), 'After inserting, the value did not exist in cache');



            // Get a value
            $this->assertEquals('TestValue', $cache->select('TestKey'), 'Could not get value from cache');



            // Delete key
            $this->assertTrue($cache->delete('TestKey'), 'Could not delete value from cache');



            // Check key exists
            $this->assertFalse($cache->exists('TestKey'), 'Key still existed after deletion');



            // Store a value
            $this->assertTrue($cache->insert('TestKey2', 'TestValue2'), 'Could not insert value into cache 2');



            // Delete all values
            $this->assertTrue($cache->deleteAll(), 'Could not delete all values from cache');



            // Check key exists
            $this->assertFalse($cache->exists('TestKey2'), 'Key still existed after delete all');



            // Test selectInsert
            $this->assertTrue(((bool)$cache->selectInsert('TestKey3', 'TestValue3')), 'Could not selectInsert into cache');



            // Check key exists
            $this->assertTrue($cache->exists('TestKey3'), 'Key did not exist after selectInsert 3');



            // Get a value
            $this->assertEquals('TestValue3', $cache->select('TestKey3'), 'Could not select value from cache, or did not match');



            // Delete key
            $this->assertTrue($cache->delete('TestKey3'), 'Could not delete key 3');



            // Check key exists
            $this->assertFalse($cache->exists('TestKey3'), 'Key 3 still exists after deletion');

        }

	}

