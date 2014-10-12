<?hh // decl


	namespace HC;

	require_once 'traits/escape.trait.php';

	require_once 'traits/textarea.trait.php';



	/**
	 * Class TextArea
	 */

	class TextArea extends Core

	{

		/**
		 * @var string
		 */

		protected $inputHTML = '';

		use TextAreaTrait;



		/**
		 * TextArea Constructor
		 * Call the function to generate a textarea, pass through options to customize.
		 *
		 * @@param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 *
		 * @return boolean
		 */

		public function __construct($settings = [])

		{



			$this->inputHTML = $this->textAreaTrait($this->parseOptions($settings, [

				'name' => false,

				'value' => '',

				'class' => false,

                'onclick' => false,

                'onchange' => false,

				'disabled' => false,

				'required' => false,

				'autofocus' => false,

				'readonly' => false,

				'style' => false,

				'cols' => false,

				'rows' => false,

				'wrap' => false,

				'form' => false,

				'placeholder' => false,

				'maxlength' => false,

                'data' => false,

				'append' => '',

				'prepend' => ''

			]));



			return true;

		}



		public function __destruct()

		{

			$this->inputHTML = null;

		}



		/**
		 * @param $string
		 * @return bool
		 */

		public function prepend($string)

		{

			if (is_string($string)) {

				$this->inputHTML = $string . $this->inputHTML;



				return true;

			}



			return false;

		}



		/**
		 * @param $string
		 * @return bool
		 */

		public function append($string)

		{

			if (is_string($string)) {

				$this->inputHTML = $this->inputHTML . $string;



				return true;

			}



			return false;

		}



		/**
		 * TextArea Render
		 * Call the function to render the textarea created by this class
		 *
		 * @return string
		 */

		public function render()

		{



			// Return all the generated html
			return $this->inputHTML;

		}

	}

