<?php

// Prevent multi-thread execution
$semaphore = sem_get(
    crc32(
        __DIR__
    ), 1
);

if (false === sem_acquire($semaphore, true)) exit;

// Load dependencies
require_once __DIR__ .
             DIRECTORY_SEPARATOR . '..'.
             DIRECTORY_SEPARATOR . 'vendor' .
             DIRECTORY_SEPARATOR . 'autoload.php';

// Init profile argument
if (empty($argv[1])) throw new \Exception;

// Init config
$config = new \Yggverse\Pulsar\Model\Config(
    $argv[1]
);

$config = $config->get(); // registry only

// Init database
$database = new \Yggverse\Pulsar\Model\Database(
    $config->database->location,
    $config->database->username,
    $config->database->password
);

// Begin channels crawl
foreach ($config->crawler->channel as $channel)
{
    // Check channel enabled
    if (!$channel->enabled)
    {
        if ($config->crawler->debug->info)
        {
            printf(
                _('[%s] [info] skip disabled channel "%s"') . PHP_EOL,
                date('c'),
                $channel->source
            ) . PHP_EOL;
        }

        continue;
    }

    // Get channel data
    if (!$remoteChannel = simplexml_load_file($channel->source)->channel)
    {
        if ($config->crawler->debug->warning)
        {
            printf(
                _('[%s] [warning] channel "%s" not accessible') . PHP_EOL,
                date('c'),
                $channel->source
            ) . PHP_EOL;
        }

        continue;
    }

    // Init channel
    if (!$channelId = $database->getChannelIdBySource($channel->source))
    {
        // Create new one if not exists
        $channelId = $database->addChannel(
            $channel->source,
            isset($remoteChannel->link) ? (string) $remoteChannel->link : null,
            isset($remoteChannel->title) ? (string) $remoteChannel->title : null,
            isset($remoteChannel->description) ? (string) $remoteChannel->description : null
        );

        if ($config->crawler->debug->info)
        {
            printf(
                _('[%s] [info] channel "%s" registered as #%d') . PHP_EOL,
                date('c'),
                $channel->source,
                $channelId
            ) . PHP_EOL;
        }
    }

    // Process items
    if (!empty($remoteChannel->item))
    {
        foreach ($remoteChannel->item as $remoteChannelItem)
        {
            // Prepare link
            $link = null;

            if ($channel->item->link->enabled)
            {
                if (isset($remoteChannelItem->link))
                {
                    $link = (string) $remoteChannelItem->link;
                }

                else
                {
                    if ($config->crawler->debug->info)
                    {
                        printf(
                            _('[%s] [info] item link enabled but not defined in channel #%d') . PHP_EOL,
                            date('c'),
                            $channelId
                        ) . PHP_EOL;
                    }
                }

                if ($channel->item->link->required && !$link)
                {
                    if ($config->crawler->debug->warning)
                    {
                        printf(
                            _('[%s] [warning] could not get item link for channel #%d') . PHP_EOL,
                            date('c'),
                            $channelId
                        ) . PHP_EOL;
                    }

                    continue;
                }
            }

            // Prepare guid or define it from link
            $guid = null;

            if (isset($remoteChannelItem->guid))
            {
                $guid = (string) $remoteChannelItem->guid;
            }

            else
            {
                $guid = $link;

                if ($config->crawler->debug->warning)
                {
                    printf(
                        _('[%s] [warning] item guid defined as link in channel #%d') . PHP_EOL,
                        date('c'),
                        $channelId
                    ) . PHP_EOL;
                }
            }

            // Prepare title
            $title = null;

            if ($channel->item->title->enabled)
            {
                if (isset($remoteChannelItem->title))
                {
                    $title = (string) $remoteChannelItem->title;
                }

                else
                {
                    if ($config->crawler->debug->info)
                    {
                        printf(
                            _('[%s] [info] item title enabled but not defined in channel #%d') . PHP_EOL,
                            date('c'),
                            $channelId
                        ) . PHP_EOL;
                    }
                }

                if ($channel->item->title->required && !$title)
                {
                    if ($config->crawler->debug->warning)
                    {
                        printf(
                            _('[%s] [warning] could not get item title in channel #%d') . PHP_EOL,
                            date('c'),
                            $channelId
                        ) . PHP_EOL;
                    }

                    continue;
                }
            }

            // Prepare description
            $description = null;

            if ($channel->item->description->enabled)
            {
                if (isset($remoteChannelItem->description))
                {
                    $description = (string) $remoteChannelItem->description;
                }

                else
                {
                    if ($config->crawler->debug->info)
                    {
                        printf(
                            _('[%s] [info] item description enabled but not defined in channel #%d') . PHP_EOL,
                            date('c'),
                            $channelId
                        ) . PHP_EOL;
                    }
                }

                if ($channel->item->description->required && !$description)
                {
                    if ($config->crawler->debug->warning)
                    {
                        printf(
                            _('[%s] [warning] could not get item description in channel #%d') . PHP_EOL,
                            date('c'),
                            $channelId
                        ) . PHP_EOL;
                    }

                    continue;
                }
            }

            // Prepare content
            $content = null;

            if ($channel->item->content->enabled)
            {
                if ($_content = $remoteChannelItem->children('content', true))
                {
                    if (isset($_content->encoded))
                    {
                        $content = (string) $_content->encoded;
                    }
                }

                if (!$content && $config->crawler->debug->info)
                {
                    printf(
                        _('[%s] [info] item content enabled but not defined in channel #%d') . PHP_EOL,
                        date('c'),
                        $channelId
                    ) . PHP_EOL;
                }

                if ($channel->item->content->required && !$content)
                {
                    if ($config->crawler->debug->warning)
                    {
                        printf(
                            _('[%s] [warning] could not get item content in channel #%d') . PHP_EOL,
                            date('c'),
                            $channelId
                        ) . PHP_EOL;
                    }

                    continue;
                }
            }

            // Prepare pubDate
            $pubTime = null;

            if ($channel->item->pubDate->enabled)
            {
                if (isset($remoteChannelItem->pubDate))
                {
                    if ($_pubTime = strtotime((string) $remoteChannelItem->pubDate))
                    {
                        $pubTime = $_pubTime;
                    }

                    else
                    {
                        if ($config->crawler->debug->warning)
                        {
                            printf(
                                _('[%s] [info] could not convert item pubDate to pubTime in channel #%d') . PHP_EOL,
                                date('c'),
                                $channelId
                            ) . PHP_EOL;
                        }
                    }
                }

                else
                {
                    if ($config->crawler->debug->info)
                    {
                        printf(
                            _('[%s] [info] item pubDate enabled but not defined in channel #%d') . PHP_EOL,
                            date('c'),
                            $channelId
                        ) . PHP_EOL;
                    }
                }

                if ($channel->item->pubDate->required && !$pubTime)
                {
                    if ($config->crawler->debug->warning)
                    {
                        printf(
                            _('[%s] [warning] could not get item pubDate in channel #%d') . PHP_EOL,
                            date('c'),
                            $channelId
                        ) . PHP_EOL;
                    }

                    continue;
                }
            }

            // Check item not registered yet
            if (!$database->isChannelItemExist($channelId, $guid))
            {
                // Create new one if not exists
                $channelItemId = $database->addChannelItem(
                    $channelId,
                    $guid,
                    $link,
                    $title,
                    $description,
                    $content,
                    $pubTime
                );

                if ($channelItemId)
                {
                    if ($config->crawler->debug->info)
                    {
                        printf(
                            _('[%s] [info] registered new item #%d for channel #%d') . PHP_EOL,
                            date('c'),
                            $channelItemId,
                            $channelId
                        ) . PHP_EOL;
                    }
                }
            }
        }
    }
}