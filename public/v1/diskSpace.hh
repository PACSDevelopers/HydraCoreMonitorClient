<?hh
namespace HCPublic\V1;

class DiskSpacePage extends \HC\Ajax {

    public function __construct()
    {
        parent::__construct();
    }

    public function init($GET = [], $POST = []) :int {
        $this->body = ['status' => 1, 'message' => 'DiskSpace'];
        return 1;
	}

    public function code_get($GET = [], $POST = []) :int {
        $auth = new \HC\Authenticator();
        $auth->setCodeLength(9);
        $secret = $auth->createSecret(128);
        $code = $auth->getCode($secret);
    
        $this->body = ['status' => 1, 'message' => 'DiskSpace', 'secret' => $secret, 'code' => $code];
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
        
        $ds = \HCMC\Stats::getDiskSpace();
    
        $this->body = ['status' => 1, 'message' => 'DiskSpace', 'result' => ['ds' => $ds]];
        return 1;
    }
}