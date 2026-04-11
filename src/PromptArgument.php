<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp;

final readonly class PromptArgument
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string|null $description
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public bool $required,
    ) {}
}
