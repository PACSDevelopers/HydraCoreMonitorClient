<?hh
namespace HCMC;

class Authenticator extends \HC\Core
{
    protected $settings = [];
    protected $authenticator;

    public function  __construct($domain = [])
    {
        // Parse global / local options
        $globalSettings = $GLOBALS['HC_CORE']->getSite()->getSettings();
        if(isset($globalSettings['monitor-client'])) {
            $this->settings = $globalSettings['monitor-client'];
            $this->authenticator = new \HC\Authenticator();
            $this->authenticator->setCodeLength(9);
        }
    }

    public function checkAccess($code) {
        if(isset($this->settings['key'])) {
            return $this->authenticator->verifyCode($this->settings['key'], $code);
        }
        
        return false;
    }
}
