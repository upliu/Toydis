<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/16/16
 * Time: 4:35 PM
 */

namespace Toydis\Command;


use Toydis\Response;

class Dbsize extends Command
{
    public function rules()
    {
        return [
            ['argc', 0]
        ];
    }

    protected function runInternal($args)
    {
        return Response::integer($this->getDb()->dbSize());
    }
}
