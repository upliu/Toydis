<?php
/**
 * Created by IntelliJ IDEA.
 * User: liu
 * Date: 6/15/16
 * Time: 5:52 PM
 */

namespace Toydis;


use Toydis\Command\Command;

class Toydis
{
    public $debug;

    public $server;

    /** @var  static */
    public static $instance;

    /** @var  RedisProtocol */
    public $protocol;

    public function __construct()
    {
        static::$instance = $this;
        $this->protocol = new RedisProtocol();
    }

    public static function registerAutoload()
    {
        spl_autoload_register(function($class){
            // project-specific namespace prefix
            $prefix = 'Toydis\\';

            // base directory for the namespace prefix
            $base_dir = __DIR__ . '/';

            // does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }

            // get the relative class name
            $relative_class = substr($class, $len);

            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
            }
        });
    }

    public function run()
    {
        $this->server = $server = new RedisServer();

        // TCP Server
        $serv = new \swoole_server("127.0.0.1", 9501);
        $serv->set(array(
            'worker_num' => 8,   //工作进程数量
            'daemonize' => false, //是否作为守护进程
        ));
        $serv->on('connect', function ($serv, $fd) use ($server){
            $server->clients[(int)$fd] = new RedisClient([
                'socket' => $fd,
                'db' => $server->getDb(0), // 默认 database 0
            ]);
        });
        $serv->on('close', function ($serv, $fd) use ($server) {
            unset($server->clients[(int)$fd]);
        });
        $serv->on('receive', function ($serv, $fd, $from_id, $data) use($server) {
            if ($this->debug) {
//            /*
                echo json_encode([
                        $data,
                        strlen($data),
                        (int)$fd,
                        count($server->clients),
                        isset($server->clients[(int)$fd]),
                        get_class($server->clients[(int)$fd]),
                        get_class_methods($server->clients[(int)$fd]),
                    ]) . "\n\n\n";
                //*/
            }
            try {
                $cmd = $server->clients[(int)$fd]->onReceive($data);
                if (is_object($cmd) && $cmd instanceof Command) {
                    if ($this->debug) {
                        print_r($cmd->args);
                    }
                    $resp = $cmd->run();
                }
            } catch (\Exception $e) {
                $resp = Response::error($e->getMessage());
            }
            isset($resp) && $serv->send($fd, $resp);
        });
        $serv->start();
    }

}
