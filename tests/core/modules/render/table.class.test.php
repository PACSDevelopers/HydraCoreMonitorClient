<?hh


	/**
	 * Class Table Test
	 */

	class HC_Table_Test extends PHPUnit_Framework_TestCase {



		// @todo: HC_Table_Test Test


		public function testTable()

		{



			$this->assertEquals(class_exists('HC\Table'), true);

		}



		/**
		 * @depends testTable
		 */

		public function testTableCreation()

		{



			$this->assertNotEmpty(new HC\Table());

		}



		/**
		 * @depends testTableCreation
		 */

		public function testTableHTML()

		{

            $tableSettings = [

                'name' => 'tableName',

                'class' => 'tableClass',

                'style' => 'color: yellow;',

                'append' => '<p>Append</p>',

                'prepend' => '<p>Prepend</p>'

            ];



            $table = new HC\Table($tableSettings);



            $table->openHeader([

                'name' => 'headerName',

                'class' => 'headerClass',

                'style' => 'color: red;'

            ]);





            foreach(range(1,5) as $row) {

                $table->openRow([

                    'name' => 'headerRowName' . $row,

                    'class' => 'headerRowClass' . $row,

                    'style' => 'color: red;'

                ]);

                foreach(range(1,5) as $column) {

                    $table->column([

                            'name' => 'headerColName' . $row . $column,

                            'class' => 'headerColClass' . $row . $column,

                            'style' => 'color: red;',

                            'value' => 'Header' . $column]

                    );

                }

                $table->closeRow();

            }



            $table->closeHeader();



            $table->openBody([

                'name' => 'bodyName',

                'class' => 'bodyClass',

                'style' => 'color: green;'

            ]);





            foreach(range(1,5) as $row) {

                $table->openRow([

                    'name' => 'bodyRowName'. $row,

                    'class' => 'bodyRowClass' . $row,

                    'style' => 'color: green;'

                ]);

                foreach(range(1,5) as $column) {

                    $table->column([

                            'name' => 'bodyColName' . $row . $column,

                            'class' => 'bodyColClass' . $row . $column,

                            'style' => 'color: green;',

                            'value' => 'Body' . $column]

                    );

                }

                $table->closeRow();

            }



            $table->closeBody();



            $table->openFooter([

                'name' => 'footerName',

                'class' => 'footerClass',

                'style' => 'color: blue;'

            ]);





            foreach(range(1,5) as $row) {

                $table->openRow([

                    'name' => 'footerRowName' . $row,

                    'class' => 'footerRowClass' . $row,

                    'style' => 'color: blue;'

                ]);

                foreach(range(1,5) as $column) {

                    $table->column([

                            'name' => 'footerColName' . $row . $column,

                            'class' => 'footerColClass' . $row . $column,

                            'style' => 'color: blue;',

                            'value' => 'Footer' . $column]

                    );

                }

                $table->closeRow();

            }



            $table->closeFooter();



            $tableHTML = $table->render();





			$this->assertContains('<table', $tableHTML, 'Table did not contain <table:' . $tableHTML);

            $this->assertContains('tableName', $tableHTML, 'Table did not contain tableName:' . $tableHTML);

            $this->assertContains('tableClass', $tableHTML, 'Table did not contain tableClass:' . $tableHTML);

            $this->assertContains('color: yellow;', $tableHTML, 'Table did not contain color: yellow;:' . $tableHTML);



            $this->assertContains('headerName', $tableHTML, 'Table did not contain headerName:' . $tableHTML);

            $this->assertContains('headerClass', $tableHTML, 'Table did not contain headerClass:' . $tableHTML);

            $this->assertContains('color: red;', $tableHTML, 'Table did not contain color: red;:' . $tableHTML);



            foreach(range(1,5) as $row) {

                $this->assertContains('headerRowName' . $row, $tableHTML, 'Table did not contain headerRowName' . $row . ':' . $tableHTML);

                $this->assertContains('headerRowClass' . $row, $tableHTML, 'Table did not contain headerRowClass' . $row . ':' . $tableHTML);

                foreach(range(1,5) as $column) {

                    $this->assertContains('headerColName' . $row . $column, $tableHTML, 'Table did not contain headerColName' . $row . $column . ':' . $tableHTML);

                    $this->assertContains('headerColClass' . $row . $column, $tableHTML, 'Table did not contain headerColClass' . $row . $column . ':' . $tableHTML);

                }

            }



            $this->assertContains('bodyName', $tableHTML, 'Table did not contain bodyName:' . $tableHTML);

            $this->assertContains('bodyClass', $tableHTML, 'Table did not contain bodyClass:' . $tableHTML);

            $this->assertContains('color: green;', $tableHTML, 'Table did not contain color: green;:' . $tableHTML);



            foreach(range(1,5) as $row) {

                $this->assertContains('bodyRowName' . $row, $tableHTML, 'Table did not contain bodyRowName' . $row . ':' . $tableHTML);

                $this->assertContains('bodyRowClass' . $row, $tableHTML, 'Table did not contain bodyRowClass' . $row . ':' . $tableHTML);

                foreach(range(1,5) as $column) {

                    $this->assertContains('bodyColName' . $row . $column, $tableHTML, 'Table did not contain bodyColName' . $row . $column . ':' . $tableHTML);

                    $this->assertContains('bodyColClass' . $row . $column, $tableHTML, 'Table did not contain bodyColClass' . $row . $column . ':' . $tableHTML);

                }

            }



            $this->assertContains('footerName', $tableHTML, 'Table did not contain tableName:' . $tableHTML);

            $this->assertContains('footerClass', $tableHTML, 'Table did not contain footerClass:' . $tableHTML);

            $this->assertContains('color: blue;', $tableHTML, 'Table did not contain color: blue;:' . $tableHTML);



            foreach(range(1,5) as $row) {

                $this->assertContains('footerRowName' . $row, $tableHTML, 'Table did not contain footerRowName' . $row . ':' . $tableHTML);

                $this->assertContains('footerRowClass' . $row, $tableHTML, 'Table did not contain footerRowClass' . $row . ':' . $tableHTML);

                foreach(range(1,5) as $column) {

                    $this->assertContains('footerColName' . $row . $column, $tableHTML, 'Table did not contain footerColName' . $row . $column . ':' . $tableHTML);

                    $this->assertContains('footerColClass' . $row . $column, $tableHTML, 'Table did not contain footerColClass' . $row . $column . ':' . $tableHTML);

                }

            }



            $this->assertContains('<p>Append</p>', $tableHTML, 'Table did not contain <p>Append</p>:' . $tableHTML);

            $this->assertContains('<p>Prepend</p>', $tableHTML, 'Table did not contain <p>Prepend</p>:' . $tableHTML);



			Assert::HTMLValidate($tableHTML);

		}

	}

