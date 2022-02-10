<?php

class Consumers extends MqConnectionFactory
{
    /**
     * simple
     *
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPEnvelopeException
     * @throws AMQPQueueException
     */
    public function receiveSimpleQueue()
    {
        $queue = new AMQPQueue($this->channel);
        $queue->setName('simple_queue');
        $queue->declareQueue();
        function processMessage(AMQPEnvelope $envelope, $queue) {
            $msg = $envelope->getBody();
            var_dump("Received ExchangeName: ".$envelope->getExchangeName());
            var_dump("Received RouteKey: ".$envelope->getRoutingKey());
            var_dump("Received: " . $msg);
            var_dump("---------------------");
            $queue->ack($envelope->getDeliveryTag());
        }
        $queue->consume('processMessage');
    }

    /**
     * work
     *
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPEnvelopeException
     * @throws AMQPQueueException
     */
    public function receiveWorkQueue()
    {
        $queue = new AMQPQueue($this->channel);
        $queue->setName('work_queue');
        $queue->declareQueue();
        function processMessage(AMQPEnvelope $envelope, $queue) {
            $msg = $envelope->getBody();
            var_dump("Received ExchangeName: ".$envelope->getExchangeName());
            var_dump("Received RouteKey: ".$envelope->getRoutingKey());
            var_dump("Received: " . $msg);
            var_dump("---------------------");
            $queue->ack($envelope->getDeliveryTag());
        }
        $queue->consume('processMessage');
    }

    /**
     * pub/sub
     *
     * @param $queueName
     *
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPEnvelopeException
     * @throws AMQPQueueException
     */
    public function receivePubSubQueue($queueName)
    {
        $queue = new AMQPQueue($this->channel);
        $queue->setName($queueName);
        $queue->declareQueue();
        function processMessage(AMQPEnvelope $envelope, $queue) {
            $msg = $envelope->getBody();
            $name = $queue->getName();
            if ($name == 'pub_sub_queue1') {
                var_dump('我是pub/sub 1系统');
            }
            if ($name == 'pub_sub_queue2') {
                var_dump('我是pub/sub 2系统');
            }
            var_dump("Received: " . $msg);
            var_dump("---------------------");
            $queue->ack($envelope->getDeliveryTag());
        }
        $queue->consume('processMessage');
    }

    /**
     * route queue
     *
     * @param $queueName
     *
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPEnvelopeException
     * @throws AMQPQueueException
     */
    public function receiveRouteQueue($queueName)
    {
        $queue = new AMQPQueue($this->channel);
        $queue->setName($queueName);
        $queue->declareQueue();
        function processMessage(AMQPEnvelope $envelope, $queue) {
            $msg = $envelope->getBody();
            $name = $queue->getName();
            if ($name == 'route_queue1') {
                var_dump('我是route 1系统');
            }
            if ($name == 'route_queue2') {
                var_dump('我是route 2系统');
            }
            var_dump("Received: " . $msg);
            var_dump("---------------------");
            $queue->ack($envelope->getDeliveryTag());
        }
        $queue->consume('processMessage');
    }


    /**
     * topic queue
     *
     * @param $queueName
     *
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPEnvelopeException
     * @throws AMQPQueueException
     */
    public function receiveTopicQueue($queueName)
    {
        $queue = new AMQPQueue($this->channel);
        $queue->setName($queueName);
        $queue->setFlags(AMQP_DURABLE);     // 持久化
        $queue->declareQueue();
        function processMessage(AMQPEnvelope $envelope, AMQPQueue $queue) {
            $msg = $envelope->getBody();
            $name = $queue->getName();
            if ($name == 'topic_queue1') {
                var_dump('我是topic 1系统');
            }
            if ($name == 'topic_queue2') {
                var_dump('我是topic 2系统');
            }
            var_dump("Received: " . $msg);
            var_dump("---------------------");
            $queue->ack($envelope->getDeliveryTag());
        }
        $queue->consume('processMessage');
    }
}