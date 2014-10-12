<?hh


	namespace HC;

	/**
	 * Trait EscapeTrait
	 */

	trait EscapeTrait

	{

		/**
		 * @param string|integer $string
		 * @return string|false
		 */

		protected function escapeTrait($string)

		{

			if (!is_string($string)) {

				if (!is_integer($string)) {

					return false;

				}

				$string = strval($string);

			}



			if (mb_strpos($string, '"') !== false) {

				$string = str_replace('"', '&quot;', $string);

			}

			if (mb_strpos($string, '\'') !== false) {

				$string = str_replace('\'', '\\\'', $string);

			}



			return $string;

		}

	}

