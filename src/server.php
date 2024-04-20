<?php

// Load dependencies
require_once __DIR__ .
             DIRECTORY_SEPARATOR . '..'.
             DIRECTORY_SEPARATOR . 'vendor' .
             DIRECTORY_SEPARATOR . 'autoload.php';

// Init required arguments
if (empty($argv[1]))
{
    throw new \Exception(
        _('Configured hostname required as argument')
    );
}

// Init server path
define(
    'PULSAR_SERVER_DIRECTORY',
    rtrim(
        str_starts_with($argv[1], DIRECTORY_SEPARATOR) ? $argv[1] :
        realpath(
            __DIR__  .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . $argv[1]
        ),
        DIRECTORY_SEPARATOR
    ) . DIRECTORY_SEPARATOR
);

// Init server config
if (!file_exists(PULSAR_SERVER_DIRECTORY . 'host.json'))
{
    throw new \Exception(
        _('Host not configured')
    );
}

$config = json_decode(
    file_get_contents(
        PULSAR_SERVER_DIRECTORY . 'host.json'
    )
);

// Init server certificate
define(
    'PULSAR_SERVER_CERT',
    str_starts_with(
        $config->cert,
        DIRECTORY_SEPARATOR
    ) ? $config->cert : PULSAR_SERVER_DIRECTORY . $config->cert
);

if (!file_exists(PULSAR_SERVER_CERT))
{
    throw new \Exception(
        _('Certificate file not found')
    );
}

// Init server key
define(
    'PULSAR_SERVER_KEY',
    str_starts_with(
        $config->key,
        DIRECTORY_SEPARATOR
    ) ? $config->key : PULSAR_SERVER_DIRECTORY . $config->key
);

if (!file_exists(PULSAR_SERVER_KEY))
{
    throw new \Exception(
        _('Key file not found')
    );
}

// Init data directory
define(
    'PULSAR_SERVER_DATA_DIRECTORY',
    str_starts_with(
        $config->data->directory,
        DIRECTORY_SEPARATOR
    ) ? $config->data->directory : PULSAR_SERVER_DIRECTORY . $config->data->directory
);

if (!is_dir(PULSAR_SERVER_DATA_DIRECTORY))
{
    throw new \Exception(
        _('Data directory not found')
    );
}

// Init server
$server = new \Yggverse\TitanII\Server();

$server->setCert(
    PULSAR_SERVER_CERT
);

$server->setKey(
    PULSAR_SERVER_KEY
);

$server->setHandler(
    function (\Yggverse\TitanII\Request $request): \Yggverse\TitanII\Response
    {
        global $config;

        $response = new \Yggverse\TitanII\Response;

        // Filter path request
        $path = trim(
            preg_replace(
                [
                    '/\/[\.]+\//',
                    '/\/[\/]+\//',
                ],
                '/',
                $request->getPath()
            )
        );

        if ($path != $request->getPath() || in_array($path, ['', null, false]))
        {
            $response->setCode(
                30
            );

            $response->setMeta(
                sprintf(
                    'gemini://%s%s/%s',
                    $config->host,
                    $config->port == 1965 ? null : ':' . $config->port,
                    trim(
                        (string) $path,
                        '/'
                    )
                )
            );

            return $response;
        }

        // Directory request
        if (is_dir(PULSAR_SERVER_DATA_DIRECTORY . $path))
        {
            // Try index
            if (file_exists(PULSAR_SERVER_DATA_DIRECTORY . $path . $config->data->index))
            {
                $response->setContent(
                    file_get_contents(
                        PULSAR_SERVER_DATA_DIRECTORY . $path . $config->data->index
                    )
                );

                $response->setCode(
                    20
                );

                $response->setMeta(
                    'text/gemini; charset=utf-8'
                );

                return $response;
            }

            // Build listing
            if ($config->data->listing)
            {
                $response->setCode(
                    20
                );

                $response->setMeta(
                    'text/gemini; charset=utf-8'
                );

                $links = [];

                foreach ((array) scandir(PULSAR_SERVER_DATA_DIRECTORY . $path) as $link)
                {
                    if (!str_starts_with($link, '.'))
                    {
                        if (is_dir(PULSAR_SERVER_DATA_DIRECTORY . $path . $link))
                        {
                            $links[] = sprintf(
                                '=> %s/',
                                $link
                            );
                        }

                        else
                        {
                            $links[] = sprintf(
                                '=> %s',
                                $link
                            );
                        }
                    }
                }

                $response->setContent(
                    implode(
                        PHP_EOL,
                        $links
                    )
                );

                return $response;
            }
        }

        // File request
        if (file_exists(PULSAR_SERVER_DATA_DIRECTORY . $path))
        {
            $response->setCode(
                20
            );

            $response->setMeta(
                'text/gemini; charset=utf-8'
            );

            $response->setContent(
                file_get_contents(
                    PULSAR_SERVER_DATA_DIRECTORY . $path
                )
            );

            return $response;
        }

        // Noting found
        $response->setCode(
            51
        );

        return $response;
    }
);

// Start server
echo sprintf(
    _('Server started on %s:%d'),
    $config->host,
    $config->port
);

$server->start(
    $config->host,
    $config->port
);