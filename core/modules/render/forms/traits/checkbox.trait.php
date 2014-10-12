<?hh


	namespace HC;



	require_once 'escape.trait.php';



	/**
	 * Class CheckBoxTrait
	 */

	trait CheckBoxTrait

	{

		use EscapeTrait;



		/**
		 * Checkbox Constructor
		 * Call the function to generate a checkbox input, pass through options to customize.
		 *
		 * @param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 * @param createElement => (true, false)   [true]      (Specifies whether to generate the element as part of the form, or return the html)
		 * @param required => (true, false)   [false]     (Specifies that an input field must be filled out before submitting the form)
		 * @param name => (true, false)   [false]     (Specifies a name and id for the input)
		 * @param checked => (true, false)   [false]     (Specifies whether the checkbox is checked)
		 * @param class => (true, false)   [false]     (Specifies the class of the input)
		 * @param onclick => (true, false)   [false]     (Specifies the onClick of the input)
		 * @param autofocus => (true, false)   [false]     (Specifies that a input should automatically get focus when the page loads)
		 * @param disabled => (true, false)   [false]     (Specifies that a input should be disabled)
		 *
		 * @return string
		 */

		protected function checkboxTrait($settings = [])

		{



			// Draw the start of the checkbox
			$tempHTML = $settings['prepend'] . '<input type="checkbox"';

			// Allow disabling the button
			if ($settings['disabled'] === true) {

				$tempHTML .= ' disabled="disabled"';

			}

			// Allow setting the autofocus
			if ($settings['autofocus'] === true) {

				$tempHTML .= ' autofocus="autofocus"';

			}

			// Allow setting the style
			if (is_string($settings['style'])) {

				$tempHTML .= ' style="' . $this->escapeTrait($settings['style']) . '"';

			}

			// Allow setting the checked
			if ($settings['checked'] === true) {

				$tempHTML .= ' checked="checked"';

			}

			// Allow setting the required
			if ($settings['required'] === true) {

				$tempHTML .= ' required="required"';

			}



			// Allow setting the class
			if (is_string($settings['class'])) {

				$tempHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

			}

			// Allow setting the on click event
			if (is_string($settings['onclick'])) {

				$tempHTML .= ' onclick="' . $this->escapeTrait($settings['onclick']) . '"';

			}

			// Allow setting the name
			if (is_string($settings['name'])) {

				$tempHTML .= ' name="' . $this->escapeTrait($settings['name']) . '" id="' . $this->escapeTrait($settings['name']) . '"';

			}

			// Allow setting the name
			if (is_string($settings['value'])) {

				$tempHTML .= ' value="' . $this->escapeTrait($settings['value']) . '"';

			}



            // Allow setting data attributes
            if (is_array($settings['data'])) {

                foreach($settings['data'] as $key => &$value) {

                    $tempHTML .= ' data-' . $key . '="' . $this->escapeTrait($value) . '"';

                }

            }



			// Handle the html
			return $tempHTML . '>' . $settings['append'];

		}

	}

