<?php
namespace  Task\Subscribe;
use Task\Process\ProcessManger;
use Swoole\Coroutine\Redis as SwooleRedis;
class Redis
{
    protected $job;

    public function __construct($job)
    {
        $this->job = $job;

    }
    public function run()
    {
        ProcessManger::createProcess(function(){
            go(function(){
                $redis = new SwooleRedis();
                $redis->connect('127.0.0.1', 6379);
                while (true) {
                    $values = $redis->subscribe(['pubsub']);
                    $messge = $values[2];
                    ($this->job)::add($messge,['data'=>$messge]);                 
                }
            });
        });
    }

}