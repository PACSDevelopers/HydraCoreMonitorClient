<?hh // decl


class POTENTIAL_XSS_HOLE {
  private $htmlString;

  public function __construct($htmlString) {
    $this->htmlString = $htmlString;
  }

  public function getRawHTML() {
    return $this->htmlString;
  }

  public function render() {
    return $this->htmlString;
  }
}

function POTENTIAL_XSS_HOLE($htmlString) {
  return new POTENTIAL_XSS_HOLE($htmlString);
}


require_once 'core.hh';
require_once 'html.hh';
