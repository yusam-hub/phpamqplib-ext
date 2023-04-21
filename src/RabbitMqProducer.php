<?php

namespace YusamHub\PhpAmqpLibExt;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMqProducer
{
    protected bool $enabled = true;
    protected array $queues = [];
    protected array $exchanges = [];

    protected RabbitMqConnection $rabbitMqConnection;
    protected AMQPStreamConnection $amqpStreamConnection;
    protected AMQPChannel $amqpChannel;

    protected string $activeExchange;
    protected string $activeRoutingKey;

    public function __construct(RabbitMqConnection $rabbitMqConnection, string $queueName, array $config = [])
    {
        foreach($config as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }

        $this->rabbitMqConnection = $rabbitMqConnection;

        $this->amqpStreamConnection = $this->rabbitMqConnection->newAMQPStreamConnection();

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Open channel");

        $this->amqpChannel = $this->amqpStreamConnection->channel();

        $this->activeExchange = $this->queues[$queueName]['bind']['exchange'];
        $this->activeRoutingKey = $this->queues[$queueName]['bind']['routing_key'];

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Exchange declare [ %s ] with type [ %s ]", $this->activeExchange, $this->exchanges[$this->activeExchange]['type']));

        $this->amqpChannel->exchange_declare($this->activeExchange,
            $this->exchanges[$this->activeExchange]['type'],
            $this->exchanges[$this->activeExchange]['passive'],
            $this->exchanges[$this->activeExchange]['durable'],
            $this->exchanges[$this->activeExchange]['exclusive'],
            $this->exchanges[$this->activeExchange]['auto_delete'],
            $this->exchanges[$this->activeExchange]['nowait'],
            $this->exchanges[$this->activeExchange]['arguments'],
            $this->exchanges[$this->activeExchange]['ticket']
        );

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Queue declare [ %s ]", $queueName));

        $this->amqpChannel->queue_declare($queueName,
            $this->queues[$queueName]['declare']['passive'],
            $this->queues[$queueName]['declare']['durable'],
            $this->queues[$queueName]['declare']['exclusive'],
            $this->queues[$queueName]['declare']['auto_delete'],
            $this->queues[$queueName]['declare']['nowait'],
            $this->queues[$queueName]['declare']['arguments'],
            $this->queues[$queueName]['declare']['ticket']
        );

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Queue bind [ %s ] with exchange / routing key [%s / %s]", $queueName, $this->activeExchange, $this->activeRoutingKey));

        $this->amqpChannel->queue_bind($queueName, $this->activeExchange, $this->activeRoutingKey);
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
     * @param array $messageProperties
     * @param bool $mandatory
     * @param bool $immediate
     * @param int|null $ticket
     * @return void
     * @throws \Exception
     */
    public function publishByQueue(
        string $messageBody,
        array $messageProperties = [],
        bool $mandatory = false,
        bool $immediate = false,
        ?int $ticket = null
    ): void {

        if (!$this->enabled) {
            $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog("Publish disabled in config");
            return;
        }

        $this->rabbitMqConnection->getPhpAmqpLibExt()->debugLog(sprintf("Publish message [ %s ] with properties:", $messageBody), $messageProperties);

        $message = new AMQPMessage($messageBody, $messageProperties);

        $this->amqpChannel->basic_publish($message, $this->activeExchange, $this->activeRoutingKey, $mandatory, $immediate, $ticket);
    }

    /**
     * @param string $queue
     * @param string $messageBody
     * @param array $messageProperties
     * @param bool $mandatory
     * @param bool $immediate
     * @param int|null $ticket
     * @return void
     * @throws \Exception
     */
    public function publishByQueueWithDeliveryModePersistent(
        string $messageBody,
        array $messageProperties = [],
        bool $mandatory = false,
        bool $immediate = false,
        ?int $ticket = null
    ): void {

        $this->publishByQueue(
            $messageBody,
            array_merge($messageProperties, [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            ]),
            $mandatory,
            $immediate,
            $ticket
        );
    }

    /**
     * @param string $messageBody
     * @param int $delayInMilliseconds
     * @param array $messageProperties
     * @param bool $mandatory
     * @param bool $immediate
     * @param int|null $ticket
     * @return void
     * @throws \Exception
     */
    public function publishByQueueWithDeliveryModePersistentDelay(
        string $messageBody,
        int $delayInMilliseconds = 0,
        array $messageProperties = [],
        bool $mandatory = false,
        bool $immediate = false,
        ?int $ticket = null
    ): void {

        $this->publishByQueueWithDeliveryModePersistent(
            $messageBody,
            array_merge($messageProperties, [
                'application_headers' => new AMQPTable([
                    'x-delay' => $delayInMilliseconds
                ]),
            ]),
            $mandatory,
            $immediate,
            $ticket
        );
    }
}