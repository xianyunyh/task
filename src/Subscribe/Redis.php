<?php

namespace Task\Subscribe;

use Task\Job;
use Task\Process\ProcessManger;
use Swoole\Coroutine\Redis as SwooleRedis;
use Task\Singleton;
use Task\Tools\Message;

class Redis
{
    use Singleton;

    /**
     * @var Job
     */
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
        ProcessManger::createProcess(function ($worker) use($config){
            go(function () use($config) {
                $redis = new SwooleRedis();
                $host = $config['host'] ?? $this->host;
                $port = $config['port'] ?? $this->port;
                $password = $config['password'] ?? $this->passord;
                $topic = $config['topic'] ?? $this->topic;
                $redis->connect($host, $port, $password);
                while (true) {
                    $values = $redis->subscribe([$topic]);
                    $message = $values[2];
                    $message = Message::decode($message);
                    if (isset($message['id'])) {
                        ($this->job)::add($message['id'], ['data' => $message]);
                    }

                }
            });
        });
    }

}