<?php
namespace Task;

trait Singleton
{
    private static $instance;

    public static function getInstance($job)
    {
        if(!self::$instance) {
            self::$instance = new static($job);
        }
        return self::$instance;
    }
}