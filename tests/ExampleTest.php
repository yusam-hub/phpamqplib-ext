<?php

namespace YusamHub\PhpAmqpLibExt\Tests;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use YusamHub\PhpAmqpLibExt\PhpAmqpLibExt;

class ExampleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws \Exception
     */
    public function testDefault()
    {
        $phpAmqpLibExt = new PhpAmqpLibExt(require __DIR__ . '/../config/config.php');

        $producer = $phpAmqpLibExt->getConnection()->newProducer(
            'message_informer_delay',
            'informer_delay.message.sending_push_messages',
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => new AMQPTable([
                    'x-delay' => 5000
                ])
            ]
        );
        $producer->publish("test");
        $producer = null;

        $this->assertTrue(true);
    }
}