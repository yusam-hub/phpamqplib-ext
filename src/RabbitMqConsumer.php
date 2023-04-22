<?php

namespace YusamHub\PhpAmqpLibExt;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMqConsumer
{
    protected string $exchangeName  = '';
    protected string $exchangeType = AMQPExchangeType::TOPIC;
    protected array $exchangeArguments = [];
    protected string $queueName = '';
    protected string $consumerTag = '';
    protected array $routingKeys = [];
    protected int $countPerChannel = 10;
    protected RabbitMqConnection $rabbitMqConnection;
    public function __construct(RabbitMqConnection $rabbitMqConnection, array $config = [])
    {
        foreach($config as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }

        $this->rabbitMqConnection = $rabbitMqConnection;
    }

    /**
     * @throws \ErrorException
     * @throws \Exception
     */
    public function daemon(callable $callback): void
    {
        $connection = $this->rabbitMqConnection->newAMQPStreamConnection();

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Open channel");
        $channel = $connection->channel();

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Exchange declare [ %s ] with type [ %s ]",  $this->exchangeName,  $this->exchangeType), $this->exchangeArguments);
        $channel->exchange_declare(
            $this->exchangeName,
            $this->exchangeType,
            false,
            true,
            false,
            false,
            false,
            new AMQPTable($this->exchangeArguments),
            null
        );

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Queue declare [ %s ]",  $this->queueName));
        $channel->queue_declare(
            $this->queueName,
            false,
            true,
            false,
            false,
            false,
            null,
            null
        );

        foreach ($this->routingKeys as $routingKey) {
            $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Queue bind [ %s ] with exchange / routing key [ %s / %s ]",  $this->queueName, $this->exchangeName, $routingKey));
            $channel->queue_bind(
                $this->queueName,
                $this->exchangeName,
                $routingKey
            );
        }

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("QOS CountPerChannel [ %d ]",  $this->countPerChannel));
        $channel->basic_qos(0, $this->countPerChannel, false);

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Consume with tag [ %s ]",  $this->consumerTag));
        $channel->basic_consume(
            $this->queueName,
            $this->consumerTag,
            false,
            false,
            false,
            false,
            $callback,
            null,
            null
        );

        if (extension_loaded('pcntl'))
        {
            $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Start to process signals");
            define('AMQP_WITHOUT_SIGNALS', false);

            $callbackSigHandler = function (int $signalNumber) use ($channel) {
                $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Handling signal: #%d for stop consumer in channelId: #%d", $signalNumber, $channel->getChannelId()));
                $channel->stopConsume();
            };
            pcntl_signal(SIGQUIT, $callbackSigHandler);
            pcntl_signal(SIGTERM, $callbackSigHandler);
        } else {
            $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Unable to process signals");
        }

        register_shutdown_function(function(AMQPChannel $channel, AMQPStreamConnection $connection) {
            $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Shutdown channelId: #%d", $channel->getChannelId()));
            $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Shutdown channelId: #%d", $channel->getChannelId()));
            $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Channel close");
            $channel->close();
            $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Connection close");
            $connection->close();
        }, $channel, $connection);

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Consumer ready and waiting messages...");
        $channel->consume();

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Consumer stopped on channelId: #%d", $channel->getChannelId()));
    }
}