<?hh // decl


	namespace HC;

    /**
     * Class Ajax
     */

    class Ajax extends Page

    {
        protected $isAJAX = true;
        public $body = [];

        public function render() {
            if(($this->rendered) || (isset($GLOBALS['skipRender']) && $GLOBALS['skipRender'])) {
                return false;
            }

            if(is_array($this->body)) {
                $this->body = json_encode($this->parseTypes($this->body));
            } else {
                $this->body = utf8_encode($this->body);
            }

            return parent::render();
        }

        protected function parseTypes($array) {
            foreach($array as $key => $value) {
                if(is_array($value)) {
                    $array[$key] = $this->parseTypes($value);
                    continue;
                } else {
                    if(is_numeric($value)) {
                        continue;
                    } else if(is_string($value)) {
                        $array[$key] = utf8_encode($value);
                    }

                }
            }

            return $array;
        }

        public function sendHeader() :void {
            if(!headers_sent()) {
                header('Content-type: application/json');
            }
			
		}
    }
