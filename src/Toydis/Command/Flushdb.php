<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/16/16
 * Time: 6:01 PM
 */

namespace Toydis\Command;


class Flushdb extends Command
{
    public $expectArgc = 0;
    
    protected function runInternal($args)
    {
        $this->getDb()->flush();
    }
}
