# 关于Redis分布式锁
应用场景:
比如一个活动发放优惠券，需要用户自己领取，后台设置好优惠券并设置1000个发放数量，当领取完成之后，客户端显示为0，领取之后将优惠券增加到用户的账户当中

![Laravel](https://cdn.learnku.com/uploads/images/202202/24/30430/wf5QL7tkGr.png!large)

1\. 在没做任何并发处理的情况下出现的问题?
- 死锁问题
    - 在减少数量成功之后，加用户优惠券，为了保证数据的一致性，肯定要做事务处理，为了防止数据错乱，update操作会增加行锁，多个事物会对这个优惠券数量进行资源争抢，造成锁等待，导致死锁
- 领取的优惠券数量超过1000
    - 当并发查询时的时候，比如两个并发请求查询到都只剩下1张优惠券，因为并行，都进入到扣减数量的条件，造成扣减数量为负数

2\. 使用Redis分布式锁可以避免这些问题
- 命令： `set($key, $value, ['nx', 'ex' => $ttl])`
- 当操作的key不存在时候，会进行创建，当key已经存在，返回null，因为是**原子性**操作，所以当多个请求进来，其中一个请求抢到了锁那么其他同一时刻与它争抢锁的请求全部返回失败
    - 加过期时间是为了防止解锁失败，导致锁无法解锁，造成死锁问题
    - 当业务逻辑完成，如果锁还没过期，需要手动解锁，所以在设置value值的时候，必须具有当前请求的唯一性值，方便解锁的时候做验证是否来自同一个请求，解锁时在get锁值时候判断是否与当前请求值相等，然后在删除，并且get与del一起操作并非原子性的，所以需要借助lua脚本使这个两个命令是原子性操作

结合这个逻辑，做了一下伪代码：

```php
<?php

require 'RedisCache.php';

// 假设优惠券设置成10
//RedisCache::getInstance()->set('coupon_number', 10)   // 本机测试小一点

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
```

这是并发请求后的数据， 测试 300个用户，15秒类请求完，也就是一秒20个并发


![Laravel](https://cdn.learnku.com/uploads/images/202202/24/30430/uW73BfdjgK.png!large)

![Laravel](https://cdn.learnku.com/uploads/images/202202/24/30430/2LbCScLghk.png!large)

Redis 统计的数据

- error_number 是没抢到锁的进程数量
- coupon_number 已经抢完的优惠券数量
- coupon_number_is_zero 抢到锁但是优惠券数量已经抢完了的进程数量

下图加起来刚好有300个请求(包含初始值为10的优惠券)

![Laravel](https://cdn.learnku.com/uploads/images/202202/24/30430/0h2AebXC04.png!large)

一个好用的php lock扩展包

文档： https://symfony.com/doc/current/components/lock.html