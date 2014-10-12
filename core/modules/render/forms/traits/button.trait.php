<?hh


	namespace HC;



	require_once 'escape.trait.php';



	/**
	 * Class ButtonTrait
	 */

	trait ButtonTrait

	{

		use EscapeTrait;



		/**
		 * Button Constructor
		 * Call the function to generate a button, pass through options to customize.
		 *
		 * @param (int|bool|array|string|null)[] $settings  [] (The settings below:)
		 *
		 * @param type => ('button', 'submit', 'reset')    ['button']      (Specifies the type of the button)
		 * @param name => (true, false)                     [false]         (Specifies a name and id for the button)
		 * @param value => (true, false)                     [ucfirst(type)] (Specifies an initial value for the button)
		 * @param class => (true, false)                     [false]         (Specifies the class of the button)
		 * @param onclick => (true, false)                     [false]         (Specifies the onClick of the button)
		 * @param autofocus => (true, false)                     [false]         (Specifies that a button should automatically get focus when the page loads)
		 * @param disabled => (true, false)                     [false]         (Specifies that a button should be disabled)
		 *
		 * @return string
		 */

		protected function buttonTrait($settings = [])

		{



			// Draw the start of the button
			$tempHTML = $settings['prepend'] . '<button type="' . $this->escapeTrait($settings['type']) . '"';

			// Allow disabling the button
			if ($settings['disabled'] === true) {

				$tempHTML .= ' disabled="disabled"';

			}

			// Allow setting the autofocus
			if ($settings['autofocus'] === true) {

				$tempHTML .= ' autofocus="autofocus"';

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



			// Allow setting the checked
			if ($settings['checked'] === true) {

				$tempHTML .= ' checked="checked"';

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



			// Force setting the value
			if ($settings['value'] === false) {

				$settings['value'] = ucfirst($settings['type']);

			}



			$settings['name'] = $this->escapeTrait($settings['value']);



			// Handle the html
			return $tempHTML . '>' . $settings['value'] . '</button>' . $settings['append'];

		}

	}

