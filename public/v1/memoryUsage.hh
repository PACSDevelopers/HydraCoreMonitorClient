<?hh
namespace HCPublic\V1;

class MemoryUsagePage extends \HC\Ajax {

    public function __construct()
    {
        parent::__construct();
    }

    public function init($GET = [], $POST = []) :int {
        $this->body = ['status' => 1, 'message' => 'MemoryUsage'];
        return 1;
	}

    public function code_get($GET = [], $POST = []) :int {
        $auth = new \HC\Authenticator();
        $auth->setCodeLength(9);
        $secret = $auth->createSecret(128);
        $code = $auth->getCode($secret);
    
        $this->body = ['status' => 1, 'message' => 'MemoryUsage', 'secret' => $secret, 'code' => $code];
        return 1;
    }

    public function get_get($GET = [], $POST = []) {
        if(!isset($GET['code'])) {
            return 400;
        }
        
        $auth = new \HCMC\Authenticator();
        if(!$auth->checkAccess($GET['code'])) {
            return 401;
        }

        $mem = \HCMC\Stats::getMemoryUsage();
    
        $this->body = ['status' => 1, 'message' => 'MemoryUsage', 'result' => ['mem' => $mem]];
        return 1;
    }
}
