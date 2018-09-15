<?php


namespace Task\Tools;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use \Exception;

class Log
{
    private $logHandler;
    public function __construct()
    {
        $dateFormat = "Y-m-d, H:i:s";
        $output = "%datetime% > %level_name% > %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler('/tmp/my_app.log', Logger::DEBUG);
        $stream->setFormatter($formatter);
        $securityLogger = new Logger('security');
        $securityLogger->pushHandler($stream);
        $this->logHandler = $securityLogger;
    }

    public function write($message = '',$type ='warn')
    {
        if(method_exists($this->logHandler,$type)) {
            return $this->logHandler->$type($message);
        }
        throw new Exception("不存在的方法");
    }
}