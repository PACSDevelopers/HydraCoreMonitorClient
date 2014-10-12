<?hh


	class HC_Post_Receive_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_Post_Receive_Test Test


		public function testPostReceive()

		{



			$skipCoreInclude = true;

			$location        = str_replace('core', 'hooks', HC_CORE_LOCATION);

			$cwd             = getcwd();

			chdir($location);

			ob_start();

			try {

				require_once $location . '/post-receive.php';

			} catch(Exception $e) {

				$contents = ob_get_contents();

				ob_clean();

				trigger_error('Post-Receive Failed');

				exit(1);

			}



			$contents = ob_get_contents();

            ob_end_clean();



			$this->assertContains('Updating public', $contents, 'Post-Receive did not update public.' . PHP_EOL . $contents);

			$this->assertNotContains('Failed', $contents, 'Post-Receive Failed.' . PHP_EOL . $contents);

			unset($contents);

			chdir($cwd);

		}

	}

