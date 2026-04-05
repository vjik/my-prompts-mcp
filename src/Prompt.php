<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp;

use Closure;

final readonly class Prompt
{
    public function __construct(
        public string $name,
        public Closure $handler,
    ) {}
}
