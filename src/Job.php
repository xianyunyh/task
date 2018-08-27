<?php
namespace Task;

use Swoole\Table;
class Job
{
    /**
     * @var Table
     */
    public static $table;

    public static function init($size = 1024,$columns = [] )
    {
        $table = new Table($size);
        foreach ($columns as $column) {
            $table->column($columns[0],$column[1],$columns[2]);
        }
        $table->create();
        self::$table = $table;
        return $table;
    }

    public static function add($key,array $data)
    {
        return self::$table->set($key,$data);
    }

    public static function remove($key)
    {
        return self::$table->del($key);
    }

    public static function update($key,array $data)
    {
        if(self::$table->exist($key)) {
            return self::$table->set($key,$data);
        }
        return false;
    }


}