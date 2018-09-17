<?php
namespace Task;

use Swoole\Exception;
use Swoole\Process;
use Swoole\Table;
use Swoole\Timer;
use Task\Process\ProcessManger;
use Task\Job;

class Client
{


    /**
     * @var Table
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
        set_exception_handler(function( Exception $exception){
            echo "Exception Caught: ". $exception->getMessage() .PHP_EOL;
        });
        //内存表初始化
        self::$job = Job::init(1024,$columns);
        //监听数据
        $subscribeType = $config['subscribe_type'];
        if(!isset($config['subscribe'][$subscribeType]) || empty($config['subscribe'][$subscribeType])) {
            throw new \Exception("$subscribeType not configured");
        }

        $subscribeClass = $config['subscribe'][$subscribeType]['class'];
        if(!class_exists($subscribeClass)) {
            throw new \Exception("$subscribeClass not found");
        }
        $instance = $subscribeClass::getInstance(self::$job);

        $instance->run($config[$subscribeType] ?? []);
        //定时器
        self::timeTick($config['time_tick'] ?? 5000);
        //设置主进程名字
        swoole_set_process_name("php:job-master");
        //回收子进程
        ProcessManger::wait();
    }


    protected static function timeTick($time = 1000)
    {
        Timer::tick($time, function () {
            $count = ProcessManger::getProcessNum();
            foreach(Job::$table as $row) {
                if(!isset($row['command'])) {
                    continue;
                }
                $command = $row['command'];
                ProcessManger::createProcess(function (Process $worker) use($command) {
                    $commandArr = explode(" ",$command);
                    $cmd =  array_shift($commandArr);
                    $args = $commandArr;
                    $worker->exec($cmd,$args);
                    //TODO 通知执行结果 超时判断
                    $worker->exit(0);
                });
            }
        });
    }
}