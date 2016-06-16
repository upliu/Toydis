<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 6:32 PM
 */

namespace Toydis;


use Toydis\RedisValue\Value;

class Response
{
    public $content;

    public static function simpleString($str)
    {
        return new static('+'.$str."\r\n");
    }

    public static function bulkString($str)
    {
        return new static('$'.strlen($str)."\r\n".$str."\r\n");
    }

    public static function arrays($arrays)
    {
        $count = count($arrays);
        $buff = '*'.$count."\r\n";
        foreach ($arrays as $v) {
            if (is_array($v) && $v[1] == 1) {
                $buff .= static::integer($v[0]);
            } else if (is_string($v) || (is_object($v) && $v instanceof Value)) {
                $buff .= static::bulkString($v);
            } else if (is_null($v)) {
                $buff .= static::null();
            }
        }

        return new static($buff);
    }

    public static function null()
    {
        return new static("$-1\r\n");
    }

    public static function ok()
    {
        return static::simpleString('OK');
    }

    public static function error($msg)
    {
        return new static('-'.$msg."\r\n");
    }

    public static function integer($int)
    {
        return new static(':'.$int."\r\n");
    }

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function __toString()
    {
        return $this->content;
    }
}
