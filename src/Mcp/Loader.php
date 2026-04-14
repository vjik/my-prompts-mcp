<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp\Mcp;

use Closure;
use Mcp\Capability\Registry\Loader\LoaderInterface;
use Mcp\Capability\RegistryInterface;
use Mcp\Schema\Content\PromptMessage;
use Mcp\Schema\Content\TextContent;
use Mcp\Schema\Enum\Role;
use Mcp\Schema\Prompt as McpPrompt;
use Mcp\Schema\PromptArgument as McpPromptArgument;
use Mcp\Schema\Request\GetPromptRequest;
use Mcp\Server\RequestContext;
use Vjik\MyPromptsMcp\Prompt;

use function assert;

final readonly class Loader implements LoaderInterface
{
    /**
     * @param array<non-empty-string, Prompt> $prompts
     */
    public function __construct(
        private array $prompts,
    ) {}

    public function load(RegistryInterface $registry): void
    {
        foreach ($this->prompts as $prompt) {
            $mcpArguments = [];
            foreach ($prompt->arguments as $argument) {
                $mcpArguments[] = new McpPromptArgument(
                    name: $argument->name,
                    description: $argument->description,
                    required: $argument->required,
                );
            }

            $registry->registerPrompt(
                new McpPrompt(
                    name: $prompt->name,
                    title: $prompt->title,
                    description: $prompt->description,
                    arguments: $mcpArguments ?: null,
                ),
                $this->createHandler($prompt),
            );
        }
    }

    private function createHandler(Prompt $prompt): Closure
    {
        return static function (RequestContext $context) use ($prompt): PromptMessage {
            $request = $context->getRequest();
            assert($request instanceof GetPromptRequest);

            /**
             * @var array<string, string>
             * @see https://github.com/modelcontextprotocol/php-sdk/pull/285
             */
            $argumentValues = $request->arguments ?? [];

            $text = $prompt->handle($argumentValues);

            return new PromptMessage(
                role: Role::User,
                content: new TextContent($text),
            );
        };
    }
}
