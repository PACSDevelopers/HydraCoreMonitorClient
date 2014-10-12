<?hh // decl


	namespace HC;

	/**
	 * Class Text
	 */

	class Text extends Core

	{

		/**
		 * @var
		 */

		protected $settings = [

				'system' => null, // clockwork || twilio
				'accountKey' => null,
				'apiKey' => null,
				'from' => null

		];



		protected $textObject = null;


		/**
		 *
		 */

		public function __construct($settings = [])

		{

			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['text'])) {

				if (is_array($globalSettings['text'])) {

					$this->settings = $this->parseOptions($settings, $globalSettings['text']);

				}

			} else {
				return false;
			}



			$this->settings = $this->parseOptions($settings, $this->settings);



			if(is_null($this->settings['system'])) {

				throw new \Exception('No text system is defined.');

			}



			if(is_null($this->settings['apiKey'])) {

				throw new \Exception('No text api key is defined.');

			}



			if(is_null($this->settings['from'])) {

				throw new \Exception('No text from is defined.');

			}



			switch($this->settings['system']) {

				case 'clockwork':

					require_once HC_LOCATION . '/vendor/clockwork/class-Clockwork.php';

					$this->textObject = new \Clockwork($this->settings['apiKey'], ['from' => $this->settings['from'], 'ssl' => true]);

				break;

				case 'twilio':

				break;

				default:

						throw new \Exception('No text system is defined.');

				break;

			}



			return true;

		}



		public function __destruct()

		{

			$this->settings = null;

		}



		public function send($to, $message) {



			if(mb_strlen($message) > 160) {

				throw new \Exception('Text message content too large, max 160 characters.');

			}



			switch($this->settings['system']) {

				case 'clockwork':

					return $this->sendClockWorkText($to, $message);

				break;

				case 'twilio':
					return $this->sendTwillioText($to, $message);
				break;

				default:

					return false;

				break;

			}

		}



		private function sendClockWorkText($to, $message)

		{

				try {

					$response = $this->textObject->send(['to' => $to, 'message' => $message]);

					if($response['success'] === 1) {

						return true;

					}

				} catch (\ClockworkException $e) {



				}



				return false;

		}

		private function sendTwillioText($to, $message)

		{

				$curl = curl_init();

				curl_setopt_array($curl, [
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/' . $this->settings['accountKey'] . '/Messages.json',
						CURLOPT_USERAGENT => 'HydraCore ' . HC_VERSION,
						CURLOPT_USERPWD => $this->settings['accountKey'] . ':' . $this->settings['apiKey'],
						CURLOPT_POST => true,
						CURLOPT_POSTFIELDS => [
							'To' => '+' . $to,
							'From' => '+' . $this->settings['from'],
							'Body' => $message
						]
				]);

				$resp = curl_exec($curl);
				curl_close($curl);

				if($resp) {
					$resp = (array)json_decode($resp);
					if(isset($resp['sid']) && $resp['sid']) {
						return true;
					}

				}

				return false;
		}

	}
