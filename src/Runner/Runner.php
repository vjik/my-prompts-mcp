<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp\Runner;

use Mcp\Server\InitializationOptions;
use Mcp\Server\McpServerException;
use Mcp\Server\Server;
use Mcp\Server\ServerRunner;
use Mcp\Shared\ErrorData;
use Mcp\Shared\McpError;
use Mcp\Types\GetPromptRequestParams;
use Mcp\Types\GetPromptResult;
use Mcp\Types\ListPromptsResult;
use Mcp\Types\ListToolsResult;
use Mcp\Types\Prompt as McpPrompt;
use Mcp\Types\PromptArgument as McpPromptArgument;
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
        
        // Stub for clients that ignore server capabilities and request tools anyway
        $server->registerHandler('tools/list', static fn() => new ListToolsResult([]));

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
            $mcpArguments = [];
            foreach ($prompt->arguments as $argument) {
                $mcpArguments[] = new McpPromptArgument(
                    name: $argument->name,
                    description: $argument->description,
                    required: $argument->required,
                );
            }
            $mcpPrompts[] = new McpPrompt(
                name: $prompt->name,
                description: $prompt->description,
                arguments: $mcpArguments,
                title: $prompt->title,
            );
        }
        return new ListPromptsResult($mcpPrompts);
    }

    private function actionPromptsGet(GetPromptRequestParams $params): GetPromptResult
    {
        $prompt = $this->prompts[$params->name]
            ?? throw McpServerException::unknownPrompt($params->name);

        $argumentValues = $params->arguments?->getExtraFields() ?? [];
        $pairs = [];
        foreach ($prompt->arguments as $argument) {
            if ($argument->required && !isset($argumentValues[$argument->name])) {
                throw new McpError(
                    new ErrorData(-32602, "Missing required argument: {$argument->name}"),
                );
            }
            $pairs['{{' . $argument->name . '}}'] = $argumentValues[$argument->name] ?? '';
        }
        $content = $pairs === []
            ? $prompt->content
            : strtr($prompt->content, $pairs);

        return new GetPromptResult(
            [
                new PromptMessage(
                    role: Role::USER,
                    content: new TextContent(
                        text: $content,
                    ),
                ),
            ],
            $prompt->description,
        );
    }
}
