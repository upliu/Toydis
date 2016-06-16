<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/16/16
 * Time: 6:06 PM
 */

namespace Toydis\Command;


use Toydis\Response;

class Keys extends Command
{
    public $expectArgc = 1;
    protected function runInternal($args)
    {
        return Response::arrays($this->getDb()->keys($args[0]));
    }
}
