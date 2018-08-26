<?php

namespace Task\Server;

use Swoole\Server;

class Tcp
{
    protected static $host = "0.0.0.0";
    protected static $port = 9090;
    /**
     * @var Server
     */
    public static $server;

    public static function init($config = [])
    {
        self::$host = $config['host'];
        self::$port = $config['port'];
        $mode = $config['mode'] ?? SWOOLE_BASE;
        $setting = $config['setting'] ?? [];

        self::$server = new Server(self::$host, self::$port,$mode);
        if(empty($setting)) {
            self::$server->set($setting);
        }
        return self::$server;
    }

}