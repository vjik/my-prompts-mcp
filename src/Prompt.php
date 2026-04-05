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
     * @param non-empty-string|null $description
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public string $content,
    ) {}
}
