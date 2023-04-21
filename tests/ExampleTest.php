<?php

namespace YusamHub\PhpAmqpLibExt\Tests;

use YusamHub\PhpAmqpLibExt\PhpAmqpLibExt;

class ExampleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \Exception
     */
    public function testDefault()
    {
        $phpAmqpLibExt = new PhpAmqpLibExt(require __DIR__ . '/../config/config.php');

        $producer = $phpAmqpLibExt->getConnection()->newProducer("informer_delay_sending_push_messages");
        $delay = 2000;
        $messageBody = sprintf("publishByQueueWithDeliveryModePersistentDelay_%d_%d", 1, $delay);
        $producer->publishByQueueWithDeliveryModePersistentDelay(
            $messageBody,
            $delay
        );
        $delay = 5000;
        $messageBody = sprintf("publishByQueueWithDeliveryModePersistentDelay_%d_%d", 2, $delay);
        $producer->publishByQueueWithDeliveryModePersistentDelay(
            $messageBody,
            $delay
        );

        $producer = null;

        /*$exchange = 'router';
        $queue = 'msgs';

        $connection = new AMQPStreamConnection('localhost', '5672', 'admin', 'Qwertyu1', '/');
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
        $channel->queue_bind($queue, $exchange);

        $messageBody = 'test message body';
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange);

        $channel->close();
        $connection->close();*/

        $this->assertTrue(true);
    }
}