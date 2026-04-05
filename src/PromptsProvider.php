<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp;

final readonly class PromptsProvider
{
    public function __construct(
        private string $path,
    ) {}

    /**
     * @return list<Prompt>
     */
    public function getPrompts(): array
    {
        $prompts = [];

        foreach ($this->getFiles() as $file) {
            $prompts[] = new Prompt(
                name: pathinfo($file, PATHINFO_FILENAME),
                handler: static function () use ($file): array {
                    return ['user' => file_get_contents($file)];
                },
            );
        }

        return $prompts;
    }

    /**
     * @return list<string>
     */
    private function getFiles(): array
    {
        return glob($this->path . '/*.md') ?: [];
    }
}
