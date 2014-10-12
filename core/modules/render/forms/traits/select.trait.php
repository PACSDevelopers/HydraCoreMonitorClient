<?hh


	namespace HC;



	require_once 'escape.trait.php';



	/**
	 * Class SelectTrait
	 */

	trait SelectTrait

	{

		use EscapeTrait;



		/**
		 * Select Constructor
		 * Call the function to generate a text input, pass through options to customize.
		 *
		 * @param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 * @param required => (true, false)            [false]         (Specifies that an input field must be filled out before submitting the form)
		 * @param name => (true, false)        [false]         (Specifies a name and id for the input)
		 * @param values => (['name' => 'Name 1', 'value' => 'Value 1', 'disabled' => false, 'selected' => false]) (Specifies initial values for the select, use sub array to set grouping)
		 * @param class => (true, false)        [false]         (Specifies the class of the input)
		 * @param onclick => (true, false)        [false]         (Specifies the onClick of the input)
		 * @param autofocus => (true, false)        [false]         (Specifies that a input should automatically get focus when the page loads)
		 * @param disabled => (true, false)        [false]         (Specifies that a input should be disabled)
		 * @param readonly => (true, false)       [false]         (Specifies that an input field is read-only)
		 *
		 * @return string
		 */

		protected function selectTrait($settings = [])

		{



			// Draw the start of the input
			$tempHTML = $settings['prepend'] . '<select';

			// Allow disabling the button
			if ($settings['disabled'] !== false) {

				$tempHTML .= ' disabled="disabled"';

			}

			// Allow setting the autofocus
			if ($settings['autofocus'] !== false) {

				$tempHTML .= ' autofocus="autofocus"';

			}

			// Allow setting the required
			if ($settings['required'] !== false) {

				$tempHTML .= ' required="required"';

			}

            // Allow setting the readonly
            if ($settings['multiple'] !== false) {

                $tempHTML .= ' multiple="multiple"';

            }

            // Allow setting the size
            if (is_int($settings['size'])) {

                $tempHTML .= ' size="' . $this->escapeTrait($settings['size']) . '"';

            }



			// Allow setting the class
			if (is_string($settings['class'])) {

				$tempHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

			}

			// Allow setting the style
			if (is_string($settings['style'])) {

				$tempHTML .= ' style="' . $this->escapeTrait($settings['style']) . '"';

			}

            // Allow setting the on click event
            if (is_string($settings['onclick'])) {

                $tempHTML .= ' onclick="' . $this->escapeTrait($settings['onclick']) . '"';

            }



            // Allow setting the on change event
            if (is_string($settings['onchange'])) {

                $tempHTML .= ' onchange="' . $this->escapeTrait($settings['onchange']) . '"';

            }

            // Allow setting the name
            if (is_string($settings['name'])) {

                $settings['name'] = $this->escapeTrait($settings['name']);

                $tempHTML .= ' name="' . $settings['name'] . '" id="' . $settings['name'] . '"';

            }



            // Allow setting data attributes
            if (is_array($settings['data'])) {

                foreach($settings['data'] as $key => &$value) {

                    $tempHTML .= ' data-' . $key . '="' . $this->escapeTrait($value) . '"';

                }

            }



			// Handle the html
			return $tempHTML . '>';

		}

	}

