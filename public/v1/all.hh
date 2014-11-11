<?hh
namespace HCPublic\V1;

class AllPage extends \HC\Ajax {

    public function __construct()
    {
        parent::__construct();
    }

    public function init($GET = [], $POST = []) :int {
        $this->body = ['status' => 1, 'message' => 'All'];
        return 1;
	}

    public function code_get($GET = [], $POST = []) :int {
        $auth = new \HC\Authenticator();
        $auth->setCodeLength(9);
        $secret = $auth->createSecret(128);
        $code = $auth->getCode($secret);
    
        $this->body = ['status' => 1, 'message' => 'All', 'secret' => $secret, 'code' => $code];
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
        
        $net = \HCMC\Stats::getNetworkTraffic();
        $mem = \HCMC\Stats::getMemoryUsage();
        $ds = \HCMC\Stats::getDiskSpace('/');
        $updates = \HCMC\Stats::getUpdates();
        $securityUpdates = \HCMC\Stats::getSecurityUpdates();
        $rebootRequired = \HCMC\Stats::rebootRequired();
        
        $output = [];
        $status = exec('iostat', $output);
    
        foreach($output as $key => $value) {
            $output[$key] = preg_replace('/\s+/', ' ', $value);
        }
        
        $iowait = 0;
        if(isset($output[3])) {
            $line = explode(' ', $output[3]);
            if(isset($line[4])) {
                $iowait = (float)$line[4];
            }
        }
    
        $tps = 0;
        if(isset($output[6])) {
            $line = explode(' ', $output[6]);
            if(isset($line[1])) {
                $tps = (float)$line[1];
            }
        }
    
        $cpu = 0;
        if(isset($output[3])) {
            $line = explode(' ', $output[3]);
            if(isset($line[6])) {
                $cpu = 100 - (float)$line[6];
                if($cpu < 0) {
                    $cpu = 0;
                }
            }
        }

        $avgRespTime = 0;
        $avgTimeCpuBound = 0;
        $qpm = 0;
        $rpm = 0;
    
        $timecode = apc_fetch('HC_APP_STATS_TIMECODE_LAST');
        $expire = time() - 60;
        if($timecode && ($timecode > $expire)) {
            $avgRespTime = apc_fetch('HC_APP_STATS_TIME_LAST');
            if(!$avgRespTime) {
                $avgRespTime = 0;
            }

            $avgTimeCpuBound = apc_fetch('HC_APP_STATS_TIME_CPUBOUND_LAST');
            if(!$avgTimeCpuBound) {
                $avgTimeCpuBound = 0;
            }

            $qpm = apc_fetch('HC_APP_STATS_QPM_LAST');
            if(!$qpm) {
                $qpm = 0;
            }

            $rpm = apc_fetch('HC_APP_STATS_REQUESTS_LAST');
            if(!$rpm) {
                $rpm = 0;
            }
        }
    
        $this->body = ['status' => 1, 'message' => 'All', 'result' => ['rebootRequired' => $rebootRequired, 'updates' => $updates, 'securityUpdates' => $securityUpdates, 'cpu' => $cpu, 'mem' => $mem, 'iow' => $iowait, 'ds' => $ds, 'net' => $net, 'rpm' => $rpm, 'tps' => $tps, 'avgRespTime' => $avgRespTime, 'qpm' => $qpm, 'avgTimeCpuBound' => $avgTimeCpuBound]];
        return 1;
    }
}
