<?php

declare(strict_types=1);

use Mcp\Server;
use Mcp\Server\Transport\StdioTransport;

require __DIR__ . '/../vendor/autoload.php';

$server = Server::builder()
    ->setServerInfo('My Prompts MCP', '0.1')
    ->build();

$transport = new StdioTransport();
$server->run($transport);
