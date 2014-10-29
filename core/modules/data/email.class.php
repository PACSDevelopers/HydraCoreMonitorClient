<?hh // decl


	namespace HC;

	/**
	 * Class Email
	 *
	 * @MobliMic: This is way from being finished yet
	 *
	 * @todo    : Complete Email Class
	 * @todo    : Add mail gun support
	 * @todo    : Make all one function and define send type by setting (maybe have a single function and separate functions if wanting to use multiple methods of email?)
	 */

	class Email extends Core

	{

		/**
		 * @var
		 */

		protected $settings = [

				'mailSystem' => 'default', // MailGun - SendGrid - default
				'sendGridUser' => false,

				'sendGridPass' => false,

				'emailType' => 'html',

				'defaults' => [

				'sentFromAddress' => 'admin@' . SITE_DOMAIN,

						'sentFromName' => AUTHOR

				]

		];



		protected $defaultMailOptions = [

			'headers' => [],

			'parameters' => [],

			'toName' => 'User'

		];



		/**
		 *
		 */

		public function __construct($settings = [])

		{

			$globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();

			if (isset($globalSettings['email'])) {

				if (is_array($globalSettings['email'])) {

					$settings = $this->parseOptions($globalSettings['email'], $this->settings);

					$this->settings = $this->parseOptions($settings, $globalSettings['email']);

				}

			}





			return true;

		}



		public function __destruct()

		{

			$this->settings = null;

		}



		public function send($to, $subject, $message, $additional = []) {

            if (filter_var($to, FILTER_VALIDATE_EMAIL) === false) {

                // @todo: Return a custom error if fails with error.class.php
                return false; // Fail email send because not a vail email

            }

			$additional = $this->parseOptions($additional, $this->defaultMailOptions);

            if(!isset($additional['headers'])) {
                $additional['headers'] = [];
            }

            if(isset($additional['toName'])) {
                $additional['headers']['To'] = $additional['toName'] . '<' . $to . '>';
            } else {
                $additional['headers']['To'] = $to;
            }

            $additional['headers']['From'] = $this->settings['defaults']['sentFromAddress'];
            $additional['headers']['Reply-To'] = $this->settings['defaults']['sentFromAddress'];
            $additional['headers']['Subject'] = $subject;
            $additional['headers']['X-Mailer'] = 'HydraCore ' . HC_VERSION;
            
            if ($this->settings['emailType'] == 'html') {
                // Set proper headers
                $additional['headers']['MIME-Version'] = '1.0';
                $additional['headers']['Content-type'] = 'text/html; charset=utf-8';
            }
            
			switch($this->settings['mailSystem']) {

				case 'sendGrid':

					return $this->sendGridMail($to, $subject, $message, $additional);

				break;



				case 'mailGun':

					return $this->mailGunMail($to, $subject, $message, $additional);

				break;



				default:

					return $this->phpMail($to, $subject, $message, $additional);

				break;

			}

		}



		/**
		 * phpMail
		 *
		 * Uses PHP mail() function to send mail
		 *
		 * @description:
		 *             Sending mass mail? I would suggest using a service like SendGrid or MailGun
		 *
		 * @param $to
		 * @param $subject
		 * @param $message
		 * @param $headers
		 */



		private function phpMail($to, $subject, $message, $additional = [])

		{
            $headers = '';
            foreach($additional['headers'] as $key => $value) {
                $headers .= $key . ': ' . $value . '\r\n';
            }

            $message = wordwrap($message, 70, '\r\n');
            $mail = mail($to, $subject, $message, $headers);
            
            if ($mail === true) {

                return true;

            } else {

                // @todo: Return a custom error if fails with error.class.php
                return false;

            }

		}



		/**
		 * SendGrid Mail Function
		 *
		 * @description:
		 *             Uses SendGrid to send emails
		 *
		 * @param $to
		 * @param $subject
		 * @param $message
		 * @param $additional // Include things like files etc
		 *
		 * @return bool
		 */



		private function sendGridMail($to, $subject, $message, $additional = [])

		{

				$mailReq = curl_init();



				curl_setopt_array($mailReq, [

					CURLOPT_RETURNTRANSFER => 1,

					CURLOPT_URL => 'https://api.sendgrid.com/api/mail.send.json',

					CURLOPT_USERAGENT => 'HydraCore',

					CURLOPT_POST => 1,

					CURLOPT_POSTFIELDS => [

						'api_user' => $this->settings['sendGridUser'],

						'api_key' => $this->settings['sendGridPass'],

						'subject' => $subject,

						'html' => $message,

						'to' => $to,

						'toname' => $additional['toName'],

						'from' => $this->settings['defaults']['sentFromAddress'],

						'fromname' => $this->settings['defaults']['sentFromName']

					]

				]);



				$mailResp = json_decode(curl_exec($mailReq));



				curl_close($mailReq);

				

				if($mailResp) {

					if(isset($mailResp->message)) {

						if($mailResp->message == 'success') {

							return true;

						}

					}

				}



				return false;

		}



		/**
		 * @return bool
		 */

		private function mailGunMail()

		{



			// @todo: learn MailGuns API!
			return true;

		}



	}

