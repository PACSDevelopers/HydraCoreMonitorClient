<?hh // decl


	namespace HC;

	require_once 'traits/escape.trait.php';

	require_once 'traits/input.trait.php';



	/**
	 * Class Input
	 */

	class Input extends Core

	{

		/**
		 * @var string
		 */

		protected $inputHTML = '';

		use InputTrait;



		/**
		 * Text Input Constructor
		 * Call the function to generate a text input, pass through options to customize.
		 *
		 * @param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 * @param createElement => (true, false)        [true]          (Specifies whether to generate the element as part of the form, or return the html)
		 * @param required => (true, false)            [false]         (Specifies that an input field must be filled out before submitting the form)
		 * @param name => (true, false)        [false]         (Specifies a name and id for the input)
		 * @param value => (true, false)        [ucfirst(type)] (Specifies an initial value for the input)
		 * @param class => (true, false)        [false]         (Specifies the class of the input)
		 * @param onclick => (true, false)        [false]         (Specifies the onClick of the input)
		 * @param autofocus => (true, false)        [false]         (Specifies that a input should automatically get focus when the page loads)
		 * @param style => (string, false)              [false]         (Specifies the style of the input)
		 * @param disabled => (true, false)        [false]         (Specifies that a input should be disabled)
		 * @param readonly => (true, false)       [false]         (Specifies that an input field is read-only)
		 * @param maxlength => (0-999, false)      [false]         (Specifies the maximum number of characters allowed in an <input> element)
		 * @param pattern => (regexp, false)     [false]         (Specifies a regular expression that an <input> element's value is checked against)
		 * @param spellcheck => (true, false)       [false]         (Specifies whether the element is to have its spelling and grammar checked or not)
		 * @param append => string                      ['']            (Specifies a string that is appended to the input)
		 * @param prepend => string                     ['']            (Specifies a string that is prepended to the input)
		 *
		 * @return boolean
		 */

		public function __construct($settings = [])

		{



			$this->inputHTML = $this->inputTrait($this->parseOptions($settings, [

				'name' => false,

				'value' => false,

				'class' => false,

                'onclick' => false,

                'onchange' => false,

				'disabled' => false,

				'required' => false,

				'autofocus' => false,

				'multiple' => false,

				'accept' => false,

				'style' => false,

				'readonly' => false,

				'maxlength' => false,

				'pattern' => false,

				'spellcheck' => false,

                'data' => false,

				'type' => 'text',

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
		 * Text Input Render
		 * Call the function to render the text input created by this class
		 *
		 * @return string
		 */

		public function render()

		{



			// Return all the generated html
			return $this->inputHTML;

		}

	}

