<?hh


	class HC_Error_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_Error_Test Test


		public function testError()

		{



			$this->assertEquals(class_exists('HC\Error'), true);

		}



        /**
         * @depends testError
         */

        public function testErrorBacktrace()

        {



            $this->assertNotEmpty(HC\Error::getBacktrace(debug_backtrace(),PHP_EOL));

        }

	}

