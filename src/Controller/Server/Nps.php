<?php

namespace Yggverse\Pulsar\Controller\Server;

use \Ratchet\MessageComponentInterface;

class Nps implements MessageComponentInterface
{
    private object $_config;

    private \Yggverse\Pulsar\Model\Database $_database;

    public function __construct(
        \Yggverse\Pulsar\Model\Config $config,
        \Yggverse\Pulsar\Model\Database $database
    ) {
        // Init config
        $this->_config = $config->get()->server->nps;

        // Init database
        $this->_database = $database;

        // Dump event on enabled
        if ($this->_config->event->init->debug->enabled)
        {
            print(
                str_ireplace(
                    [
                        '{time}',
                        '{host}',
                        '{port}'
                    ],
                    [
                        (string) date('c'),
                        (string) $this->_config->host,
                        (string) $this->_config->port
                    ],
                    $this->_config->event->init->debug->template
                ) . PHP_EOL
            );
        }
    }

    public function onOpen(
        \Ratchet\ConnectionInterface $connection
    ) {
        // Debug open event on enabled
        if ($this->_config->event->open->debug->enabled)
        {
            // Print debug from template
            print(
                str_ireplace(
                    [
                        '{time}',
                        '{host}',
                        '{crid}'
                    ],
                    [
                        (string) date('c'),
                        (string) $connection->remoteAddress,
                        (string) $connection->resourceId
                    ],
                    $this->_config->event->open->debug->template
                ) . PHP_EOL
            );
        }
    }

    public function onMessage(
        \Ratchet\ConnectionInterface $connection,
        $request
    ) {
        // Filter request
        $request = trim(
            (string) $request
        );

        // Send response
        $connection->send(
            'test'
        );

        // Debug message event on enabled
        if ($this->_config->event->message->debug->enabled)
        {
            // Print debug from template
            print(
                str_ireplace(
                    [
                        '{time}',
                        '{host}',
                        '{crid}',
                        '{path}'
                    ],
                    [
                        (string) date('c'),
                        (string) $connection->remoteAddress,
                        (string) $connection->resourceId,
                        (string) $request
                    ],
                    $this->_config->event->message->debug->template
                ) . PHP_EOL
            );
        }

        // Disconnect
        $connection->close();
    }

    public function onClose(
        \Ratchet\ConnectionInterface $connection
    ) {
        // Debug close event on enabled
        if ($this->_config->event->close->debug->enabled)
        {
            // Print debug from template
            print(
                str_ireplace(
                    [
                        '{time}',
                        '{host}',
                        '{crid}'
                    ],
                    [
                        (string) date('c'),
                        (string) $connection->remoteAddress,
                        (string) $connection->resourceId
                    ],
                    $this->_config->event->close->debug->template
                ) . PHP_EOL
            );
        }
    }

    public function onError(
        \Ratchet\ConnectionInterface $connection,
        \Exception $exception
    ) {
        // Debug error event on enabled
        if ($this->_config->event->error->debug->enabled)
        {
            // Print debug from template
            print(
                str_ireplace(
                    [
                        '{time}',
                        '{host}',
                        '{crid}',
                        '{info}'
                    ],
                    [
                        (string) date('c'),
                        (string) $connection->remoteAddress,
                        (string) $connection->resourceId,
                        (string) $exception->getMessage()
                    ],
                    $this->_config->event->error->debug->template
                ) . PHP_EOL
            );
        }

        // Disconnect
        $connection->close();
    }
}