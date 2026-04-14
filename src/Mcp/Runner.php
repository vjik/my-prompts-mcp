<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp\Mcp;

use Mcp\Server;
use Mcp\Server\Transport\StdioTransport;
use Vjik\MyPromptsMcp\Prompt;

final readonly class Runner
{
    /**
     * @param array<non-empty-string, Prompt> $prompts
     */
    public function __construct(
        private string $name,
        private string $version,
        private array $prompts,
    ) {}

    public function run(): void
    {
        $server = Server::builder()
            ->setServerInfo($this->name, $this->version)
            ->addLoader(new Loader($this->prompts))
            ->build();

        $transport = new StdioTransport();
        $server->run($transport);
    }
}
