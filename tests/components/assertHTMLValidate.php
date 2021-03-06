<?hh


    /**
     * CUSTOM ASSERT EXTENSION FOR PHPUNIT FRAMEWORK
     *
     * Copyright © 2012, Vitalii Tereshchuk <xvoland@dotoca.net>
     * All rights reserved.
     *
     * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
     * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
     * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
     * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
     * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
     * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
     * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
     * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
     * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
     * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
     * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
     * POSSIBILITY OF SUCH DAMAGE.
     *
     * @package    Custom Assert
     * @subpackage Custom Assert Extension for PHPUnit Framework
     * @author     Vitalii Tereshchuk <xvoland@dotoca.net>
     * @copyright  2012 Vitalii Tereshchuk <xvoland@dotoca.net>
     * @link       http://dotoca.net/
     * @link       https://github.com/xvoland/html-validate
     *
     */

    class Assert extends PHPUnit_Framework_Assert

    {

        /**
         * Asserts that a HTML is validate.
         *
         * @param  string $html
         * @param  string $output
         *
         * @throws PHPUnit_Framework_AssertionFailedError
         */

        public static function HTMLValidate($html, $output = 'text')

        {



            $_url       = 'http://html5.validator.nu/';

            $_port      = null;

            $_output    = [

                'xhtml',

                'html',

                'xml',

                'json',

                'text'

            ];

            $_useragent = 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)';



            if (empty($html)) {

                throw new PHPUnit_Framework_Exception('HTML was empty');

            }



            if (!is_string($html)) {

                throw new PHPUnit_Framework_Exception('HTML was not empty');

            }



            if (!is_string($output) || !in_array($output, $_output)) {

                throw PHPUnit_Util_InvalidArgumentHelper::factory(2, 'string - text/xhtml/html/xml/json');

            }



            $posts = [

                'out'     => $output,

                'content' => self::_makeHTMLBody($html)

            ];



            $curlOpt = [

                CURLOPT_USERAGENT      => $_useragent,

                CURLOPT_URL            => $_url,

                CURLOPT_PORT           => $_port,

                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_POST           => true,

                CURLOPT_POSTFIELDS     => $posts

            ];



            $curl = curl_init();

            curl_setopt_array($curl, $curlOpt);



            if (!$response = curl_exec($curl)) {

                throw new PHPUnit_Framework_Exception(sprintf('Can\'t check validation. cURL returning error %s', trigger_error(curl_error($curl))));

            }



            curl_close($curl);



            // check response
            if (stripos($response, 'Error') !== false || stripos($response, 'Warning') !== false) {

                //self::assertTrue(false);
                self::fail($response . PHP_EOL . $html);

            }



            return self::assertTrue(true);

        }



        /**
         * this is HTML body?
         *
         * @param  string $html
         *
         * @return string  $html
         */

        protected static function _makeHTMLBody($isHTML)

        {



            // this is HTML or part of HTML?
            if (stripos($isHTML, 'html>') === false) {

                return '<!DOCTYPE html><html><head><meta charset=utf-8 /><title>UnitTest</title></head><body>' . $isHTML . '</body></html>';

            } else {

                return $isHTML;

            }

        }

    }

