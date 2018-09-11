<?php
namespace  Task\Subscribe;
use Task\Process\ProcessManger;
use Swoole\Coroutine\Redis as SwooleRedis;
class Redis
{
    protected $job;
    protected $host = '127.0.0.1';
    protected $port = '6379';
    protected $passord = '';
    protected $topic = 'pubsub';


    public function __construct($job)
    {
        $this->job = $job;

    }
    public function run(array $config)
    {
        ProcessManger::createProcess(function($worker){
            go(function(){
                $redis = new SwooleRedis();
                $host = $config['host'] ?? $this->host;
                $port = $config['port'] ?? $this->port;
                $password = $config['password'] ?? $this->passord;
                $topic = $config['topic'] ?? $this->topic;
                $redis->connect($host,$port,$password);
                while (true) {
                    $values = $redis->subscribe([$topic]);
                    $messge = $values[2];
                    ($this->job)::add($messge,['data'=>$messge]);                 
                }
            });
        });
    }

}