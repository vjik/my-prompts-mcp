<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp;

use Spatie\YamlFrontMatter\YamlFrontMatter;

use function is_scalar;

final class PromptsProvider
{
    /**
     * @var array<non-empty-string, Prompt>|null
     */
    private ?array $prompts = null;

    public function __construct(
        private readonly string $path,
    ) {}

    /**
     * @return Prompt[]
     */
    public function getPrompts(): array
    {
        if ($this->prompts !== null) {
            return $this->prompts;
        }

        $this->prompts = [];
        foreach ($this->getFiles() as $file) {
            $document = YamlFrontMatter::parseFile($file);
            $name = $this->parseNonEmptyStringOrNull($document->matter('name'))
                ?? $this->parseNonEmptyStringOrNull(pathinfo($file, PATHINFO_FILENAME))
                ?? null;
            if ($name !== null) {
                $this->prompts[$name] = new Prompt(
                    name: $name,
                    description: $this->parseNonEmptyStringOrNull($document->matter('description')),
                    content: $document->body(),
                );
            }
        }
        return $this->prompts;
    }

    /**
     * @return list<string>
     */
    private function getFiles(): array
    {
        return glob($this->path . '/*.md') ?: [];
    }

    /**
     * @param mixed $value
     * @return non-empty-string|null
     */
    private function parseNonEmptyStringOrNull(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }
        $value = (string) $value;
        return $value === '' ? null : $value;
    }
}
