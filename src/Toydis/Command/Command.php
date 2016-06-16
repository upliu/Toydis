<?php
namespace Toydis\Command;
use Toydis\Object;
use Toydis\RedisClient;
use Toydis\RedisDb;
use Toydis\RedisServer;
use Toydis\Response;

/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 6:26 PM
 */

/**
 * Class Command
 * @package Toydis\Command
 * @property RedisDb $db
 */
abstract class Command extends Object
{
    public $expectArgc;
    
    /** @var  RedisClient */
    public $client;

    /** @var  String[] */
    public $args;

    public function rules()
    {
        return [];
        return [
            ['argc', 2],
            ['argcRange', 1, 3],
        ];
    }

    public function run($args = null)
    {
        if (null === $args) {
            $args = $this->args;
        }
        $this->checkArgs($args);
        $resp = $this->runInternal($args);
        if (!is_object($resp) || !$resp instanceof Response) {
            $resp = Response::ok();
        }

        return $resp;
    }

    public function checkArgs($args)
    {
        $rules = $this->rules();
        if (is_integer($this->expectArgc)) {
            array_unshift($rules, ['argc', $this->expectArgc]);
        }
        foreach ($rules as $rule) {
            switch ($rule[0]) {
                case 'argc':
                    if (count($args) != $rule[1]) {
                        $this->throwExceptionWrongNumberOfArguments();
                    }
                    break;
                case 'argcRange':
                    if (count($args) < $rule[1] || count($args) > $rule[2]) {
                        $this->throwExceptionWrongNumberOfArguments();
                    }
                    break;
                case 'callback':
                    call_user_func($rule[1], $args);
                    break;
            }
        }
    }

    abstract protected function runInternal($args);

    protected $_name;
    public function getName()
    {
        if ($this->_name === null) {
            $command = substr(get_class($this), strlen(__NAMESPACE__ . '\\'));
            $this->_name = strtolower(str_replace('\\', ' ', $command));
        }

        return $this->_name;
    }

    public function __invoke($args = null)
    {
        $this->run($args);
    }
    
    protected function throwExceptionWrongNumberOfArguments()
    {
        throw new \Exception("ERR Wrong number of arguments for command '" . $this->getName() . "'");
    }
    
    public function getDb()
    {
        return $this->client->db;
    }
    
    public function setDb($db)
    {
        $this->client->db = $db;
    }
    
    public function getServer()
    {
        return $this->getDb()->server;
    }
}
