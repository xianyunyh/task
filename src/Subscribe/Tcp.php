<?php

namespace Task\Subscribe;
use Swoole\Server;
use Task\Job;
use Task\Singleton;
use Task\Process\ProcessManger;

class Tcp
{
    use singleton;
    protected static $host = "0.0.0.0";
    protected static $port = 9090;
    /**
     * @var Server
     */
    public static $server;

    /**
     * @var Job
     */
    private static $job;

    private function __construct($job)
    {
        self::$job = $job;
    }


    public function run($config = [])
    {
        ProcessManger::createProcess(function ($worker) use($config){
            $host = $config['host'] ?? self::$host;
            $port = $config['port'] ?? self::$port;
            $mode = $config['mode'] ?? SWOOLE_BASE;
            $setting = $config['setting'] ?? [];

            $server = new Server($host, $port,$mode);
            if(empty($setting)) {
                $server->set($setting);
            }
            $server->on('WorkerStart',[$this,'_workStart']);
            $server->on('receive',[$this,'_Receive']);
            $server->start();
        });
    }
    /**
     * 回调设置进程名字
     * @param Server $server
     * @param int $worker_id
     */
    public function _workStart(Server $server, int $worker_id)
    {
        swoole_set_process_name("php:TCP-Server");
    }

    /**
     * server 回调
     * @param Server $server
     * @param int $fd
     * @param int $reactor_id
     * @param string $data
     */
    public function _Receive(Server $server, int $fd, int $reactor_id, string $data)
    {
        $message = json_decode($data,true);
        if(isset($message['id'])) {
            (self::$job)::add($message['id'], $message);
        }
        //$server->send($fd, $data);
    }

}