<?php

namespace Task\Process;

use Swoole\Process;
use Swoole\Server;

class ProcessManger
{
    protected static $processList = [];

    protected $maxNumber;


    public static function createProcess(callable $callback)
    {
        $process = new Process($callback,false,false);
        self::$processList[$process->pid] = $process;
        $process->start();
        return $process;
    }
    /**
     * 回收子进程
     */
    public static function wait()
    {
        Process::signal(SIGCHLD, function($sig) {
            //必须为false，非阻塞模式
            while($ret =  Process::wait(false)) {
                echo "PID={$ret['pid']}\n";
                unset(self::$processList[$ret['pid']]);
            }
        });
    }

    /**
     * 获取进程的数量
     * @return int
     */
    public  static  function getProcessNum()
    {
        return count(self::$processList);
    }

    /**
     * 退出进程
     * @param int $pid
     */
    public static function quit(int $pid)
    {
        if(key_exists($pid,self::$processList)) {
            $process = self::$processList[$pid];
            $process->exit();
        }
    }

    /**
     * 通过进程id 获取进程实例
     * @param int $pid
     * @return bool|mixed
     */
    public static function getProcess(int $pid)
    {
        if(key_exists($pid,self::$processList)) {
            $process = self::$processList[$pid];
            return $process;
        }
        return false;
    }

}