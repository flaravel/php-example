<?php

require 'RedisCache.php';

// 假设优惠券设置成10
//RedisCache::getInstance()->set('coupon_number', 10)   // 本机发测试小一点

$uuid = uuid_create(1); // uuid 安装 php的 uuid扩展
$key = 'coupon:1';  // 领取的优惠券id为1，当做key值
$couponNumber = 'coupon_number'; // 优惠券领取数量key
$errorNumber = 'error_number'; // 锁没抢到的进程数量key
$couponNumberIsZero = 'coupon_number_is_zero'; // 抢到锁但是优惠券领取完了的进程数量key

// 生成锁
$lock = RedisCache::getInstance()->set($key, $uuid, ['NX', 'EX' => 1]);

if (!$lock) {
    // incr 是原子操作
    RedisCache::getInstance()->incr($errorNumber);
    echo '您未能抢到优惠券，请重试';
    header('HTTP/1.1 500 您未能抢到优惠券，请重试', true, 500);
    die();
}
try {
    // 判断优惠券数量是否大于0
    if (RedisCache::getInstance()->get($couponNumber) == 0) {
        RedisCache::getInstance()->incr($couponNumberIsZero);
        echo '优惠券已经抢完了';
        header('HTTP/1.1 422 优惠券已经抢完了', true, 422);
        die();
    }
    // 减少优惠券数量
    RedisCache::getInstance()->decr($couponNumber);
    // 增加用户优惠券
    usleep(200);
    // 其他操作
    echo '抢到优惠券--';
} finally {
    // 解锁 放在lua脚本下执行 保证get与del是原子性操作
    $script = '
        if redis.call("get",'.$key.') == '.$uuid.' then
            return redis.call("del",'.$key.')
        else
            return 0
        end
    ';
    RedisCache::getInstance()->eval($script);
}
