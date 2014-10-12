<?hh


	/**
	 * Class Select Test
	 */

	class HC_Select_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_Select_Test Test


		public function testSelect()

		{



			$this->assertEquals(class_exists('HC\Select'), true);

		}



		/**
		 * @depends testSelect
		 */

		public function testSelectCreation()

		{



			$this->assertNotEmpty(new HC\Select());

		}



		/**
		 * @depends testSelectCreation
		 */

		public function testSelectHTML()

		{

            $select     = new HC\Select([

                'name'      => 'selectName',

                'class'     => 'selectClass',

                'onclick'   => 'alert("Select Clicked");',

                'onchange'  => 'alert("Select Changed")',

                'disabled'  => true,

                'required'  => true,

                'autofocus' => true,

                'style'     => 'color: red;',

                'multiple'  => true,

                'append'    => '<p>Append Select</p>',

                'prepend'   => '<p>Prepend Select</p>'

            ]);





            foreach(range(1,5)  as $option) {

                $select->option([

                    'name'     => 'optionName'. $option,

                    'value'    => 'optionValue' . $option,

                    'disabled' => true,

                    'selected' => true

                ]);

            }







            foreach(range(1,5)  as $group) {

                $select->openGroup([

                    'name'     => 'groupName' . $group,

                    'disabled' => true,

                    'label'    => 'groupLabel' . $group

                ]);



                foreach(range(1,5)  as $option) {

                    $select->option([

                        'name'     => 'optionName' . $group . $option,

                        'value'    => 'optionValue' . $group . $option,

                        'disabled' => true,

                        'selected' => true

                    ]);

                }



                $select->closeGroup();

            }





			$selectHTML = $select->render();

            $this->assertContains('<select', $selectHTML, 'Select did not contain <select,:' . $selectHTML);



			// Check for prepend
			$this->assertStringStartsWith('<p>Prepend Select</p>', $selectHTML, 'Checkbox did not contain prepend');



			// Check for append
			$this->assertStringEndsWith('<p>Append Select</p>', $selectHTML, 'Checkbox did not contain append');



            foreach(range(1,5)  as $option) {

                $this->assertContains('optionName' . $option, $selectHTML, 'Select did not contain optionName' . $option . ':' . $selectHTML);

                $this->assertContains('optionValue' . $option, $selectHTML, 'Select did not contain optionValue' . $option . ':' . $selectHTML);

            }







            foreach(range(1,5)  as $group) {

                $this->assertContains('groupName' . $group, $selectHTML, 'Select did not contain groupName' . $group . ':' . $selectHTML);

                $this->assertContains('groupLabel' . $group, $selectHTML, 'Select did not contain groupLabel' . $group . ':' . $selectHTML);



                foreach(range(1,5)  as $option) {

                    $this->assertContains('optionName' . $group . $option, $selectHTML, 'Select did not contain optionName' . $group . $option . ':' . $selectHTML);

                    $this->assertContains('optionValue' . $group . $option, $selectHTML, 'Select did not contain optionValue' . $group . $option . ':' . $selectHTML);

                }



                $select->closeGroup();

            }



			Assert::HTMLValidate($selectHTML);

		}

	}

