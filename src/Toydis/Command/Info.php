<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/16/16
 * Time: 3:13 PM
 */

namespace Toydis\Command;


use Toydis\Response;

class Info extends Command
{
    protected function runInternal($args)
    {
        $info = [];
        $usedMemory = memory_get_usage();
        $peakMemory = memory_get_peak_usage();
        $info['Memory'] = [
            'used_memory' => $usedMemory,
            'used_memory_human' => $this->human($usedMemory),
            'used_memory_peak' => $peakMemory,
            'used_memory_peak_human' => $this->human($peakMemory),
        ];
        $lines = [];
        foreach ($info as $title => $detail) {
            $lines[] = "# $title";
            foreach ($detail as $k => $v) {
                $lines[] = "{$k}:{$v}";
            }
        }
        $str = implode("\r\n", $lines)."\r\n";
        return Response::bulkString($str);
    }

    public function human($bytes)
    {
        if ($bytes == 0)
            return "0.00 B";

        $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $e = (int)floor(log($bytes, 1024));

        return round($bytes/pow(1024, $e), 2).$s[$e];
    }
}
