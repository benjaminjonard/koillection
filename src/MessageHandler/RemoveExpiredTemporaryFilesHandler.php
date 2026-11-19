<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\RemoveExpiredTemporaryFilesMessage;
use App\Service\TemporaryFileManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RemoveExpiredTemporaryFilesHandler
{
    private const int MAX_AGE = 86400;

    public function __construct(
        private TemporaryFileManager $temporaryFileManager,
    ) {
    }

    public function __invoke(RemoveExpiredTemporaryFilesMessage $message): void
    {
        $this->temporaryFileManager->removeExpiredFiles(self::MAX_AGE);
    }
}
