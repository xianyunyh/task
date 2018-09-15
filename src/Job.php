<?php

namespace Task;

use Swoole\Table;

class Job
{
    /**
     * @var Table
     */
    public static $table;

    public static function init($size = 1024, $columns = [])
    {
        $table = new Table($size);
        foreach ($columns as $column) {
            $table->column($column[0], $column[1], $column[2]);
        }
        $table->create();
        self::$table = $table;
        return new self();
    }

    public static function add($key, array $data)
    {
        return self::$table->set($key, $data);
    }

    public static function get($key)
    {
        return self::$table->get($key);
    }

    public static function remove($key)
    {
        return self::$table->del($key);
    }

    public static function update($key, array $data)
    {
        if (self::$table->exist($key)) {
            return self::$table->set($key, $data);
        }
        return false;
    }

    public static function count()
    {
        return self::$table->count();
    }

    public static function all()
    {
        $rows = [];
        if (self::count() <= 0) {
            return $rows;
        }
        foreach (self::$table as $row) {
            $rows[] = $row;
        }
        return $rows;
    }


}