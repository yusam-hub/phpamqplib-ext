<?php

use YusamHub\PhpAmqpLibExt\PhpAmqpLibExt;

require_once(__DIR__ . "/../vendor/autoload.php");

$phpAmqpLibExt = new PhpAmqpLibExt(require __DIR__ . '/../config/config.php');

$messageCounter = 0;

$consumer = $phpAmqpLibExt->getConnection()->newConsumer([
    'exchangeName' => 'message_informer_delay',
    'exchangeType' => 'x-delayed-message',
    'exchangeArguments' => [
        'x-delayed-type' => \PhpAmqpLib\Exchange\AMQPExchangeType::TOPIC,
    ],
    'queueName' => 'informer_delay_sending_push_messages',
    'consumerTag' => 'sending_push_messages',
    'routingKeys' => [
        'informer_delay.message.sending_push_messages'
    ]
]);

$callback = function (\PhpAmqpLib\Message\AMQPMessage $message) use($phpAmqpLibExt, &$messageCounter) {
    $messageCounter++;
    $phpAmqpLibExt->debugLog(sprintf("[ %d ] Incoming message: [ %s ]", $messageCounter, $message->getBody()));
    $message->ack();
};

try {
    $consumer->daemon($callback);
} catch (ErrorException|Exception $e) {
}