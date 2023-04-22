<?php

use YusamHub\PhpAmqpLibExt\PhpAmqpLibExt;

require_once(__DIR__ . "/../vendor/autoload.php");

$phpAmqpLibExt = new PhpAmqpLibExt(require __DIR__ . '/../config/config.php');

$messageCounter = 0;

$config = [
    'exchangeName' => 'exchange1',
    'exchangeType' => \PhpAmqpLib\Exchange\AMQPExchangeType::TOPIC,
    //'exchangeType' => 'x-delayed-message',
    'exchangeArguments' => [
        //'x-delayed-type' => \PhpAmqpLib\Exchange\AMQPExchangeType::TOPIC,
    ],
    'queueName' => $argv[1]??'queue1',
    'consumerTag' => $argv[2]??'consumerTag1',
    'routingKeys' => [
        'route1'
    ]
];

$consumer = $phpAmqpLibExt->getConnection()->newConsumer($config);

$callback = function (\PhpAmqpLib\Message\AMQPMessage $message) use($phpAmqpLibExt) {
    $phpAmqpLibExt->debugLog(sprintf("[ %d | %s | %s ] Incoming message: [ %s ]", $message->getDeliveryTag(), date("Y-m-d H:i:s"), $message->getConsumerTag(), $message->getBody()));
    $message->ack();
};

try {
    $consumer->daemon($callback);
} catch (ErrorException|Exception $e) {
}