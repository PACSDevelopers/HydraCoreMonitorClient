<?hh // decl


	namespace HC;

	require_once 'traits/escape.trait.php';

	require_once 'traits/select.trait.php';



	/**
	 * Class Select
	 */

	class Select extends Core

	{

		/**
		 * @var string
		 */

		protected $inputHTML = '';

        protected $settings;

		use SelectTrait;



		/**
		 * Select Constructor
		 * Call the function to generate a select, pass through options to customize.
		 *
		 * @param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 * @param required => (true, false)            [false]         (Specifies that an select field must be filled out before submitting the form)
		 * @param name => (true, false)        [false]         (Specifies a name and id for the select)
		 * @param values => (['name' => 'Name 1', 'value' => 'Value 1', 'disabled' => false, 'selected' => false]) (Specifies initial values for the select)
		 * @param class => (true, false)        [false]         (Specifies the class of the select)
		 * @param onclick => (true, false)        [false]         (Specifies the onClick of the select)
		 * @param autofocus => (true, false)        [false]         (Specifies that a select should automatically get focus when the page loads)
		 * @param style => (string, false)              [false]         (Specifies the style of the select)
		 * @param disabled => (true, false)        [false]         (Specifies that a select should be disabled)
		 * @param readonly => (true, false)       [false]         (Specifies that an select field is read-only)
		 * @param append => string                      ['']            (Specifies a string that is appended to the select)
		 * @param prepend => string                     ['']            (Specifies a string that is prepended to the select)
		 *
		 * @return boolean
		 */

		public function __construct($settings = [])

        {

            $settings = $this->parseOptions($settings, [

                'name' => false,

                'class' => false,

                'onclick' => false,

                'onchange' => false,

                'disabled' => false,

                'required' => false,

                'autofocus' => false,

                'style' => false,

                'multiple' => false,

                'size' => false,

                'data' => false,

                'append' => '',

                'prepend' => ''

            ]);



            $this->inputHTML = $this->selectTrait($settings);



            $this->settings = $settings;



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



        public function openGroup($settings = []) {

            $settings = $this->parseOptions($settings, [

                'name'     => false,

                'disabled' => false,

                'label' => false,

            ]);

            // Create each option
            $this->inputHTML .= '<optgroup';



            // Disable the element if defined
            if ($settings['disabled'] !== false) {

                $this->inputHTML .= ' disabled="disabled"';

            }



            $settings['name'] = $this->escapeTrait($settings['name']);

            $settings['label'] = $this->escapeTrait($settings['label']);



            if(is_string($settings['label'])) {

                $this->inputHTML .= ' label="' . $settings['label'] . '"';

            }



            if(is_string($settings['name'])) {

                $this->inputHTML .= ' id="' . $settings['name'] . '">';

            } else {

                $this->inputHTML .= '>';

            }



            return $this;

        }



        public function closeGroup() {

            $this->inputHTML .= '</optgroup>';

            return $this;

        }



        public function option($settings = []) {



            $settings = $this->parseOptions($settings, [

                'name'     => false,

                'value'    => false,

                'disabled' => false,

                'selected' => false

            ]);

            // Create each option
            $this->inputHTML .= '<option';



            // Disable the element if defined
            if ($settings['disabled'] !== false) {

                $this->inputHTML .= ' disabled="disabled"';

            }



            // Set the element as selected if defined
            if ($settings['selected'] !== false) {

                $this->inputHTML .= ' selected="selected"';

            }



            $settings['name'] = $this->escapeTrait($settings['name']);

            $settings['value'] = $this->escapeTrait($settings['value']);



            if(is_string($settings['value'])) {

                $this->inputHTML .= ' value="' . $settings['value'] . '"';

                if(is_string($settings['name'])) {

                    $this->inputHTML .= '>' . $settings['name'];

                } else {

                    $this->inputHTML .= '>' . $settings['value'];

                }

            } else {

                $this->inputHTML .= '>';

            }



            $this->inputHTML .= '</option>';



            return $this;

        }



		/**
		 * Select Render
		 * Call the function to render the select created by this class
		 *
		 * @return string
		 */

		public function render()

		{



			// Return all the generated html
			return $this->inputHTML . '</select>' . $this->settings['append'];

		}

	}

