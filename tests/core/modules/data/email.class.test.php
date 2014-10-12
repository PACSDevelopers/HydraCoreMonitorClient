<?hh


	/**
	 * Class HC_Email_Test
	 */

	class HC_Email_Test extends PHPUnit_Framework_TestCase {



		// @todo: Complete HC_Email_Test


		public function testMail()

		{



			$this->assertEquals(class_exists('HC\Email'), true);

		}



		/**
		 * @depends testMail
		 */

		public function testMailCreation()

		{



			$this->assertNotEmpty(new HC\Email());

		}

	}

