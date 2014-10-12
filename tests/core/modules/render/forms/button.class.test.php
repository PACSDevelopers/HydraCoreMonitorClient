<?hh


	/**
	 * Class Button Test
	 */

	class HC_Button_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_Button_Test Test


		public function testButton()

		{



			$this->assertEquals(class_exists('HC\Button'), true);

		}



		/**
		 * @depends testButton
		 */

		public function testButtonCreation()

		{



			$this->assertNotEmpty(new HC\Button());

		}



		/**
		 * @depends testButtonCreation
		 */

		public function testButtonHTML()

		{



			$button     = new HC\Button([

				'type'      => 'button',

				'name'      => 'buttonName',

				'value'     => 'Value',

				'class'     => 'class',

				'onclick'   => 'alert("Button clicked");',

				'autofocus' => true,

				'style'     => 'color: red;',

				'disabled'  => true,

				'append'    => '<p>Append Button</p>',

				'prepend'   => '<p>Prepend Button</p>'

			]);

			$buttonHTML = $button->render();

			$this->assertContains('<button', $buttonHTML, 'Button did not contain <button,: ' . $buttonHTML);

			// Check for prepend
			$this->assertStringStartsWith('<p>Prepend Button</p>', $buttonHTML, 'Button did not contain prepend');



			// Check for append
			$this->assertStringEndsWith('<p>Append Button</p>', $buttonHTML, 'Button did not contain append');

			Assert::HTMLValidate($buttonHTML);

		}

	}

