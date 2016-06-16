<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 5:52 PM
 */

namespace Toydis;


class RedisServer extends Object
{
    public $confFile;

    public $appendOnly = false;

    public $dbNum = 16;

    public $rdbFile;
    public $aofFile;
    
    public $auth;

    /** @var  RedisDb[] */
    protected $_dbs;

    /** @var  RedisClient[] */
    public $clients;

    public function init()
    {
        // 载入配置
        $this->loadConfig();
        if ($this->appendOnly) {
            $this->restoreFromAof();
        } else if (file_exists($this->rdbFile)) {
            $this->restoreFromRdb();
        }
    }

    protected function loadConfig()
    {

    }

    protected function restoreFromAof()
    {

    }

    protected function restoreFromRdb()
    {

    }

    public function getDb($num)
    {
        if (!is_numeric($num) || $num >= $this->dbNum) {
            throw new \Exception('ERR invalid DB index');
        }
        $num = (int)$num;
        if (!isset($this->_dbs[$num])) {
            $this->_dbs[$num] = new RedisDb(['server' => $this]);
        }
        return $this->_dbs[$num];
    }

}
