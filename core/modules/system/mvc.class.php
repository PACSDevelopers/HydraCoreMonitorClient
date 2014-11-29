<?hh // decl


	namespace HC;



	/**
	 * Class MVC
	 *
	 */

	class MVC extends Core

	{

		// Setup class public variables


		// Setup class protected variables
		/**
		 * @var array
		 */

		protected $settings = [];



		protected $GET = [];



		protected $POST = [];

        protected $apiFunction = false;

		// Setup Constructor


		/**
		 * @param array $settings
		 */

		public function __construct($settings = [])

		{
            if(isset($GLOBALS['skipRender']) && $GLOBALS['skipRender']) {
                return false;
            }

			// Parse default options
			$this->settings = $this->parseOptions($settings, ['rewrites' => [], 'enabled' => false, 'api' => false]);

            if($this->settings['enabled'] !== true) {
                return false;
            }


			$this->GET = $_GET;

			$this->POST = $_POST;

			$_GET = $_POST = null;

			unset($_GET, $_POST);



			if(isset($_SERVER['REQUEST_URI'])) {

				$uri = $this->parseURI($_SERVER['REQUEST_URI']);

                if(!$uri) {
                    return false;
                }

				$path = realpath(HC_LOCATION . '/public' . $uri);

				if(is_file($path)) {

					chdir(dirname($path));

                    if(ENVIRONMENT === 'DEV') {
                        if(!\HC\Error::checkPHPSyntax($path)) {
                            return false;
                        }
                    }

					require_once $path;

                    
                    if($this->settings['enabled'] === true) {

						$class = $this->getPageName($path);

						if(class_exists($class)) {

							if(\HC\Site::extendsHydraCoreClass($class,'Page')) {

								try {

									$page = new $class($this->GET, $this->POST);
                                    $status = false;

                                    if($this->settings['api'] && $this->apiFunction) {
                                        if(method_exists($page, $this->apiFunction)) {
                                            $status = $page->{$this->apiFunction}($this->GET, $this->POST);
                                        } else {
                                            $status = 404;
                                        }
                                    } else {
                                        $status = $page->init($this->GET, $this->POST);
                                    }

									if($status === 302 || $status === 301) {
										$page->setRendered(true);
										$page = null;
										unset($page);
										return true;
									} else if($status !== true && $status !== 1 && $status !== 200) {
									    $page->setRendered(true);
									    $page = null;
									    unset($page);
                                        $error = new \HC\Error();
										$error->generateErrorPage($status);
									} else if(!REGISTER_SHUTDOWN) {
										echo $page->render();
									}

                  return true;
								} catch (\Exception $exception) {
									// Trigger the error handler, based on exception details
                                    Error::exceptionHandler($exception);
                  return true;
								}
							}

						}

					} else {


                        return true;

					}

				}

			}

            if($this->settings['enabled'] === true) {
                if (isset($path)) {
                    if (is_file($path)) {

                        if ($this->settings['enabled'] === true) {

                            if (isset($class)) {

                                // @todo: throw exception
                                \HC\Error::errorHandler(404, 'No HydraCore page was defined, expected: ' . $class, $path, 0);
                                return false;
                                
                            } else {

                                // @todo: throw exception
                                \HC\Error::errorHandler(404, 'No HydraCore page was defined.', $path, 0);
                                return false;
                            }

                        }

                    }
                }

                $error = new \HC\Error();
                $error->generateErrorPage(404);
            }
            
			return false;

		}



		public function __destruct()

		{

			$this->settings = null;

		}



		private function getPageName($path) {

			$pageName = explode('public', $path);

			if(isset($pageName[1])) {

				$pageName = mb_substr(mb_substr($pageName[1], 0, -3), 1);

				if(mb_strpos($pageName, '/') !== false) {

					$parts = explode('/', $pageName);



					$pageName = '\HCPublic\\';

					$isPage = true;

					foreach($parts as $part) {

						if($part == 'ajax') {
							$isPage = false;
						}



						$pageName .= ucfirst($part) . '\\';

					}



					$pageName = mb_substr($pageName, 0, -1);



					if($isPage) {

						$pageName .= 'Page';

					} else {

						$pageName .= 'Ajax';

					}

				} else {

					$pageName = ucwords($pageName);

					$pageName = str_replace(' ', '\\', $pageName);

					$pageName = '\\HCPublic\\' . $pageName . 'Page';

				}

				return $pageName;

			}



			return false;

		}



		private function parseURI($uri) {

			if(mb_strpos($uri, '?') !== false) {
				$uri = $this->parseQueryString($uri);
			}


            if(mb_strlen($uri) > 1) {
                $lastChar = mb_substr($uri, -1);
                if($lastChar === '/') {
                    $newUri = rtrim($uri, '/');
                    if(is_file(HC_LOCATION . '/public' . $newUri . '.hh')) {
                        if(PHP_SAPI !== 'cli') {
                            header('Location: ' . $newUri);
                        }
                        return false;
                    } else if($this->settings['api']) {
                        $pos = strrpos($newUri, '/');
                        $this->apiFunction = mb_substr($newUri, $pos+1);
                        $newUri = mb_substr($newUri, 0, $pos);

                        if(is_file(HC_LOCATION . '/public' . $newUri . '.hh') && $this->apiFunction) {
                            if(PHP_SAPI !== 'cli') {
                                header('Location: ' . $newUri . '/' . $this->apiFunction);
                            }
                            return false;
                        }
                    }

                    return '';
                }
            }


			switch($uri) {

				case '/':

					return '/index.hh';

				break;

				default:

					return $this->parseRewrites($uri);

				break;

			}

		}



		private function parseQueryString($str) {

			parse_str(mb_substr(mb_strstr($str, '?'), 1), $returnvars);



			foreach($returnvars as $key => $value) {

				$this->GET[$key] = $value;

			}



			return strtok($str, '?');

		}



		private function parseRewrites($uri) {

			$isMatch = false;

			foreach($this->settings['rewrites'] as $key => $value) {

				preg_match_all($key, $uri, $matches);

				if(!empty($matches[1])) {

					$uri = $value;

					foreach($matches as $matchName => $matchValue) {

						if(is_string($matchName)) {

							$this->GET[$matchName] = $matchValue[0];

						}

					}

					$isMatch = true;

					break;

				}

			}

            if($this->settings['api']) {
                if(!is_file(HC_LOCATION . '/public' . $uri . '.hh')) {
                    $pos = strrpos($uri, '/');
                    $tempURI = substr($uri, 0, $pos);

                    if(is_file(HC_LOCATION . '/public' . $tempURI . '.hh')) {
                        $this->apiFunction = mb_substr($uri, $pos+1);
                        if($this->apiFunction && mb_strlen($this->apiFunction) > 0) {
                            $this->apiFunction .= '_' . mb_strtolower($_SERVER['REQUEST_METHOD']);
                            $uri = $tempURI;
                        } else {
                            $this->apiFunction = false;
                        }
                    }
                }
            }

            if(mb_substr($uri,0,7) === '/error/') {
                $code = mb_substr($uri, mb_strpos($uri, '/' ) + 1);
                if($code && is_numeric($code)) {
                    $error = new \HC\Error();
                    $error->generateErrorPage($code);
                    return false;
                }
            }

			if($isMatch) {

				return $uri;

			} else {

				return $uri . '.hh';

			}

		}

	}
