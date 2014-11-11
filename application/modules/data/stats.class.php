<?hh
namespace HCMC;

class Stats extends \HC\Core
{
    public static function getDiskSpace($dir = '/') {
        $df = disk_free_space($dir);
        $dt = disk_total_space($dir);        
        return 100 - ($df / $dt) * 100;
    }
    
    public static function getMemoryUsage() {
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

        return 100 - ($free / $total) * 100;
    }
    
    public static function getNetworkTraffic() {
        // Get network packet count
        $tx1 = file_get_contents('/sys/class/net/eth0/statistics/tx_bytes');
        $rx1 = file_get_contents('/sys/class/net/eth0/statistics/rx_bytes');

        sleep(1);

        // Get network packet count
        $tx2 = file_get_contents('/sys/class/net/eth0/statistics/tx_bytes');
        $rx2 = file_get_contents('/sys/class/net/eth0/statistics/rx_bytes');

        $netTX = $tx2 - $tx1;
        $netRX = $rx2 - $rx1;
        
        return ($netTX + $netRX);
    }
    
    public static function updateApt() {
        $command = '/usr/lib/update-notifier/apt-check --human-readable';
        $output = [];
        $line = exec($command, $output, $returnCode);
        if($returnCode === 0 && isset($output[0])) {
            if(preg_match('/^\d/', $output[0], $matches)) {
                if(is_numeric($matches)) {
                    return $matches;
                }
            }
        }
    }
    
    public static function getUpdates() {
        if(is_file('/usr/lib/update-notifier/apt-check')) {
            $command = '/usr/lib/update-notifier/apt-check --human-readable';
            $output = [];
            $line = exec($command, $output, $returnCode);
            if($returnCode === 0 && isset($output[0])) {
                if(preg_match('/^\d/', $output[0], $matches)) {
                    if(is_numeric($matches)) {
                        return $matches;
                    }
                }
            }
        }
        return 0;
    }
    
    public static function getSecurityUpdates() {
        if(is_file('/usr/lib/update-notifier/apt-check')) {
            $command = '/usr/lib/update-notifier/apt-check --human-readable';
            $output = [];
            $line = exec($command, $output, $returnCode);
            if($returnCode === 0 && isset($output[1])) {
                if(preg_match('/^\d/', $output[1], $matches)) {
                    if(is_numeric($matches)) {
                        return $matches;
                    }
                }
            }
        }
        return 0;
    }
    
    public static function rebootRequired() {
        return is_file('/var/run/reboot-required');
    }
}
