<?hh
namespace HCPublic\V1;

class AllPage extends \HC\Ajax {

    protected $db;

    static protected $lifeTime = 2592000;

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
    
        // Get network packet count
        $tx1 = file_get_contents('/sys/class/net/eth0/statistics/tx_bytes');
        $rx1 = file_get_contents('/sys/class/net/eth0/statistics/rx_bytes');
        
        sleep(1);
        
        // Get network packet count
        $tx2 = file_get_contents('/sys/class/net/eth0/statistics/tx_bytes');
        $rx2 = file_get_contents('/sys/class/net/eth0/statistics/rx_bytes');
    
        $netTX = $tx2 - $tx1;
        $netRX = $rx2 - $rx1;
    
        $net = ($netTX + $netRX);
    
        $total = 0;
        $free = 0;
        $fh = fopen('/proc/meminfo', 'r');
        while ($line = fgets($fh)) {
            $pieces = [];
            if (preg_match('/^MemTotal:\\s+(\\d+)\\skB$/m', $line, $pieces)) {
                $total = $pieces[1];
            } else if (preg_match('/^MemFree:\\s+(\\d+)\\skB$/m', $line, $pieces)) {
                $free = $pieces[1];
                break;
            }
        }
        fclose($fh);

        $mem = 100 - ($free / $total) * 100;
        
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

        $df = disk_free_space('/');
        $dt = disk_total_space('/');
        $ds = 100 - ($df / $dt) * 100;

        $avgRespTime = apc_fetch('HC_APP_STATS_TIME');
        if(!$avgRespTime) {
            $avgRespTime = 0;
        }
        
        $avgTimeCpuBound = apc_fetch('HC_APP_STATS_TIME_CPUBOUND');
        if(!$avgTimeCpuBound) {
            $avgTimeCpuBound = 0;
        }

        $qpm = apc_fetch('HC_APP_STATS_QPM');
        if(!$qpm) {
            $qpm = 0;
        }
    
        $rpm = apc_fetch('HC_APP_STATS_REQUESTS');
        if(!$rpm) {
            $rpm = 0;
        }
    
        $this->body = ['status' => 1, 'message' => 'All', 'result' => ['cpu' => $cpu, 'mem' => $mem, 'iow' => $iowait, 'ds' => $ds, 'net' => $net, 'rpm' => $rpm, 'tps' => $tps, 'avgRespTime' => $avgRespTime, 'qpm' => $qpm, 'avgTimeCpuBound' => $avgTimeCpuBound]];
        return 1;
    }
}
