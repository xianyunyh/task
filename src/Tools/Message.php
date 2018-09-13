<?php
namespace Task\Tools;

class Message
{
    /**
     * 解开消息
     * @param $message
     * @return bool|array
     */
    public static function decode($message)
    {
        if(empty($message)) {
            return false;
        }
        //now use json_decode
        return json_decode($message,true);
    }

}