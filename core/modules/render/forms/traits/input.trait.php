<?hh


	namespace HC;



	require_once 'escape.trait.php';



	/**
	 * Class InputTrait
	 */

	trait InputTrait

	{

		use EscapeTrait;



		/**
		 * Input Constructor
		 * Call the function to generate a input, pass through options to customize.
		 *
		 * @param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 * @param createElement => (true, false)        [true]          (Specifies whether to generate the element as part of the form, or return the html)
		 * @param type => (text, password)            [text]         (Specifies that the input field type)
		 * @param required => (true, false)            [false]         (Specifies that an input field must be filled out before submitting the form)
		 * @param name => (true, false)        [false]         (Specifies a name and id for the input)
		 * @param value => (true, false)        [ucfirst(type)] (Specifies an initial value for the input)
		 * @param class => (true, false)        [false]         (Specifies the class of the input)
		 * @param onclick => (true, false)        [false]         (Specifies the onClick of the input)
		 * @param autofocus => (true, false)        [false]         (Specifies that a input should automatically get focus when the page loads)
		 * @param disabled => (true, false)        [false]         (Specifies that a input should be disabled)
		 * @param readonly => (true, false)       [false]         (Specifies that an input field is read-only)
		 * @param maxlength => (0-999, false)      [false]         (Specifies the maximum number of characters allowed in an <input> element)
		 * @param pattern => (regexp, false)     [false]         (Specifies a regular expression that an <input> element's value is checked against)
		 * @param spellcheck => (true, false)       [false]         (Specifies whether the element is to have its spelling and grammar checked or not)
		 *
		 * @return string
		 */

		protected function inputTrait($settings = [])

		{



			// Draw the start of the input
			$tempHTML = $settings['prepend'] . '<input type="' . $this->escapeTrait($settings['type']) . '"';

			// Allow disabling the button
			if ($settings['disabled'] === true) {

				$tempHTML .= ' disabled="disabled"';

			}

			// Allow setting the style
			if (is_string($settings['style'])) {

				$tempHTML .= ' style="' . $this->escapeTrait($settings['style']) . '"';

			}

			// Allow setting the autofocus
			if ($settings['autofocus'] === true) {

				$tempHTML .= ' autofocus="autofocus"';

			}

			// Allow setting the required
			if ($settings['required'] === true) {

				$tempHTML .= ' required="required"';

			}

			// Allow setting the readonly
			if ($settings['readonly'] === true) {

				$tempHTML .= ' readonly="readonly"';

			}

			// Allow setting the readonly
			if ($settings['multiple'] === true) {

				$tempHTML .= ' multiple="multiple"';

			}

			// Allow setting the class
			if (is_string($settings['class'])) {

				$tempHTML .= ' class="' . $this->escapeTrait($settings['class']) . '"';

			}

			// Allow setting the on click event
			if (is_string($settings['onclick'])) {

				$tempHTML .= ' onclick="' . $this->escapeTrait($settings['onclick']) . '"';

			}



            // Allow setting the on click event
            if (is_string($settings['onchange'])) {

                $tempHTML .= ' onchange="' . $this->escapeTrait($settings['onchange']) . '"';

            }



			// Allow setting the maxlength
			if (is_integer($settings['maxlength'])) {

				$tempHTML .= ' maxlength="' . $this->escapeTrait($settings['maxlength']) . '"';

			}



			// Allow setting the accept
			if (is_string($settings['accept'])) {

				$tempHTML .= ' accept="' . $this->escapeTrait($settings['accept']) . '"';

			}

			// Allow setting the pattern
			if (is_string($settings['pattern'])) {

				$tempHTML .= ' pattern="' . $this->escapeTrait($settings['pattern']) . '"';

			}

			// Allow setting the spellcheck
			if ($settings['spellcheck'] === true) {

				$tempHTML .= ' spellcheck="true"';

			} else {

				$tempHTML .= ' spellcheck="false"';

			}



			// Allow setting the name
			if (is_string($settings['name'])) {

				$settings['name'] = $this->escapeTrait($settings['name']);

				if ($settings['multiple'] === true) {

					$tempHTML .= ' name="' . $settings['name'] . '[]" id="' . $settings['name'] . '"';

				} else {

					$tempHTML .= ' name="' . $settings['name'] . '" id="' . $settings['name'] . '"';

				}



			}



            // Allow setting data attributes
            if (is_array($settings['data'])) {

                foreach($settings['data'] as $key => &$value) {

                    $tempHTML .= ' data-' . $key . '="' . $this->escapeTrait($value) . '"';

                }

            }



			// Force setting the value
			if ($settings['value'] === false) {

				$settings['value'] = ucfirst($settings['type']);

			}



			$settings['value'] = $this->escapeTrait($settings['value']);



			// Handle the html
			return $tempHTML . ' value="' . $settings['value'] . '">' . $settings['append'];

		}

	}

