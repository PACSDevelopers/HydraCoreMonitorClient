<?hh


	/**
	 * Class Checkbox Test
	 */

	class HC_Checkbox_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_Checkbox_Test Test


		public function testCheckbox()

		{



			$this->assertEquals(class_exists('HC\Checkbox'), true);

		}



		/**
		 * @depends testCheckbox
		 */

		public function testCheckboxCreation()

		{



			$this->assertNotEmpty(new HC\Checkbox());

		}



		/**
		 * @depends testCheckboxCreation
		 */

		public function testCheckboxHTML()

		{



			$checkbox     = new HC\Checkbox([

				'name'      => 'checkboxName',

				'checked'   => true,

				'class'     => 'checkboxClass',

				'onclick'   => 'alert("Checkbox clicked");',

				'disabled'  => true,

				'required'  => true,

				'autofocus' => true,

				'style'     => 'color: red;',

				'append'    => '<p>Append Checkbox</p>',

				'prepend'   => '<p>Prepend Checkbox</p>'

			]);

			$checkboxHTML = $checkbox->render();

			$this->assertContains('type="checkbox"', $checkboxHTML, 'Checkbox did not contain <type="checkbox",: ' . $checkboxHTML);

			// Check for prepend
			$this->assertStringStartsWith('<p>Prepend Checkbox</p>', $checkboxHTML, 'Checkbox did not contain prepend');



			// Check for append
			$this->assertStringEndsWith('<p>Append Checkbox</p>', $checkboxHTML, 'Checkbox did not contain append');

			Assert::HTMLValidate($checkboxHTML);

		}

	}

