<?hh


	class HC_Site_Test extends PHPUnit_Framework_TestCase {



		// @todo: Complete HC_Site_Test


		public function testSite()

		{



			$this->assertEquals(class_exists('HC\Site'), true);

		}



        /**
         * @depends testSite
         */

        public function testSiteCreation()

        {

            $siteSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

            $GLOBALS['HC_CORE']->setSite(null);

            $site = new HC\Site($siteSettings);

            $GLOBALS['HC_CORE']->setSite($site);

            $this->assertNotEmpty($GLOBALS['HC_CORE']);

            $this->assertNotEmpty($GLOBALS['HC_CORE']->getSite());

        }



        /**
         * @depends testSiteCreation
         */

        public function testSiteSettings()

        {



            $this->assertNotEmpty($GLOBALS['HC_CORE']->getSite()->getSettings());

        }



        /**
         * @depends testSiteSettings
         */

        public function testSiteCreationTime()

        {



            $this->assertNotEmpty($GLOBALS['HC_CORE']->getSite()->getStartTime());

        }



        /**
         * @depends testSiteCreationTime
         */

        public function testSiteLinuxDistribution()

        {



            if(PHP_OS === 'Linux') {

                $this->assertNotEmpty(HC\Site::getLinuxDistro());

            }

        }

	}

