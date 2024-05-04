<?php

// Load dependencies
require_once __DIR__ .
             DIRECTORY_SEPARATOR . '..'.
             DIRECTORY_SEPARATOR . 'vendor' .
             DIRECTORY_SEPARATOR . 'autoload.php';

// Init environment
$environment = new \Yggverse\Pulsar\Model\Environment(
    $argv
);

// Init config
$config = new \Yggverse\Pulsar\Model\Config(
    $environment->get('config')
);

// Init database
$database = new \Yggverse\Pulsar\Model\Database(
    $config->get()->database->location,
    $config->get()->database->username,
    $config->get()->database->password
);

// Start server
switch ($environment->get('protocol'))
{
    case 'nex':

        $server = \Ratchet\Server\IoServer::factory(
            new \Yggverse\Pulsar\Controller\Server\Nex(
                $config,
                $database
            ),
            $config->get()->server->nex->port,
            $config->get()->server->nex->host
        );

        $server->run();

    break;

    default:

        throw new \Exception;
}