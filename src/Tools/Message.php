<?php
namespace Task\Tools;
use Task\Job;
use Task\Singleton;

class Message
{
    use Singleton;

    protected $typeList = ['add','update','delete','select','ping'];
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

    public function handler($message, Job $jobInstance)
    {
        $message = $this->decode($message);
        if(false === $message) {
            return false;
        }
        $type = $message['type'];
        $data = $message['data'];
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
            case 'select':
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