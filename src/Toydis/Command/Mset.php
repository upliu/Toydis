<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/16/16
 * Time: 4:12 PM
 */

namespace Toydis\Command;


class Mset extends Command
{
    protected function runInternal($args)
    {
        if (count($args) % 2 != 0) {
            $this->throwExceptionWrongNumberOfArguments();
        }
        $pairs = array_chunk($args, 2);
        foreach ($pairs as $pair) {
            $this->getDb()->setValue($pair[0], $pair[1]);
        }
    }
}
