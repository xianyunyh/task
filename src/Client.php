<?php
namespace Task;

use Swoole\Process;
use Swoole\Server;
use Swoole\Table;
use Swoole\Timer;
use Task\Process\ProcessManger;
use Task\Server\Tcp;
use Task\Job;
use Task\Subscribe\Redis as TaskRedis;

class Client
{


    /**
     * @var Swoole\Table
     */
    protected static $table;
    /**
     * @var ProcessManger
     */
    protected static $processManger;

    protected static $config = [];
    /**
     * @var Job
     */
    protected static $job;

    public static function start($config = [])
    {
        self::$config = $config;
        $columns = [
            ["task_id",Table::TYPE_INT,4],
            ["start_time",Table::TYPE_INT,8],
            ["cycle",Table::TYPE_INT,4],
            ["command",Table::TYPE_STRING,1024],
        ];
        set_exception_handler(function($exception){
            echo "Exception Caught: ". $exception->getMessage() .PHP_EOL;
        });

        //设置主进程名字
        swoole_set_process_name("php:job-master");
        //回收子进程
        ProcessManger::wait();
        //内存表初始化
        self::$job = Job::init(1024,$columns);
        //监听数据
        $subscribeClass = $config['subscribe'];
        if(!class_exists($subscribeClass)) {
            throw new \Exception("$subscribeClass not found");
        }
        (new $subscribeClass(self::$job))->run($config['redis'] ?? []);
        //定时器
        self::timeTick($config['time_tick'] ?? 5000);
    }


    protected static function timeTick($time = 1000)
    {
        Timer::tick($time, function () {
            $size = Job::count();
            echo "表的大小为$size".PHP_EOL;
            $count = ProcessManger::getProcessNum();
            for ($i = 0; $i < $size; $i++) {
                ProcessManger::createProcess(function (Process $worker) {
                    echo "hello\n";
                    $worker->exit(0);
                });
            }
        });
    }


    /**
     * 创建tcpserver
     */
    protected static function createServer($config)
    {
        ProcessManger::createProcess(function() use($config){
            $server = Tcp::init($config);
            $server->on('receive',"Client::_Receive");
            $server->on('WorkerStart',"Client::_workStart");
            $server->start();
            return $server;
        });
    }


    /**
     * 回调设置进程名字
     * @param Server $server
     * @param int $worker_id
     */
    public static function _workStart(Server $server, int $worker_id)
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
    public static function _Receive(Server $server, int $fd, int $reactor_id, string $data)
    {
        (self::$job)::add("user-$fd", ['data' => $data]);
        $server->send($fd, $data);
    }

}