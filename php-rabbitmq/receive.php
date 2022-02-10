<?php
/**
 * 接收消息
 */

require 'MqConnectionFactory.php';
require 'Consumers.php';

$data = require "config.php";

// simple
try {
    $publisher = new Consumers($data['host'], $data['port'], $data['vhost'], $data['login'], $data['password']);
    $publisher->receiveSimpleQueue();
} catch (Exception $e) {
    var_dump('connection mq error:' . $e->getMessage());
}
