<?hh


	class HC_User_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_User_Test Test


		public function testUser()

		{



			$this->assertEquals(class_exists('HC\User'), true);

		}



		/**
		 * @depends testUser
		 */

		public function testUserCreation()

		{



			$this->assertNotEmpty(new HC\User());

		}

	}

