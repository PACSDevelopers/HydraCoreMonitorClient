<?hh


	/**
	 * Class Form Test
	 */

	class HC_Form_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_Form_Test Test


		public function testForm()

		{



			$this->assertEquals(class_exists('HC\Form'), true);

		}



		/**
		 * @depends testForm
		 */

		public function testFormCreation()

		{



			$this->assertNotEmpty(new HC\Form());

		}



		/**
		 * @depends testFormCreation
		 */

		public function testFormHTML()

		{



			$form = new HC\Form([

				'name'         => 'FormName',

				'class'        => 'FormClass',

				'action'       => 'action.php',

				'onsubmit'     => 'alert("Submitted");',

				'autocomplete' => true,

				'style'        => 'color: red;',

				'novalidate'   => true,

				'method'       => 'GET',

				'target'       => '_blank',

				'enctype'      => 'application/x-www-form-urlencoded',

				'append'       => '<p>Append Form</p>',

				'prepend'      => '<p>Prepend Form</p>'

			]);



            $form->append('<p>Append Form 2</p>');



            $form->prepend('<p>Prepend Form 2</p>');



            $input = new HC\Input(['name' => 'inputName']);

            $submit = new HC\Button(['type' => 'submit']);



            $form->appendToBody($input->render());

            $form->prependToBody($submit->render());



            $formHTML = $form->render();



			// Check for prepend
			$this->assertStringStartsWith('<p>Prepend Form 2</p><p>Prepend Form</p>', $formHTML, 'Form did not contain prepend');



            // Check for append
            $this->assertStringEndsWith('<p>Append Form</p><p>Append Form 2</p>', $formHTML, 'Form did not contain append');



            // Check for inputName
            $this->assertContains('inputName', $formHTML, 'Form did not contain inputName');



            // Check for submit
            $this->assertContains('submit', $formHTML, 'Form did not contain submit');



			// Check for start of form
			$this->assertContains('<form', $formHTML, 'Form did not contain <form,:' . $formHTML);



			// Check for end of form
			$this->assertContains('</form>', $formHTML, 'Form did not contain </form>,:' . $formHTML);





			// Validate the html
			Assert::HTMLValidate($formHTML);

		}

	}

