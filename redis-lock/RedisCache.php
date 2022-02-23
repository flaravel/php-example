<?php

class RedisCache
{
    protected static ?Redis $instance = null;

    public static function getInstance(): Redis
    {
        if (!is_null(self::$instance)) {
            return self::$instance;
        }
        self::$instance = new Redis();
        self::$instance->connect('127.0.0.1', '6379');
        return self::$instance;
    }
}