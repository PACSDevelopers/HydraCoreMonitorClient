<?hh


	class HC_Page_Test extends PHPUnit_Framework_TestCase {



		// @todo: Complete HC_Page_Test


		public function testPage()

		{



			$this->assertEquals(class_exists('HC\Page'), true);

		}



        /**
         * @depends testPage
         */

        public function testPageCreation()

        {


            $page = new HC\Page();
            $this->assertNotEmpty($page);
            $page->setRendered(true);

        }



        /**
         * @depends testPageCreation
         */

        public function testPageSettings()

        {

            $pageSettings = [

                'views' => [

                    'header' => [

                        'pageName' => 'TestPageName',

                        'js'       => ['main' => true],

                        'scss'     => ['main' => true],

                        'less'     => ['main' => true]

                    ],

                    'body'   => true,

                    'footer' => true

                ]

            ];



            // Create the page
            $thisPage = new HC\Page($pageSettings);



            // Make sure we actually have a page
            $this->assertNotEmpty($thisPage);



            // Set the body content
            $thisPage->body = <h1>HydraCore</h1>;


            // Render the page
            $pageContents = (string)$thisPage->render();


            // Make sure we got the contents
            $this->assertNotEmpty($pageContents);



            // Check for pageName
            $this->assertContains('TestPageName', $pageContents);



            // Check for javascript
            $this->assertContains('main.js', $pageContents);



            // Check for sass
            $this->assertContains('main.scss.css', $pageContents);



            // Check for less
            $this->assertContains('main.less.css', $pageContents);



            // Check for Body
            $this->assertContains((string)$thisPage->body, $pageContents);



            // Check for Footer
            $this->assertContains('</html>', $pageContents);

        }

	}



