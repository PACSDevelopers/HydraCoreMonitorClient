<?hh


	/**
	 * Class TextArea Test
	 */

	class HC_TextArea_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_TextArea_Test Test


		public function testTextArea()

		{



			$this->assertEquals(class_exists('HC\TextArea'), true);

		}



		/**
		 * @depends testTextArea
		 */

		public function testTextAreaCreation()

		{



			$this->assertNotEmpty(new HC\TextArea());

		}



		/**
		 * @depends testTextAreaCreation
		 */

		public function testTextAreaHTML()

		{



			$textArea     = new HC\TextArea([

                'name' => 'textAreaName',

                'value' => 'textAreaValue',

                'class' => 'textAreaClass',

                'onclick' => 'alert("TextArea Clicked");',

                'onchange' => 'alert("TextArea Changed");',

                'disabled' => true,

                'required' => true,

                'autofocus' => true,

                'readonly' => true,

                'style' => 'color: red;',

                'cols' => 1,

                'rows' => 2,

                'wrap' => 3,

                'form' => false,

                'placeholder' => 'textAreaPlaceHolder',

                'maxlength' => 255,

                'append' => '<p>Append TextArea</p>',

                'prepend' => '<p>Prepend TextArea</p>'

			]);



			$textAreaHTML = $textArea->render();

			$this->assertContains('<textarea', $textAreaHTML, 'TextArea did not contain <textarea:' . $textAreaHTML);



			// Check for prepend
			$this->assertStringStartsWith('<p>Prepend TextArea</p>', $textAreaHTML, 'TextArea did not contain prepend');



			// Check for append
			$this->assertStringEndsWith('<p>Append TextArea</p>', $textAreaHTML, 'TextArea did not contain append');

			Assert::HTMLValidate($textAreaHTML);

		}

	}

