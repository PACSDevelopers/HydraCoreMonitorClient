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
        
        // Get number of requests
        $linecountBefore = 0;
        $handle = fopen('/var/log/nginx/access.log', 'r');
        while(!feof($handle)){
            $line = fgets($handle, 4096);
            $linecountBefore += substr_count($line, PHP_EOL);
        }
    
        fclose($handle);
        
    
        // Get network packet count
        $tx1 = file_get_contents('/sys/class/net/eth0/statistics/tx_bytes');
        $rx1 = file_get_contents('/sys/class/net/eth0/statistics/rx_bytes');
    
        // Get process information
        $stat1 = file('/proc/stat');
    
        sleep(1);

        // Get process information
        $stat2 = file('/proc/stat');

        // Get network packet count
        $tx2 = file_get_contents('/sys/class/net/eth0/statistics/tx_bytes');
        $rx2 = file_get_contents('/sys/class/net/eth0/statistics/rx_bytes');

        // Get number of requests
        $linecountAfter = 0;
        $handle = fopen('/var/log/nginx/access.log', 'r');
        while(!feof($handle)){
            $line = fgets($handle, 4096);
            $linecountAfter += substr_count($line, PHP_EOL);
        }
    
        fclose($handle);
    
        $rps = $linecountAfter - $linecountBefore;
    
        $netTX = $tx2 - $tx1;
        $netRX = $rx2 - $rx1;
    
        $net = ($netTX + $netRX);
    
        $info1 = explode(' ', preg_replace('!cpu +!', '', $stat1[0]));
        $info2 = explode(' ', preg_replace('!cpu +!', '', $stat2[0]));
    
        $dif = [];
        $dif['user'] = $info2[0] - $info1[0];
        $dif['nice'] = $info2[1] - $info1[1];
        $dif['sys'] = $info2[2] - $info1[2];
        $dif['idle'] = $info2[3] - $info1[3];
        $total = array_sum($dif);
    
        $cpuA = [];
        foreach($dif as $x => $y) {
            $cpuA[$x] = round($y / $total * 100, 1);
        }

        $cpu = $total - $cpuA['idle'];
        
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

        $mem = ($free / $total) * 100;
    
        $iowait = (float)exec('iostat -c|awk \'/^ /{print $4}\'');

        $df = disk_free_space('/');
        $dt = disk_total_space('/');
        $ds = ($df / $dt) * 100;
    
        if($cpu < 0) {
            $cpu = 0;
        }
    
        $this->body = ['status' => 1, 'message' => 'All', 'result' => ['cpu' => $cpu, 'mem' => $mem, 'iow' => $iowait, 'ds' => $ds, 'net' => $net, 'rps' => $rps]];
        return 1;
    }
}
