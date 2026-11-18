<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\RemoveExpiredImportFilesMessage;
use App\Service\ImportStorage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RemoveExpiredImportFilesHandler
{
    private const int MAX_AGE = 86400;

    public function __invoke(RemoveExpiredImportFilesMessage $message): void
    {
        foreach (glob(sys_get_temp_dir() . '/' . ImportStorage::FILE_PREFIX . '*') ?: [] as $path) {
            if (is_file($path) && filemtime($path) < time() - self::MAX_AGE) {
                unlink($path);
            }
        }
    }
}
