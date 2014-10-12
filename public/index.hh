<?hh
namespace HCPublic;

class IndexPage extends \HC\Ajax {
	public function init($GET = [], $POST = []) :int {
        $this->body = ['status' => 1, 'message' => 'Index'];
        return 1;
	}
}
