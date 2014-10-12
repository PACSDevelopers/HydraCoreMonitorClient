<?hh // decl


	namespace HC;

	require_once 'traits/escape.trait.php';

	require_once 'traits/button.trait.php';



	/**
	 * Class Button
	 */

	class Button extends Core

	{

		/**
		 * @var string
		 */

		protected $inputHTML = '';

		use ButtonTrait;



		/**
		 * Button Constructor
		 * Call the function to generate a button, pass through options to customize.
		 *
		 * @@param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 * @param type => ('button', 'submit', 'reset') ['button']      (Specifies the type of the button)
		 * @param name => (true, false)                 [false]         (Specifies a name and id for the button)
		 * @param value => (true, false)                [ucfirst(type)] (Specifies an initial value for the button)
		 * @param class => (true, false)                [false]         (Specifies the class of the button)
		 * @param onclick => (true, false)              [false]         (Specifies the onClick of the button)
		 * @param autofocus => (true, false)            [false]         (Specifies that a button should automatically get focus when the page loads)
		 * @param style => (string, false)              [false]         (Specifies the style of the button)
		 * @param disabled => (true, false)             [false]         (Specifies that a button should be disabled)
		 * @param append => string                      ['']            (Specifies a string that is appended to the button)
		 * @param prepend => string                     ['']            (Specifies a string that is prepended to the button)
		 *
		 * @return boolean
		 */

		public function __construct($settings = [])

		{



			$this->inputHTML = $this->buttonTrait($this->parseOptions($settings, [

				'type' => 'button',

				'name' => false,

				'value' => false,

				'checked' => false,

				'class' => false,

				'onclick' => false,

				'autofocus' => false,

				'style' => false,

				'disabled' => false,

                'data' => false,

				'append' => '',

				'prepend' => '',

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
		 * Button Render
		 * Call the function to render the button created by this class
		 *
		 * @return string
		 */

		public function render()

		{



			// Return all the generated html
			return $this->inputHTML;

		}

	}

