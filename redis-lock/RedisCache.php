<?php

class RedisCache
{

    public static function getInstance(): Redis
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', '6379');
        return $redis;
    }
}