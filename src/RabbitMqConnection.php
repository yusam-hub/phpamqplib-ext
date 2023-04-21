<?php

namespace YusamHub\PhpAmqpLibExt;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMqConnection
{
    public $host = "localhost";
    public $port = 5672;
    public $username;
    public $password;
    public $vhost = '/';
    public $insist = false;
    public $login_method = 'AMQPLAIN';
    public $login_response = null;
    public $locale = 'en_US';
    public $connection_timeout = 10.0;
    public $read_write_timeout = 10.0;
    public $context = null;
    public $keepalive = false;
    public $heartbeat = 0;

    protected PhpAmqpLibExt $phpAmqpLibExt;

    public function __construct(PhpAmqpLibExt $phpAmqpLibExt, array $config = [])
    {
        foreach($config as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }

        $this->phpAmqpLibExt = $phpAmqpLibExt;
    }

    /**
     * @throws \Exception
     */
    public function newAMQPStreamConnection(): AMQPStreamConnection
    {
        $this->phpAmqpLibExt->debugLog(sprintf("Open connection on [ %s:%s ] with vhost [ %s ]", $this->host, $this->port, $this->vhost));

        return new AMQPStreamConnection(
            $this->host,
            $this->port,
            $this->username,
            $this->password,
            $this->vhost,
            $this->insist,
            $this->login_method,
            $this->login_response,
            $this->locale,
            $this->connection_timeout,
            $this->read_write_timeout,
            $this->context,
            $this->keepalive,
            $this->heartbeat
        );
    }

    /**
     * @return PhpAmqpLibExt
     */
    public function getPhpAmqpLibExt(): PhpAmqpLibExt
    {
        return $this->phpAmqpLibExt;
    }

    /**
     * @param string $exchangeName
     * @param string $routingKey
     * @param array $messageProperties
     * @param bool $messageMandatory
     * @param bool $messageImmediate
     * @param int|null $messageTicket
     * @param string|null $producerName
     * @return RabbitMqProducer
     * @throws \Exception
     */
    public function newProducer(
        string $exchangeName,
        string $routingKey,
        array $messageProperties = [],
        bool $messageMandatory = false,
        bool $messageImmediate = false,
        ?int $messageTicket = null,
        ?string $producerName = null
    ): RabbitMqProducer
    {
        if (empty($producerName)) {
            $producerName = $this->phpAmqpLibExt->producerDefault;
        }

        $this->phpAmqpLibExt->debugLog(sprintf("Setup producer [ %s ]", $producerName));

        return new RabbitMqProducer(
            $this,
            $this->phpAmqpLibExt->producers[$producerName],
            $exchangeName,
            $routingKey,
            $messageProperties,
            $messageMandatory,
            $messageImmediate,
            $messageTicket
        );
    }
}