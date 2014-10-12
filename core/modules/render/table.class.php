<?hh // decl


	namespace HC;

	require_once 'forms/traits/escape.trait.php';



	/**
	 * Class Table
	 */

	class Table extends Core

	{

		/**
		 * @var string
		 */

		protected $inputHTML = '';

        protected $settings;

        protected $lastStep = 0;



		use EscapeTrait;



		/**
		 * Table Constructor
		 * Call the function to generate a table, pass through options to customize.
		 *
		 * @param (int|bool|array|string|null)[] $settings [] (The settings below:)
		 *
		 * @param required => (true, false)            [false]         (Specifies that an table field must be filled out before submitting the form)
		 * @param name => (true, false)        [false]         (Specifies a name and id for the table)
		 * @param values => (['name' => 'Name 1', 'value' => 'Value 1', 'disabled' => false, 'tableed' => false]) (Specifies initial values for the table)
		 * @param class => (true, false)        [false]         (Specifies the class of the table)
		 * @param onclick => (true, false)        [false]         (Specifies the onClick of the table)
		 * @param autofocus => (true, false)        [false]         (Specifies that a table should automatically get focus when the page loads)
		 * @param style => (string, false)              [false]         (Specifies the style of the table)
		 * @param disabled => (true, false)        [false]         (Specifies that a table should be disabled)
		 * @param readonly => (true, false)       [false]         (Specifies that an table field is read-only)
		 * @param append => string                      ['']            (Specifies a string that is appended to the table)
		 * @param prepend => string                     ['']            (Specifies a string that is prepended to the table)
		 *
		 * @return boolean
		 */

		public function __construct($settings = [])

		{



			$settings = $this->parseOptions($settings, [

				'name' => false,

				'class' => false,

				'style' => false,

                'data' => false,

				'append' => '',

				'prepend' => ''

			]);



            $this->settings = $settings;



			// Draw the start of the input
			$tempHTML = $settings['prepend'] . '<table';



			// Allow setting the class
			if (is_string($settings['class'])) {

				$tempHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

			}



			// Allow setting the style
			if (is_string($settings['style'])) {

				$tempHTML .= ' style="' . $this->escapeTrait($settings['style']) . '"';

			}



			// Allow setting the name
			if (is_string($settings['name'])) {

				$settings['name'] = $this->escapeTrait($settings['name']);

				$tempHTML .= ' id="' . $settings['name'] . '"';

			}



            // Allow setting data attributes
            if (is_array($settings['data'])) {

                foreach($settings['data'] as $key => &$value) {

                    $this->inputHTML .= ' data-' . $key . '="' . $this->escapeTrait($value) . '"';

                }

            }



			// Handle the html
			$tempHTML .= '>';

            $this->inputHTML = $tempHTML;



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

				$this->inputHTML .= $string;



				return true;

			}



			return false;

		}



        public function openHeader($settings = []) {

            $this->lastStep = 1;



            $settings = $this->parseOptions($settings, [

                'name' => false,

                'class' => false,

                'style' => false,

                'data' => false,

            ]);



            $this->inputHTML .= '<thead';



            // Allow setting the class
            if (is_string($settings['class'])) {

                $this->inputHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

            }



            // Allow setting the style
            if (is_string($settings['style'])) {

                $this->inputHTML .= ' style="' . $this->escapeTrait($settings['style']) . '"';

            }



            // Allow setting the name
            if (is_string($settings['name'])) {

                $settings['name'] = $this->escapeTrait($settings['name']);

                $this->inputHTML .= ' id="' . $settings['name'] . '"';

            }



            // Allow setting data attributes
            if (is_array($settings['data'])) {

                foreach($settings['data'] as $key => &$value) {

                    $this->inputHTML .= ' data-' . $key . '="' . $this->escapeTrait($value) . '"';

                }

            }



            $this->inputHTML .= '>';



            return $this;

        }



        public function openBody($settings = []) {

            $settings = $this->parseOptions($settings, [

                'name' => false,

                'class' => false,

                'style' => false,

                'data' => false,

            ]);



            $this->inputHTML .= '<tbody';



            // Allow setting the class
            if (is_string($settings['class'])) {

                $this->inputHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

            }



            // Allow setting the style
            if (is_string($settings['style'])) {

                $this->inputHTML .= ' style="' . $this->escapeTrait($settings['style']) . '"';

            }



            // Allow setting the name
            if (is_string($settings['name'])) {

                $settings['name'] = $this->escapeTrait($settings['name']);

                $this->inputHTML .= ' id="' . $settings['name'] . '"';

            }



            // Allow setting data attributes
            if (is_array($settings['data'])) {

                foreach($settings['data'] as $key => &$value) {

                    $this->inputHTML .= ' data-' . $key . '="' . $this->escapeTrait($value) . '"';

                }

            }



            $this->inputHTML .= '>';



            return $this;

        }



        public function openFooter($settings = []) {

            $settings = $this->parseOptions($settings, [

                'name' => false,

                'class' => false,

                'style' => false,

                'data' => false,

            ]);



            $this->inputHTML .= '<tfoot';



            // Allow setting the class
            if (is_string($settings['class'])) {

                $this->inputHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

            }



            // Allow setting the style
            if (is_string($settings['style'])) {

                $this->inputHTML .= ' style="' . $this->escapeTrait($settings['style']) . '"';

            }



            // Allow setting the name
            if (is_string($settings['name'])) {

                $settings['name'] = $this->escapeTrait($settings['name']);

                $this->inputHTML .= ' id="' . $settings['name'] . '"';

            }



            // Allow setting data attributes
            if (is_array($settings['data'])) {

                foreach($settings['data'] as $key => &$value) {

                    $this->inputHTML .= ' data-' . $key . '="' . $this->escapeTrait($value) . '"';

                }

            }



            $this->inputHTML .= '>';



            return $this;

        }



        public function openRow($settings = []) {

            $settings = $this->parseOptions($settings, [

                'name' => false,

                'class' => false,

                'style' => false,

                'data' => false,

            ]);



            $this->inputHTML .= '<tr';



            // Allow setting the class
            if (is_string($settings['class'])) {

                $this->inputHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

            }



            // Allow setting the style
            if (is_string($settings['style'])) {

                $this->inputHTML .= ' style="' . $this->escapeTrait($settings['style']) . '"';

            }



            // Allow setting the name
            if (is_string($settings['name'])) {

                $settings['name'] = $this->escapeTrait($settings['name']);

                $this->inputHTML .= ' id="' . $settings['name'] . '"';

            }



            // Allow setting data attributes
            if (is_array($settings['data'])) {

                foreach($settings['data'] as $key => &$value) {

                    $this->inputHTML .= ' data-' . $key . '="' . $this->escapeTrait($value) . '"';

                }

            }



            $this->inputHTML .= '>';



            return $this;

        }



        public function column($settings = []) {

            $settings = $this->parseOptions($settings, [

                'name' => false,

                'class' => false,

                'style' => false,

                'data' => false,

                'value' => ''

            ]);



            $element = 'td';

            if($this->lastStep === 1) {

                $element = 'th';

            }

            $this->inputHTML .= '<' . $element;



            // Allow setting the class
            if (is_string($settings['class'])) {

                $this->inputHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

            }



            // Allow setting the style
            if (is_string($settings['style'])) {

                $this->inputHTML .= ' style="' . $this->escapeTrait($settings['style']) . '"';

            }



            // Allow setting the name
            if (is_string($settings['name'])) {

                $settings['name'] = $this->escapeTrait($settings['name']);

                $this->inputHTML .= ' id="' . $settings['name'] . '"';

            }



            // Allow setting data attributes
            if (is_array($settings['data'])) {

                foreach($settings['data'] as $key => &$value) {

                    $this->inputHTML .= ' data-' . $key . '="' . $this->escapeTrait($value) . '"';

                }

            }



            $this->inputHTML .= '>' . $settings['value'] . '</' . $element . '>';



            return $this;

        }



        public function closeRow() {

            $this->inputHTML .= '</tr>';



            return $this;

        }



        public function closeHeader() {

            $this->inputHTML .= '</thead>';



            $this->lastStep = 0;



            return $this;

        }



        public function closeBody() {

            $this->inputHTML .= '</tbody>';



            return $this;

        }



        public function closeFooter() {

            $this->inputHTML .= '</tfoot>';

            return $this;

        }



		/**
		 * Table Render
		 * Call the function to render the table created by this class
		 *
		 * @return string
		 */

		public function render()

		{



			// Return all the generated html
			return $this->inputHTML . '</table>' . $this->settings['append'];

		}

	}

