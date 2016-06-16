<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 5:59 PM
 */

namespace Toydis;


use Toydis\RedisValue\Scala;
use Toydis\RedisValue\Value;

class RedisDb extends Object
{

    /** @var  [] */
    protected $_values = [];

    /** @var  [] 过期时间 */
    protected $_valueExpires = [];

    /** @var  RedisServer */
    public $server;

    /**
     * @param $key
     * @param $value Value
     * @param int $expire 过期的时间（微妙时间戳）
     */
    public function setValue($key, $value, $expire = 0)
    {
        if (is_scalar($value)) {
            $value = new Scala($value);
        }
        $this->_values[$key] = $value;
        if ($expire > 0) {
            $this->_valueExpires[$key] = $expire;
        }
    }

    public function removeValue($key)
    {
        unset($this->_values[$key]);
        unset($this->_valueExpires[$key]);
    }

    /**
     * @param $key
     * @return null|Value
     */
    public function getValue($key)
    {
        if ($this->exists($key)) {
            return $this->_values[$key];
        }
    }

    public function clearExpire($key)
    {
        if (isset($this->_valueExpires[$key]) && $this->_valueExpires[$key] < microtime(true) * 1000) {
            $this->removeValue($key);
            return true;
        }
    }

    public function exists($key)
    {
        if ($this->clearExpire($key)) {
            return false;
        }
        return isset($this->_values[$key]);
    }

    public function dbSize()
    {
        foreach ($this->_valueExpires as $key => $t) {
            if ($t < microtime(true) * 1000) {
                $this->removeValue($key);
            }
        }
        return count($this->_values);
    }

    public function flush()
    {
        $this->_values = [];
        $this->_valueExpires = [];
    }

    public function keys($pattern)
    {
        // @todo 模式匹配
        return array_keys($this->_values);
    }
}
