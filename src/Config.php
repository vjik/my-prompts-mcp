<?php

declare(strict_types=1);

namespace Vjik\MyPromptsMcp;

use function file_exists;
use function is_array;
use function is_dir;

final readonly class Config
{
    public string $path;

    public function __construct()
    {
        $options = getopt('p:', ['path:']);
        $path = $options['path'] ?? $options['p'] ?? null;

        if ($path === null) {
            fwrite(STDERR, "Usage: php run.php --path=/path/to/dir\n");
            exit(1);
        }

        if (is_array($path)) {
            fwrite(STDERR, "Error. --path flag must be specified only once\n");
            exit(1);
        }

        if ($path === false) {
            fwrite(STDERR, "Error. --path value is missing\n");
            exit(1);
        }

        if (!file_exists($path)) {
            fwrite(STDERR, "Error. The path does not exist: $path\n");
            exit(1);
        }

        if (!is_dir($path)) {
            fwrite(STDERR, "Error. The path is not a directory: $path\n");
            exit(1);
        }

        $this->path = $path;
    }
}
