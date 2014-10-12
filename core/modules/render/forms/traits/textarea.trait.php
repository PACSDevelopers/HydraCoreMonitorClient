<?hh


	namespace HC;



	require_once 'escape.trait.php';



	/**
	 * Class TextAreaTrait
	 */

	trait TextAreaTrait

	{

		use EscapeTrait;



		/**
		 * TextArea Constructor
		 * Call the function to generate a textarea, pass through options to customize.
		 *
		 * @param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 * @return string
		 */

		protected function textAreaTrait($settings = [])

		{



			// Draw the start of the textarea
			$tempHTML = $settings['prepend'] . '<textarea';



			// Allow setting the disabled
			if ($settings['disabled'] === true) {

				$tempHTML .= ' disabled="disabled"';

			}



			// Allow setting the autofocus
			if ($settings['autofocus'] === true) {

				$tempHTML .= ' autofocus="autofocus"';

			}



			// Allow setting the readonly
			if ($settings['readonly'] === true) {

				$tempHTML .= ' readonly="readonly"';

			}



			// Allow setting the cols
			if (is_int($settings['cols'])) {

				$tempHTML .= ' cols="' . $this->escapeTrait($settings['cols']) . '"';

			}



			// Allow setting the rows
			if (is_int($settings['rows'])) {

				$tempHTML .= ' rows="' . $this->escapeTrait($settings['rows']) . '"';

			}



			// Allow setting the wrap
			if (is_string($settings['wrap'])) {

				$tempHTML .= ' wrap="' . $this->escapeTrait($settings['wrap']) . '"';

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



            // Allow setting the on click event
            if (is_string($settings['onchange'])) {

                $tempHTML .= ' onchange="' . $this->escapeTrait($settings['onchange']) . '"';

            }



			// Allow setting the form
			if (is_string($settings['form'])) {

				$tempHTML .= ' form="' . $this->escapeTrait($settings['form']) . '"';

			}



			// Allow setting the placeholder
			if (is_string($settings['placeholder'])) {

				$tempHTML .= ' placeholder="' . $this->escapeTrait($settings['placeholder']) . '"';

			}



			// Allow setting the form
			if (is_int($settings['maxlength'])) {

				$tempHTML .= ' maxlength="' . $this->escapeTrait($settings['maxlength']) . '"';

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



			$settings['name'] = $this->escapeTrait($settings['value']);



			// Handle the html
			return $tempHTML . '>' . $settings['value'] . '</textarea>' . $settings['append'];

		}

	}

