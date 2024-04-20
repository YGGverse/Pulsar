<?php

// Prevent multi-thread execution
$semaphore = sem_get(
    crc32(
        __DIR__
    ), 1
);

if (false === sem_acquire($semaphore, true))
{
    exit;
}

// Init config
$config = json_decode(
    file_get_contents(
        __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'crawler.json'
    )
);

// Update feeds
foreach ($config->feed as $feed)
{
    // Init feed location
    $filename = str_starts_with(
        $feed->target,
        DIRECTORY_SEPARATOR
    ) ? $feed->target : __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $feed->target;

    // Init destination storage
    @mkdir(
        dirname(
            $filename
        ),
        0755,
        true
    );

    // Get feed data
    if (!$channel = simplexml_load_file($feed->source)->channel)
    {
        continue;
    }

    // Update title
    if (!empty($channel->title))
    {
        $title = trim(
            strip_tags(
                html_entity_decode(
                    $channel->title
                )
            )
        );
    }

    else
    {
        $title = parse_url(
            $feed->source,
            PHP_URL_HOST
        );
    }

    file_put_contents(
        $filename,
        sprintf(
            '# %s',
            $title
        ) . PHP_EOL
    );

    // Append description
    if (!empty($channel->description))
    {
        file_put_contents(
            $filename,
            PHP_EOL . trim(
                strip_tags(
                    html_entity_decode(
                        $channel->description
                    )
                )
            ) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }

    // Append items
    $i = 1; foreach ($channel->item as $item)
    {
        // Apply items limit
        if ($i > $feed->item->limit)
        {
            break;
        }

        // Format item
        file_put_contents(
            $filename,
            PHP_EOL . trim(
                preg_replace(
                    '/[\s]{3,}/ui',
                    PHP_EOL . PHP_EOL,
                    str_replace(
                        [
                            '{nl}',
                            '{link}',
                            '{guid}',
                            '{pubDate}',
                            '{title}',
                            '{description}'
                        ],
                        [
                            PHP_EOL,
                            !empty($item->link)        ? trim($item->link) : '',
                            !empty($item->guid)        ? trim($item->guid) : '',
                            !empty($item->pubDate)     ? trim($item->pubDate) : '',
                            !empty($item->title)       ? trim(strip_tags(html_entity_decode($item->title))) : '',
                            !empty($item->description) ? trim(strip_tags(html_entity_decode($item->description))) : ''
                        ],
                        $feed->item->template
                    ) . PHP_EOL
                )
            ) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );

        $i++;
    }
}