<?php

namespace Yggverse\Pulsar\Controller\Server;

use \Ratchet\MessageComponentInterface;

class Nex implements MessageComponentInterface
{
    private object $_config;

    private \Yggverse\Pulsar\Model\Database $_database;

    public function __construct(
        \Yggverse\Pulsar\Model\Config $config,
        \Yggverse\Pulsar\Model\Database $database
    ) {
        // Init config
        $this->_config = $config->get()->server->nex;

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
        // Format request
        $request = '/' . ltrim(
            trim(
                urldecode(
                    filter_var(
                        $request,
                        FILTER_SANITIZE_URL
                    )
                )
            ), '/'
        );

        // Route request
        switch (true)
        {
            // Try static route settings
            case isset($this->_config->route->{$request}):

                $response = file_get_contents(
                    $this->_config->route->{$request}
                );

            break;

            // Chanel item
            case preg_match(
                    '/\/(?<id>\d+)($|\.gmi)$/i',
                    $request,
                    $attribute
                ) && $channelItem = $this->_database->getChannelItem(
                    $attribute['id']
                ):

                $lines = [];

                if ($channelItem->title)
                {
                    $lines[] = sprintf(
                        '# %s',
                        \Yggverse\Pulsar\Model\Filter::string(
                            $channelItem->title
                        )
                    );
                }

                if ($channelItem->pubTime)
                {
                    $lines[] = date(
                        'c',
                        $channelItem->pubTime
                    ) . PHP_EOL;
                }

                if ($channelItem->description)
                {
                    $lines[] = \Yggverse\Pulsar\Model\Filter::text(
                        $channelItem->description
                    ) . PHP_EOL;
                }

                if ($channelItem->content)
                {
                    $lines[] = \Yggverse\Pulsar\Model\Filter::text(
                        $channelItem->content
                    ) . PHP_EOL;
                }

                if ($channelItem->link)
                {
                    $lines[] = sprintf(
                        '=> %s %s',
                        $channelItem->link,
                        parse_url(
                            $channelItem->link,
                            PHP_URL_HOST
                        )
                    );
                }

                // Get channel info
                if ($channel = $this->_database->getChannel($channelItem->channelId))
                {
                    $lines[] = sprintf(
                        '=> /%s %s',
                        $channel->alias,
                        $channel->title
                    );
                }

                // Build response
                $response = implode(
                    PHP_EOL,
                    $lines
                );

            break;

            // Channel page
            case preg_match(
                    '/^\/(?<alias>.+)$/i',
                    $request,
                    $attribute
                ) && $channel = $this->_database->getChannelByAlias(
                    $attribute['alias']
                ):

                $lines = [];

                if ($channel->title)
                {
                    $lines[] = sprintf(
                        '# %s',
                        \Yggverse\Pulsar\Model\Filter::string(
                            $channel->title
                        )
                    );
                }

                if ($channel->description)
                {
                    $lines[] = $channel->description . PHP_EOL;
                }

                // Get channel items
                foreach ((array) $this->_database->getChannelItems($channel->id, 0, 20) as $channelItem)
                {
                    $lines[] = sprintf(
                        '=> %d.gmi %s',
                        $channelItem->id,
                        \Yggverse\Pulsar\Model\Filter::string(
                            $channelItem->title
                        )
                    );

                    if ($channelItem->description)
                    {
                        $lines[] = \Yggverse\Pulsar\Model\Filter::text(
                            $channelItem->description
                        ) . PHP_EOL;
                    }

                    if ($channelItem->content)
                    {
                        $lines[] = \Yggverse\Pulsar\Model\Filter::text(
                            $channelItem->content
                        ) . PHP_EOL;
                    }
                }

                // Build response
                $response = implode(
                    PHP_EOL,
                    $lines
                );

            break;

            // Main
            // Not found
            default:

                // Create site map
                $lines = [];

                // Get channels
                foreach ((array) $this->_database->getChannels() as $channel)
                {
                    $lines[] = sprintf(
                        '=> /%s %s',
                        $channel->alias,
                        $channel->title
                    );
                }

                // Build response
                $response = implode(
                    PHP_EOL,
                    $lines
                );
        }

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

        // Send response
        $connection->send(
            $response
        );

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