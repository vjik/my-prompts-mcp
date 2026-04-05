<?php

declare(strict_types=1);

use Mcp\Server;
use Mcp\Server\Transport\StdioTransport;
use Vjik\MyPromptsMcp\Config;

require __DIR__ . '/../vendor/autoload.php';

$config = new Config();
$builder = Server::builder();

$provider = new Vjik\MyPromptsMcp\PromptsProvider($config->path);
foreach ($provider->getPrompts() as $prompt) {
    $builder->addPrompt($prompt->handler, $prompt->name);
}

$server = $builder
    ->setServerInfo('My Prompts MCP', '0.1')
    ->build()
    ->run(new StdioTransport());
