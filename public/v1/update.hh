<?hh
namespace HCPublic\V1;

class UpdatePage extends \HC\Ajax {

    public function __construct()
    {
        parent::__construct();
    }
    
    public function request_get($GET = [], $POST = []) {
        if(!isset($GET['code'])) {
            return 400;
        }
        
        $auth = new \HCMC\Authenticator();
        if(!$auth->checkAccess($GET['code'])) {
            return 401;
        }
        
        if(!is_dir(HC_TMP_LOCATION)) {
            mkdir(HC_TMP_LOCATION, 0777);
        }

        if(!is_dir(HC_TMP_LOCATION . '/actions')) {
            mkdir(HC_TMP_LOCATION . '/actions', 0777);
        }
        
        $this->body = ['status' => touch(HC_TMP_LOCATION . '/actions/updateRequested'), 'message' => 'Update'];
        return 1;
    }
}
