<?hh


	class HC_Pre_Receive_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_Pre_Receive_Test Test


		public function testPreReceive()

		{



			$skipCoreInclude = true;

			$location        = str_replace('core', 'hooks', HC_CORE_LOCATION);

			$cwd             = getcwd();

			chdir($location);

			ob_start();

			try {

				require_once $location . '/pre-receive.php';

			} catch(Exception $e) {

				$contents = ob_get_contents();

				ob_clean();

				trigger_error('Pre-Receive Failed');

				exit(1);

			}



			$contents = ob_get_contents();

			ob_end_clean();



			$this->assertContains('Locked Application', $contents, 'Pre-Receive did not update public.' . PHP_EOL . $contents);

			$this->assertNotContains('Failed', $contents, 'Pre-Receive Failed.' . PHP_EOL . $contents);

			unset($contents);

			chdir($cwd);

		}

	}

