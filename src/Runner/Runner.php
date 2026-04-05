<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp\Runner;

use Mcp\Server\InitializationOptions;
use Mcp\Server\McpServerException;
use Mcp\Server\Server;
use Mcp\Server\ServerRunner;
use Mcp\Types\GetPromptRequestParams;
use Mcp\Types\GetPromptResult;
use Mcp\Types\ListPromptsResult;
use Mcp\Types\Prompt as McpPrompt;
use Mcp\Types\PromptMessage;
use Mcp\Types\Role;
use Mcp\Types\ServerCapabilities;
use Mcp\Types\ServerPromptsCapability;
use Mcp\Types\TextContent;
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
        $server = new Server($this->name);
        $server->registerHandler('prompts/list', $this->actionPromptsList(...));
        $server->registerHandler('prompts/get', $this->actionPromptsGet(...));

        new ServerRunner(
            $server,
            new InitializationOptions(
                $this->name,
                $this->version,
                new ServerCapabilities(
                    prompts: new ServerPromptsCapability(true),
                ),
            ),
        )->run();
    }

    private function actionPromptsList(): ListPromptsResult
    {
        $mcpPrompts = [];
        foreach ($this->prompts as $prompt) {
            $mcpPrompts[] = new McpPrompt(
                name: $prompt->name,
                description: $prompt->description,
                title: $prompt->title,
            );
        }
        return new ListPromptsResult($mcpPrompts);
    }

    private function actionPromptsGet(GetPromptRequestParams $params): GetPromptResult
    {
        $prompt = $this->prompts[$params->name]
            ?? throw McpServerException::unknownPrompt($params->name);

        return new GetPromptResult(
            [
                new PromptMessage(
                    role: Role::USER,
                    content: new TextContent(
                        text: $prompt->content,
                    ),
                ),
            ],
            $prompt->description,
        );
    }
}
