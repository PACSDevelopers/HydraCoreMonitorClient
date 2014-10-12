<?hh


	class HC_Core_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_Core_Test Test


		public $passed = false;



		public function testCore()

		{



			$this->assertNotEmpty($GLOBALS['HC_CORE']);

		}

	}

