<?php

namespace Toydis\RedisValue;

/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 6:17 PM
 */

/**
 * string or integer
 * Class Scala
 * @package Toydis\RedisValue
 */
class Scala extends Value
{
    protected $_value;
    
    public function __construct($value)
    {
        $this->_value = $value;
    }
    
    public function __toString()
    {
        return (string)$this->_value;
    }
}
