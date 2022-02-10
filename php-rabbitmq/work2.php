<?php
/**
 * 接收消息
 */

require 'MqConnectionFactory.php';
require 'Consumers.php';

$data = require "config.php";

// work
//try {
//    $publisher = new Consumers($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
//    $publisher->receiveWorkQueue();
//} catch (Exception $e) {
//    var_dump('connection mq error:' . $e->getMessage());
//}


// pub sub
//try {
//    $publisher = new Consumers($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
//    $publisher->receivePubSubQueue('pub_sub_queue2');
//} catch (Exception $e) {
//    var_dump('connection mq error:' . $e->getMessage());
//}

// route queue
//try {
//    $publisher = new Consumers($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
//    $publisher->receiveRouteQueue('route_queue2');
//} catch (Exception $e) {
//    var_dump('connection mq error:' . $e->getMessage());
//}

// topic queue
try {
    $publisher = new Consumers($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
    $publisher->receiveTopicQueue('topic_queue2');
} catch (Exception $e) {
    var_dump('connection mq error:' . $e->getMessage());
}