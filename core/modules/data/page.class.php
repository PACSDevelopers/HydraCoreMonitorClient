<?hh // decl


	namespace HC;

	/**
	 * Class Page
	 */

	class Page extends Core

	{

		// Setup class public variables
		/**
		 * @var string
		 */

		public $body = '';

		protected $isAJAX = false;

		protected static $nodeWhiteList = [
			'#text',
			'#document',
			'p',
			'html',
			'body',
			'p',
			'br',
			'blockquote',
			'span',
			'ul',
			'ol',
			'li',
			'table',
			'thead',
			'tbody',
			'tfoot',
			'th',
			'tr',
			'td',
			'a',
			'img',
			'iframe',
			'hr',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'strong',
			'em',
			'b',
			'i',
			'small',
			'address',
			'date',
			'sub',
			'sup',
			'code',
			'pre'
		];

		protected static $nodeAttributeWhitelist = [
			'href' => 'javascript(.[\n\r]*)*?:',
			'style' => 'expression(.[\n\r]*)*?\(',
			'width' => '',
			'height' => '',
			'src' => '',
			'frameborder' => '',
			'sandbox' => '',
			'class' => '',
			'alt' => '',
			'title' => '',
			'colspan' => '',
			'target' => ''
		];



		// Setup class protected variables
		/**
		 * @var array
		 */

		protected $settings = [

			'authentication' => false,

			'cacheViews' => false,

			'views' => []

		];

    protected $finalRender = [];



		protected $cache = null;

		/**
		 * @var bool|mixed
		 */

		protected $viewsLocation = false;



		protected $rendered = false;

		protected $renderRaw = false;



		// Setup public functions
		/**
		 * @param (int|bool|array|string|null)[] $settings
		 */

		public function __construct(&$settings = [])

		{
			if(is_null($settings)) {
				$settings = [];
			}


			$settings = $this->parseOptions($settings, $this->settings);



			// Parse global / local options
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			$settings = $this->parseOptions($settings, $globalSettings['pages']);



			$globalSettings = null;

			unset($globalSettings);



			if ($settings['cacheViews'] === true) {

				$this->cache = new Cache();

			}



			if (isset($settings['authentication'])) {

				if($settings['authentication'] === true) {

					if (!isset($_SESSION['user'])) {

                        $_SESSION['desiredLoginPage'] = PROTOCOL . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        
						header('Location: ' . PROTOCOL . '://' . SITE_DOMAIN . '/' . LOGIN_PAGE);

						exit();

					}

				}

			}

			$this->settings = $settings;

			$this->viewsLocation = str_replace('core', 'views', HC_CORE_LOCATION);



			return true;

		}



		public function __destruct()

		{

			if ($this->rendered === false) {

				echo $this->render();

			}

			$this->rendered = null;

			$this->cache = null;

			$this->body = null;

			$this->settings = null;

			$this->viewsLocation = null;

		}

		/**
		 * @return bool
		 */

		public function render() {

            if(isset($GLOBALS['skipRender']) && $GLOBALS['skipRender']) {
                return false;
            }

            if(!$this->rendered) {
                if($this->renderRaw) {
                    $this->sendHeader();
                    $this->rendered = true;
                    return $this->body;
                }

                if (isset($this->settings['views'])) {
										if($this->isAJAX) {
											$page = $this->body;
										} else {
											try {
												$body = <x:frag></x:frag>;
												$header = <x:frag></x:frag>;

												foreach ($this->settings['views'] as $row => $value) {
														if ($value) {
																if($row === 'header') {
																		$header = $this->getView($row, $value);
																} else {
																		// Render default view that was defined
																		$body->appendChild($this->getView($row, $value));
																}
														}
												}

												$page = <x:doctype>
                                                                <html>
                                                                        <head>{$header}</head>
                                                                        <body>{$body}</body>
                                                                </html>
                                                        </x:doctype>;
											}	catch (\Exception $exception) {
                                                Error::exceptionHandler($exception);
												return true;
											}
										}

										$this->sendHeader();

										$this->rendered = true;
                    return $page;
                }

            }

			return false;

		}



		/**
		 * @param string $viewName
		 * @param (int|bool|array|string|null)[] $viewSettings
		 *
		 * @return bool
		 */

		protected function getView($viewName, $viewSettings) {
			if (is_string($this->viewsLocation)) {

				$fileName = $this->viewsLocation . '/' . $viewName . '.php';


				// If view exists
				if (is_file($fileName)) {

					include($fileName);

                    $class = ucfirst($viewName . 'View');
                    if(class_exists($class)) {
                        $view = new $class();
												if ($this->settings['views']['body'] && $viewName === 'body') {
													if($this->isAJAX) {
														return $this->body;
													} else {
														return $view->init($viewSettings, $this->body);
													}
												}
                        return $view->init($viewSettings);
                    }
				} else {
                    if($viewName === 'body') {
                        return $this->body;
                    }
                }

			}

			return false;

		}



		/**
		 * @return bool
		 */

		public function startBuffer()

		{



			// If headers have not been sent
			if (!headers_sent()) {

				// Try override the time limit
				@set_time_limit(0);

				// Clean the buffer
				ob_clean();

				// Close the connection
				@header("Connection: close");

				// Ignore client aborting request
				ignore_user_abort('true');

				// Start the object
				ob_start();



				return true;

			}



			return false;

		}



		// Setup protected functions


		/**
		 * @return bool
		 */

		public function endBuffer()

		{



			// Get the length of our buffered content
			$size = ob_get_length();



			// If we have content from the buffer
			if ($size !== false) {



				// If headers have not been sent
				if (!headers_sent()) {

					// Send the content length header with the buffer size
					@header('Content-Length: ' . $size);



					// Flush the buffer
					ob_end_flush();

					flush();



					return true;

				}

			}



			return false;

		}

        /**
         * @param  string [][] $viewSettings
         * @return string
         */

        public static function generateComponents($additional = [], $type = 'all')

        {
            $output = <x:frag></x:frag>;
            
            $siteSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();
            if($type === 'all' || $type === 'css') {
                if (isset($siteSettings['pages'])) {
                    if (isset($siteSettings['pages']['components'])) {
                        if (isset($siteSettings['pages']['components']['css'])) {
                            foreach($siteSettings['pages']['components']['css'] as $css) {
                                $output->appendChild(<link rel="stylesheet" type="text/css" href={PROTOCOL . '://' . SITE_DOMAIN . $css} />);
                            }
                        }
                    }
                }

                if(isset($additional['css'])) {
                    foreach($additional['css'] as $css) {
                        $output->appendChild(<link rel="stylesheet" type="text/css" href={PROTOCOL . '://' . SITE_DOMAIN . $css} />);
                    }
                }
            }
            
            if($type === 'all' || $type === 'js') {
                if (isset($siteSettings['pages'])) {
                    if (isset($siteSettings['pages']['components'])) {
                        if (isset($siteSettings['pages']['components']['js'])) {
                            foreach($siteSettings['pages']['components']['js'] as $js) {
                                $output->appendChild(<script src={PROTOCOL . '://' . SITE_DOMAIN .  $js}></script>);
                            }
                        }
                    }
                }

                if(isset($additional['js'])) {
                    foreach($additional['js'] as $js) {
                        $output->appendChild(<script src={PROTOCOL . '://' . SITE_DOMAIN .  $js}></script>);
                    }
                }
            }
            
			return $output;

		}



        /**
         * @param  string [][] $viewSettings
         * @return string
         */

        public static function generateResources($viewSettings, $type = 'all')

        {
            $output = <x:frag></x:frag>;



			// Get the resource path
			$siteSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($siteSettings['compilation'])) {

                if (isset($siteSettings['compilation']['path'])) {

                    $resourcePath = $siteSettings['compilation']['path'];

                } else {

                    $resourcePath = '/resources/';

                }

            } else {

                $resourcePath = '/resources/';

            }



            $viewSettings = Core::parseOptions($viewSettings, ['scss' => [], 'less' => [], 'js' => []]);
            
			// Exclude settings we no longer need
			if (isset($siteSettings['pages'])) {

                if (isset($siteSettings['pages']['resources'])) {
                    // Parse the view settings based on default
                    $siteSettingsResources = Core::parseOptions($siteSettings['pages']['resources'], ['scss' => [], 'less' => [], 'js' => []]);


                    // Parse the view settings based on site settings
                    $viewSettings = Core::parseOptions($viewSettings, $siteSettingsResources);
                }
                
            }
            

			switch($type) {

                case 'js':

                    $output->appendChild(self::renderJS($resourcePath, $viewSettings));

                    break;



                case 'css':

                    $output->appendChild(self::renderCSS($resourcePath, $viewSettings));

                    break;

                default:

                    $output->appendChild(self::renderCSS($resourcePath, $viewSettings));

                    $output->appendChild(self::renderJS($resourcePath, $viewSettings));

                    break;

            }



			return $output;

		}



		private static function renderCSS($resourcePath, $viewSettings) {

			$output = <x:frag></x:frag>;



			// If this page uses scss or less
			foreach (['scss', 'less'] as $css) {

				if (isset($viewSettings[$css])) {

					if (is_array($viewSettings[$css])) {

						// Link the resources
						foreach ($viewSettings[$css] as $row => $value) {

							$output->appendChild(<link rel="stylesheet" type="text/css" href={PROTOCOL . '://' . SITE_DOMAIN . $resourcePath . $css . '/' . $row . '.' . $css . '.css'} />);

						}

					}

				}

			}



			return $output;

		}



		private static function renderJS($resourcePath, $viewSettings) {

            $output = <x:frag></x:frag>;

			if (isset($viewSettings['js'])) {

					if (is_array($viewSettings['js'])) {

							// Link the resources
							foreach ($viewSettings['js'] as $row => $value) {

									if (ENVIRONMENT === 'DEV') {

											// Include un-compiled js for development environments
											$output->appendChild(<script src={PROTOCOL . '://' . SITE_DOMAIN . $resourcePath . '.js/' . $row . '.js'}></script>);

											continue;

									}

                                $output->appendChild(<script src={PROTOCOL . '://' . SITE_DOMAIN . $resourcePath . 'js/' . $row . '.min.js'}></script>);

							}

					}

			}



			return $output;

		}



		/**
		 * @param bool $value
		 * @return bool
		 */

		public function setRendered($value)

		{

			if ($value === false) {

				$this->rendered = $value;



				return true;

			} elseif ($value === true) {

				$this->rendered = $value;



				return true;

			}



			return false;

		}

		public static function safeHTML($html) {
			$object = new \DomDocument();
			$object->loadHTML(trim($html));
			$object = self::parseHTMLNode($object);
			$html = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body>', '', trim($object->saveHTML()));
			$html = str_replace('</body></html>', '', $html);
			return $html;
		}

		protected static function parseHTMLNode($node, $parent = null) {
			if(in_array($node->nodeName, self::$nodeWhiteList)) {
				if($node->hasChildNodes()) {
					foreach ($node->childNodes as $childNode) {
						$childNode = self::parseHTMLNode($childNode, $node);
					}
				}

				switch($node->nodeName) {
					case '#text':
						$node->nodeValue = <x:frag>{$node->nodeValue}</x:frag>;
					break;

					case 'iframe':
						$node->setAttribute('sandbox', '');
						break;

					default:
					break;
				}
			} else {
				if(!is_null($parent)) {
					$parent->removeChild($node);
				} else {
					return new \DomDocument();
				}
			}

			$node = self::parseNodeAttributes($node);


			return $node;
		}

		protected static function parseNodeAttributes($node) {
			if($node->hasAttributes()) {
				$attributes = $node->attributes;
				foreach ($attributes as $attr) {
					if(in_array($attr->nodeName, array_keys(self::$nodeAttributeWhitelist))) {
						if(!empty(self::$nodeAttributeWhitelist[$attr->nodeName])) {
							if(preg_match('/' . self::$nodeAttributeWhitelist[$attr->nodeName] . '/m', $attr->nodeValue)) {
								$node->setAttribute($attr->nodeName, '');
							}
						}
					} else {
						$node->setAttribute($attr->nodeName, '');
					}
				}
			}

			return $node;
		}

		public function init() :int {
			return 1;
		}

		public function sendHeader():void {

		}

	}
