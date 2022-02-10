<?php

class MqConnectionFactory
{
    /**
     * 链接ip
     *
     * @var string
     */
    protected string $host = '';

    /**
     * 链接端口
     *
     * @var string
     */
    protected string $port = '';

    /**
     * 使用的虚拟机
     *
     * @var string
     */
    protected string $vhost = '/';

    /**
     * 登录账号
     *
     * @var string
     */
    protected string $login = '';

    /**
     * 登录密码
     *
     * @var string
     */
    protected string $password = '';


    /**
     * mq 链接对象
     *
     * @var AMQPConnection|null
     */
    protected ?AMQPConnection $connection = null;

    /**
     * @var AMQPChannel|null
     */
    protected ?AMQPChannel $channel = null;

    /**
     * @var AMQPExchange|null
     */
    protected ?AMQPExchange $exchange = null;


    /**
     * MqConnection.
     *
     * @param string $host
     * @param string $port
     * @param string $vhost
     * @param string $login
     * @param string $password
     *
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    public function __construct(
        string $host,
        string $port,
        string $vhost,
        string $login,
        string $password
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->vhost = $vhost;
        $this->login = $login;
        $this->password = $password;

        $this->createMqConnection();
        $this->createMqChannel();
        $this->createMqExchange();

    }

    /**
     * create mq connection.
     *
     * @throws AMQPConnectionException
     * @return $this
     */
    protected function createMqConnection(): self
    {
        $this->connection = new AMQPConnection([
            'host'      => $this->host,
            'port'      => $this->port,
            'login'     => $this->login,
            'password'  => $this->password,
            'vhost'     => $this->vhost,
        ]);
        $this->connection->connect();
        return $this;
    }

    /**
     * create mq channel
     *
     * @return $this
     * @throws AMQPConnectionException
     */
    protected function createMqChannel(): self
    {
        $this->channel = new AMQPChannel($this->connection);
        return $this;
    }


    /**
     * create mq exchange
     *
     * @return $this
     *
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     */
    protected function createMqExchange(): MqConnectionFactory
    {
        $this->exchange = new AMQPExchange($this->channel);
        return $this;
    }

    public function close()
    {
        $this->connection->disconnect();
        $this->channel->close();
    }
}