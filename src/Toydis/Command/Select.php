<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 6:27 PM
 */

namespace Toydis\Command;


class Select extends Command
{
    public function rules()
    {
        return [
            ['argc', 1],
        ];
    }

    protected function runInternal($args = null)
    {
        $this->db = $this->server->getDb($args[0]);
    }
}
