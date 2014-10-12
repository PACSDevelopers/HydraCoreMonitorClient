<?hh
namespace HCPublic;

class V1Page extends \HC\Ajax {
	public function init($GET = [], $POST = []) :int {
        $this->body = ['status' => 1, 'message' => 'V1'];
        return 1;
	}
}
