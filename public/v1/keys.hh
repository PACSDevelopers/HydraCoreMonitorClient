<?hh
namespace HCPublic\V1;

class KeysPage extends \HC\Ajax {

    protected $db;

    static protected $lifeTime = 2592000;

    public function __construct()
    {
        parent::__construct();
        $this->db = new \HC\DB();
    }

	public function init($GET = [], $POST = []) :int {
        $this->body = ['status' => 1, 'message' => 'Keys'];
        return 1;
	}
}
