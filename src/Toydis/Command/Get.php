<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 6:27 PM
 */

namespace Toydis\Command;


use Toydis\Response;

class Get extends Command
{
    public function rules()
    {
        return [
            ['argc', 1],
        ];
    }

    protected function runInternal($args)
    {
        $k = $args[0];
        $value = $this->db->getValue($k);
        if (!$value) {
            return Response::null();
        }
        return Response::bulkString($value);
    }
}
