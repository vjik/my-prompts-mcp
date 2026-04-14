<?php

declare(strict_types=1);

use Vjik\MyPromptsMcp\Config;
use Vjik\MyPromptsMcp\PromptsProvider;
use Vjik\MyPromptsMcp\Mcp\Runner;

require __DIR__ . '/../vendor/autoload.php';

$config = new Config();
$prompts = new PromptsProvider($config->path)->getPrompts();
new Runner(
    'My Prompts MCP',
    '0.1',
    $prompts,
)
    ->run();
