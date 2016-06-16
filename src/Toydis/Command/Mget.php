<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/16/16
 * Time: 4:03 PM
 */

namespace Toydis\Command;


use Toydis\Response;

class Mget extends Command
{
    protected function runInternal($args)
    {
        $values = [];
        foreach ($args as $k) {
            $values[] = $this->db->getValue($k);
        }
        return Response::arrays($values);
    }
}
