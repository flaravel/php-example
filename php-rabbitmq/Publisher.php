<?php

class Publisher extends MqConnectionFactory
{
    /**
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function sendSimpleQueue()
    {
        $message = 'sendSimpleQueue!';
        $queue = new AMQPQueue($this->channel);
        $queue->setName('simple_queue');
        $queue->declareQueue();
        echo 'Send Message: '. $this->exchange->publish($message, $queue->getName()) . "\n";
        echo "Message Is Sent: " . $message . "\n";
        $this->close();
    }


    /**
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     * @throws AMQPChannelException
     * @throws AMQPExchangeException
     */
    public function sendWorkQueue($i)
    {
        $message = 'sendWorkQueue_'.$i;
        $queue = new AMQPQueue($this->channel);
        $queue->setName('work_queue');
        $queue->declareQueue();
        echo 'Send Message: '. $this->exchange->publish($message, $queue->getName()) . "\n";
        echo "Message Is Sent: " . $message . "\n";
    }




    /**
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     * @throws AMQPChannelException
     * @throws AMQPExchangeException
     */
    public function sendPubSubQueue()
    {
        $message = 'sendPubSubQueue';

        $queue = new AMQPQueue($this->channel);
        $queue->setName('pub_sub_queue1');
        $queue->declareQueue();

        $queue2 = new AMQPQueue($this->channel);
        $queue2->setName('pub_sub_queue2');
        $queue2->declareQueue();

        $this->exchange->setName('pub_sub_exchange_fanout');
        $this->exchange->setType(AMQP_EX_TYPE_FANOUT);  // 广播
        $this->exchange->declareExchange();

        $queue->bind('pub_sub_exchange_fanout');
        $queue2->bind('pub_sub_exchange_fanout');

        echo 'Send Message: '. $this->exchange->publish($message) . "\n";
        echo "Message Is Sent: " . $message . "\n";

        $this->close();
    }


    /**
     * route queue
     *
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function sendRouteQueue()
    {
        $message = '这是error信息';

        $queue = new AMQPQueue($this->channel);
        $queue->setName('route_queue1');
        $queue->declareQueue();

        $queue2 = new AMQPQueue($this->channel);
        $queue2->setName('route_queue2');
        $queue2->declareQueue();

        $this->exchange->setName('route_exchange_direct');
        $this->exchange->setType(AMQP_EX_TYPE_DIRECT);
        $this->exchange->declareExchange();

        // bind info
        $queue->bind('route_exchange_direct', 'info');

        // bind error, waring
        $queue2->bind('route_exchange_direct', 'error');
        $queue2->bind('route_exchange_direct', 'waring');

        echo 'Send Message: '. $this->exchange->publish($message, 'waring') . "\n";
        echo "Message Is Sent: " . $message . "\n";
        $this->close();
    }


    /**
     * topic queue
     *
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function sendTopicQueue($message, $error)
    {
        $queue = new AMQPQueue($this->channel);
        $queue->setName('topic_queue1');
        $queue->setFlags(AMQP_DURABLE);     // 持久化
        $queue->declareQueue();

        $queue2 = new AMQPQueue($this->channel);
        $queue2->setName('topic_queue2');
        $queue2->setFlags(AMQP_DURABLE);     // 持久化
        $queue2->declareQueue();

        $this->exchange->setName('topic_exchange');
        $this->exchange->setType(AMQP_EX_TYPE_TOPIC);
        $this->exchange->setFlags(AMQP_DURABLE);     // 持久化
        $this->exchange->declareExchange();

        // bind info
        $queue->bind('topic_exchange', '*.info');

        // bind error, waring
        $queue2->bind('topic_exchange', '*.error');
        $queue2->bind('topic_exchange', '*.waring');

        $this->channel->confirmSelect();

        $this->channel->setConfirmCallback(function ($delivery_tag,$multiple) {
            var_dump($delivery_tag, $multiple);
            return true;
        }, function () {
            return true;
        });

        // 持久化
        echo 'Send Message: '. $this->exchange->publish($message, 'goods.'.$error,AMQP_NOPARAM, array('delivery_mode'=>2,'priority'=>9)) . "\n";

        echo "Message Is Sent: " . $message . "\n";

        $this->channel->waitForConfirm(5);

    }
}