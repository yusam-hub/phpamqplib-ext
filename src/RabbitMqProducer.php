<?php

namespace YusamHub\PhpAmqpLibExt;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqProducer
{
    protected bool $enabled = true;
    protected RabbitMqConnection $rabbitMqConnection;
    protected AMQPStreamConnection $amqpStreamConnection;
    protected AMQPChannel $amqpChannel;
    protected string $activeExchange;
    protected string $activeRoutingKey;
    protected array $activeMessageProperties = [];
    protected bool $activeMessageMandatory = false;
    protected bool $activeMessageImmediate = false;
    protected ?int $activeMessageTicket = null;

    /**
     * @throws \Exception
     */
    public function __construct(
        RabbitMqConnection $rabbitMqConnection,
        array $config,
        string $exchangeName,
        string $routingKey,
        array $messageProperties = [],
        bool $messageMandatory = false,
        bool $messageImmediate = false,
        ?int $messageTicket = null
    )
    {
        foreach($config as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }

        $this->activeExchange = $exchangeName;
        $this->activeRoutingKey = $routingKey;
        $this->activeMessageProperties = $messageProperties;
        $this->activeMessageMandatory = $messageMandatory;
        $this->activeMessageImmediate = $messageImmediate;
        $this->activeMessageTicket = $messageTicket;

        $this->rabbitMqConnection = $rabbitMqConnection;
        $this->amqpStreamConnection = $this->rabbitMqConnection->newAMQPStreamConnection();

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Open channel");
        $this->amqpChannel = $this->amqpStreamConnection->channel();
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Channel close");
        $this->amqpChannel->close();

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Connection close");
        $this->amqpStreamConnection->close();
    }


    /**
     * @param string $messageBody
     * @return void
     */
    public function publish(string $messageBody): void
    {

        if (!$this->enabled) {
            $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Publish is disabled in config producer");
            return;
        }

        $message = new AMQPMessage($messageBody, $this->activeMessageProperties);

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Publish message [ %s ]", $messageBody));
        $this->amqpChannel->basic_publish(
            $message,
            $this->activeExchange,
            $this->activeRoutingKey,
            $this->activeMessageMandatory,
            $this->activeMessageImmediate,
            $this->activeMessageTicket
        );
    }

}