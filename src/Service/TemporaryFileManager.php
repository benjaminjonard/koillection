<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TemporaryFileManager
{
    private const string FILE_PREFIX = 'koillection_';

    public function __construct(
        #[Autowire('%kernel.project_dir%/var/tmp')] private readonly string $directory
    ) {
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function createFilePath(): string
    {
        if (!is_dir($this->directory)) {
            @mkdir($this->directory, 0775, true);
        }

        return tempnam($this->directory, self::FILE_PREFIX);
    }

    public function removeExpiredFiles(int $maxAge): void
    {
        foreach (glob($this->directory . '/*') ?: [] as $path) {
            if (is_file($path) && filemtime($path) < time() - $maxAge) {
                unlink($path);
            }
        }
    }
}
