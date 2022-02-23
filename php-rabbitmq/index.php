<?php

require 'MqConnectionFactory.php';
require 'Publisher.php';

$data = require "config.php";

// simple
//try {
//    $publisher = new Publisher($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
//    $publisher->sendSimpleQueue();
//} catch (Exception $e) {
//    var_dump('connection mq error:'. $e->getMessage());
//}

// work
//try {
//    $publisher = new Publisher($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
//    for ($i = 0; $i <= 10; $i++) {
//       $publisher->sendWorkQueue($i);
//    }
//    $publisher->close();
//} catch (Exception $e) {
//    var_dump('connection mq error:'. $e->getMessage());
//}

// pubSub
//try {
//    $publisher = new Publisher($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
//    $publisher->sendPubSubQueue();
//    $publisher->close();
//} catch (Exception $e) {
//    var_dump('connection mq error:'. $e->getMessage());
//}

// route queue
//try {
//    $publisher = new Publisher($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
//    $publisher->sendRouteQueue();
//} catch (Exception $e) {
//    var_dump('connection mq error:'. $e->getMessage());
//}

// topic queue
try {
    $publisher = new Publisher($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
    $publisher->sendTopicQueue('error日志-'.date('Y-m-d H:i:s'), 'error');
//    $publisher->sendTopicQueue('waring日志-'.date('Y-m-d H:i:s'), 'waring');
//    $publisher->sendTopicQueue('info日志-'.date('Y-m-d H:i:s'), 'info');
    $publisher->close();
} catch (Exception $e) {
    var_dump('connection mq error:'. $e->getMessage());
}