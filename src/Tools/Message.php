<?php
namespace Task\Tools;
use Task\Job;
use Task\Singleton;
use Task\Tools\Log;

class Message
{
    use Singleton;

    protected $typeList = ['add','update','delete','all','ping'];
    protected $job;
    protected $log;

    public function __construct(Job $job)
    {
        $this->job = $job;
        $this->log = new Log();
    }

    /**
     * 解开消息
     * @param $message
     * @return bool|array
     */
    public function decode($message)
    {
        if(empty($message)) {
            return false;
        }
        //now use json_decode
        return json_decode($message,true);
    }

    /**
     * 处理消息handler
     * @param $message
     * @param Job $jobInstance
     * @return array|bool|string
     */
    public function handler($message)
    {
        $message = $this->decode($message);
        if(false === $message) {
            // log
            return false;
        }
        $type = $message['type'];
        $data = $message['data'];
        if(!isset($data['task_id']) && empty($data['task_id'])) {
            echo "1";
            $this->log->write("tak_id不存在\n消息内容：".json_encode($message));
            return false;
        }
        $jobInstance =($this->job);
        switch ($type){
            case 'add':
                $jobInstance::add($data['task_id'],$data);
                break;
            case 'delete':
                $jobInstance::remove($data['task_id']);
                break;
            case 'update':
                $jobInstance::update($data['task_id'],$data);
                break;
            case 'ping':
                return 'pong';
                break;
            default:
                return [];
                break;
        }
    }

}