<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 6:27 PM
 */

namespace Toydis\Command;


use Toydis\RedisValue\Scala;
use Toydis\Response;

class Set extends Command
{
    public $k;
    public $v;
    public $xx;
    public $nx;
    public $expire = 0;
    public function rules()
    {
        return [
            ['argcRange', 2, PHP_INT_MAX]
        ];
    }

    public function parseArgs($args)
    {
        $this->k = $args[0];
        $this->v = $args[1];

        $idx = count($args);
        $i = 2;
        while ($i < $idx) {
            $argv = $args[$i];
            if (($px = strcasecmp($argv, 'px') === 0) || ($ex = strcasecmp($argv, 'ex') === 0)) {
                // px, ex 后面必须跟整数
                if (!isset($args[$i+1])) {
                    throw new \Exception('ERR syntax error');
                }
                $int = $args[$i+1];
                if (!is_numeric($int) || strlen($int) != strlen((int)$int)) {
                    throw new \Exception('ERR value is not an integer or out of range');
                }
                if (!empty($ex)) {
                    $int = $int * 1000;
                }
                if ($int > PHP_INT_MAX) {
                    throw new \Exception('ERR value is not an integer or out of range');
                }
                $this->expire = $int + microtime(true) * 1000;
                $i = $i+2;
                continue;
            }

            if (strcasecmp($argv, 'xx') === 0) {
                $this->xx = true;
            } else if (strcasecmp($argv, 'nx') === 0) {
                $this->nx = true;
            } else {
                throw new \Exception('ERR syntax error');
            }
            $i++;
        }
    }

    protected function runInternal($args)
    {
        $this->parseArgs($args);
        if ($this->xx && $this->nx) {
            return Response::null();
        }
        $exists = $this->db->exists($this->k);
        if (($this->xx && !$exists) || ($this->nx && $exists)) {
            return Response::null();
        }
        $this->db->setValue($this->k, $this->v, $this->expire);
    }
}
