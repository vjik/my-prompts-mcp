<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp;

/**
 * @see https://modelcontextprotocol.io/specification/2025-11-25/server/prompts#prompt
 */
final readonly class Prompt
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string|null $title
     * @param non-empty-string|null $description
     * @param array<non-empty-string, PromptArgument> $arguments
     */
    public function __construct(
        public string $name,
        public ?string $title,
        public ?string $description,
        public string $content,
        public array $arguments,
    ) {}

    /**
     * @param array<string, string> $arguments
     */
    public function handle(array $arguments): string
    {
        $pairs = [];
        foreach ($this->arguments as $argument) {
            $pairs['{{' . $argument->name . '}}'] = $arguments[$argument->name] ?? '';
        }
        return $pairs === [] ? $this->content : strtr($this->content, $pairs);
    }
}
