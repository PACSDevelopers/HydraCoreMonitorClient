<?hh // decl


	namespace HC;



	/**
	 * Class Upload
	 * @package HC
	 */

	class Upload extends Core

	{

		/**
		 * @var array
		 */

		protected $settings = [];



		// Constructor


		/**
		 * @param (int|string|null)[] $settings
		 */

		public function __construct($settings = [])

		{



			$settings = $this->parseOptions($settings, []);



			// Parse global / local settings
			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['upload'])) {

				if (is_array($globalSettings['upload'])) {

					$settings = $this->parseOptions($settings, $globalSettings['upload']);

				}

			}



			$this->settings = $settings;

		}



		/**
		 *
		 */

		public function __destruct()

		{

			$this->settings = null;

		}



		/**
		 * @return array
		 */

		public function get()

		{

			$files = [];

			if (isset($_FILES)) {

				foreach ($_FILES as $form) {

					if(!is_array($form['tmp_name'])) {

						$file = $form;



						// The temporary filename of the file in which the uploaded file was stored on the server.
						if (!isset($file['tmp_name'])) {

							$file['tmp_name'] = 'unknownFile';

						}



						if (!is_uploaded_file($file['tmp_name'])) {

							$file = null;

							unset($file);

							continue;

						}



						// The original name of the file on the client machine.
						if (!isset($file['name'])) {

							$file['name'] = $file['tmp_name'];

						}



						// The mime type of the file, if the browser provided this information. An example would be "image/gif". This mime type is however not checked on the PHP side and therefore don't take its value for granted.
						if (!isset($file['type'])) {

							$fileInformation = finfo_open(FILEINFO_MIME_TYPE);

							$file['type'] = finfo_file($fileInformation, $file['tmp_name']);

							finfo_close($fileInformation);

						}



						// The size, in bytes, of the uploaded file.
						if (!isset($file['size'])) {

							$file['size'] = filesize($file['tmp_name']);

						}



						// The error code associated with this file upload.
						if (!isset($file['error'])) {

							$file['error'] = 0;

						}



						$files[] = $file;

					} else {

						$fileCount = count($form['name']);

						// This is multiple files
						foreach(range(0, ($fileCount - 1)) as $fileNumber) {

							$file = [];



							// The temporary filename of the file in which the uploaded file was stored on the server.
							if (!isset($form['tmp_name'][$fileNumber])) {

								$file['tmp_name'] = 'unknownFile';

							} else {

								$file['tmp_name'] = $form['tmp_name'][$fileNumber];

							}



							if (!is_uploaded_file($file['tmp_name'])) {

								continue;

							}



							// The original name of the file on the client machine.
							if (!isset($form['name'][$fileNumber])) {

								$file['name'] = $form['tmp_name'][$fileNumber];

							} else {

								$file['name'] = $form['name'][$fileNumber];

							}



							// The mime type of the file, if the browser provided this information. An example would be "image/gif". This mime type is however not checked on the PHP side and therefore don't take its value for granted.
							if (!isset($form['type'][$fileNumber])) {

								$fileInformation = finfo_open(FILEINFO_MIME_TYPE);

								$file['type'] = finfo_file($fileInformation, $file['tmp_name']);

								finfo_close($fileInformation);

							} else {

								$file['type'] = $form['type'][$fileNumber];

							}



							// The size, in bytes, of the uploaded file.
							if (!isset($form['size'][$fileNumber])) {

								$file['size'] = filesize($file['tmp_name']);

							} else {

								$file['size'] = $form['size'][$fileNumber];

							}



							if($file['size'] <= 0) {

								$form['size'] = filesize($file['tmp_name']);

							}



							// The error code associated with this file upload.
							if (!isset($form['error'][$fileNumber])) {

								$file['error'] = 0;

							} else {

								$file['error'] = $form['error'][$fileNumber];

							}



							$files[] = $file;

						}

					}

				}

			}



			return $files;

		}



		/**
		 * @param $file
		 * @return bool
		 */

		public function delete($file)

		{

			return unlink($file);

		}



		/**
		 * @param $file
		 * @param $destination
		 * @return bool
		 */

		public function move($file, $destination)

		{

			return move_uploaded_file($file, $destination);

		}



	}

