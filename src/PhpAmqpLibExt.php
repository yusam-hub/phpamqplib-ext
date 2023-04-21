<?php

namespace YusamHub\PhpAmqpLibExt;

class PhpAmqpLibExt
{
    public bool $isDebugging = false;
    public string $connectionDefault;
    public array $connections;
    public string $producerDefault;
    public array $producers;

    /**
     * @var RabbitMqConnection[]
     */
    protected array $rabbitMqConnections = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach($config as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debugLog(string $message, array $context = []): void
    {
        if (!$this->isDebugging) return;
        echo $message . (!empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '') .  PHP_EOL;
    }

    /**
     * @param string|null $connectionName
     * @return RabbitMqConnection
     */
    public function getConnection(?string $connectionName = null): RabbitMqConnection
    {
        if (empty($connectionName)) {
            $connectionName = $this->connectionDefault;
        }

        $this->debugLog(sprintf("Setup connection [ %s ]", $connectionName));

        if (isset($this->rabbitMqConnections[$connectionName])) {
            return $this->rabbitMqConnections[$connectionName];
        }

        return $this->rabbitMqConnections[$connectionName] = new RabbitMqConnection($this, $this->connections[$connectionName]);
    }
}