<?hh // decl


	namespace HC;

	require_once 'traits/escape.trait.php';



	/**
	 * Class Form
	 */

	class Form extends Core

	{

		/**
		 * @var string
		 */

		protected $formName = '';



		/**
		 * @var string
		 */

		protected $formStartHTML = '';



		/**
		 * @var string
		 */

		protected $formBodyHTML = '';



		/**
		 * @var string
		 */

		protected $formEndHTML = '';



		use EscapeTrait;



		/**
		 * Form Constructor
		 * Call the function to generate a form, pass through options to customize.
		 *
		 *
		 * @param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 * @param name => (true, false)          [false]     (Specifies the name and id attributes of the form element)
		 * @param action => (true, false)          [false]     (Specifies where to send the form-data when a form is submitted)
		 * @param onsubmit => (true, false)          [false]     (Set the onsubmit attribute of the form element)
		 * @param autocomplete => (true, false)          [false]     (Specifies whether a form should have autocomplete on or off)
		 * @param style => (string, false)              [false]         (Specifies the style of the form element)
		 * @param novalidate => (true, false)          [false]     (Specifies that the form should not be validated when submitted)
		 * @param method => ('get', 'post', false) [false]     (Specifies the HTTP method to use when sending form-data)
		 * @param target => ('_blank', '_self', '_parent', '_top', false) [false]
		 * @param enctype => ('application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain', false) [false] (Specifies how the form-data should be encoded when submitting it to the server (only for method="post"))
		 * @param append => string                      ['']            (Specifies a string that is appended to the form element)
		 * @param prepend => string                     ['']            (Specifies a string that is prepended to the form element)
		 *
		 * @return boolean
		 */

		public function __construct($settings = [])

		{



			$settings = $this->parseOptions($settings, [

				'name' => false,

				'class' => false,

				'action' => false,

				'onsubmit' => false,

				'autocomplete' => false,

				'style' => false,

				'novalidate' => false,

				'method' => false,

				'target' => false,

				'enctype' => false,

                'data' => false,

				'append' => '',

				'prepend' => ''

			]);



			// Start the form, with a name/id if defined
			if (is_string($settings['name'])) {

				$settings['name'] = $this->escapeTrait($settings['name']);



				// Start Creating the form HTML
				$this->formStartHTML = $settings['prepend'] . '<form id="' . $settings['name'] . '" name="' . $settings['name'] . '"';



				// Set the form name
				$this->formName = $settings['name'];

			} else {

				// Start Creating the form HTML
				$this->formStartHTML = $settings['prepend'] . '<form';

			}



			// Allow setting the class
			if (is_string($settings['class'])) {

				$this->formStartHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

			};



			// Allow setting the action
			if (is_string($settings['action'])) {

				$this->formStartHTML .= ' action="' . $this->escapeTrait($settings['action']) . '"';

			};



			// Allow setting the onSubmit
			if (is_string($settings['onsubmit'])) {

				$this->formStartHTML .= ' onsubmit="' . $this->escapeTrait($settings['onsubmit']) . '"';

			};



			// Allow setting the autocomplete
			if ($settings['autocomplete'] !== false) {

				$this->formStartHTML .= ' autocomplete="on"';

			};



			// Allow setting the novalidate
			if ($settings['novalidate'] !== false) {

				$this->formStartHTML .= ' novalidate="novalidate"';

			};



			// Allow setting the method
			if (is_string($settings['method'])) {

				$this->formStartHTML .= ' method="' . $this->escapeTrait($settings['method']) . '"';

			};



			// Allow setting the target
			if (is_string($settings['target'])) {

				$this->formStartHTML .= ' target="' . $this->escapeTrait($settings['target']) . '"';

			};



			// Allow setting the enctype
			if (is_string($settings['enctype'])) {

				$this->formStartHTML .= ' enctype="' . $this->escapeTrait($settings['enctype']) . '"';

			};



			// Close off the element
			$this->formStartHTML .= '>';



			// Make sure the form gets closed
			$this->formEndHTML = '</form>' . $settings['append'];



			return true;

		}



		public function __destruct()

		{

			$this->formName = null;

			$this->formStartHTML = null;

			$this->formBodyHTML = null;

			$this->formEndHTML = null;

		}



		/**
		 * @param $string
		 * @return bool
		 */

		public function prepend($string)

		{

			if (is_string($string)) {

                $this->formStartHTML = $string . $this->formStartHTML;



				return true;

			} else if(is_object($string)) {

                if(method_exists($string, 'render')){

                    $this->formStartHTML = $string->render() . $this->formStartHTML;

                }

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

				$this->formEndHTML .= $string;

				return true;

			} else if(is_object($string)) {

                if(method_exists($string, 'render')){

                    $this->formEndHTML .= $string->render();

                }

            }



			return false;

		}



		/**
		 * @param $string
		 * @return bool
		 */

		public function prependToBody($string)

		{

			if (is_string($string)) {

				$this->formBodyHTML = $string . $this->formBodyHTML;



				return true;

			}



			return false;

		}



		/**
		 * @param $string
		 * @return bool
		 */

		public function appendToBody($string)

		{

			if (is_string($string)) {

				$this->formBodyHTML = $this->formBodyHTML . $string;



				return true;

			}



			return false;

		}



		/**
		 * Form Render
		 * Call the function to render the form created by this class
		 *
		 * @return string
		 */

		public function render()

		{



			// Return all the generated html
			return $this->formStartHTML . $this->formBodyHTML . $this->formEndHTML;

		}



	}

