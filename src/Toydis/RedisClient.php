<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 6:03 PM
 */

namespace Toydis;


class RedisClient extends Object
{

    public $socket;

    /** @var  RedisDb */
    public $db;

    protected $_needAuth;

    public function init()
    {
        $server = $this->db->server;
        if ($server->auth) {
            $this->_needAuth = true;
        } else {
            $this->_needAuth = false;
        }
    }

    protected $_authed;
    public function auth($pass)
    {
        if (!$this->_needAuth) {
            // @todo throw new \Exception('no need auth'); and return
        }
        // @todo time tick attack
        if ($this->db->server->auth === $pass) {
            $this->_authed = true;
            return true;
        }

        return false;
    }

    public function getIsAuthed()
    {
        return !$this->_needAuth || $this->_authed;
    }

    public $buff = '';
    public function onReceive($data)
    {
        $this->buff .= $data;
        try {
            $args = Toydis::$instance->protocol->parse($this);
            if (is_array($args)) {
                $cmd = array_shift($args);
                $class = '\\Toydis\\Command\\'.ucfirst(strtolower($cmd));
                if (!class_exists($class)) {
                    throw new \Exception("ERR unknown command '$cmd'");
                }
                $this->buff = '';
                return new $class([
                    'client' => $this,
                    'args' => $args,
                ]);
            }
        } catch (\Exception $e) {
            if ($e->getMessage() == 'RECEIVING') {
                // 继续等待数据
            } else {
                throw $e;
            }
        }
    }

}
