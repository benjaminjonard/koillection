<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Message\RemoveExpiredTemporaryFilesMessage;
use App\MessageHandler\RemoveExpiredTemporaryFilesHandler;
use App\Service\TemporaryFileManager;
use App\Tests\AppTestCase;

class TemporaryFilesTest extends AppTestCase
{
    public function test_expired_temporary_files_are_removed(): void
    {
        // Arrange
        $temporaryFileManager = static::getContainer()->get(TemporaryFileManager::class);
        $expiredPath = $temporaryFileManager->createFilePath();
        touch($expiredPath, time() - 86401);
        $recentPath = $temporaryFileManager->createFilePath();

        // Act
        static::getContainer()->get(RemoveExpiredTemporaryFilesHandler::class)(new RemoveExpiredTemporaryFilesMessage());

        // Assert
        $this->assertFileDoesNotExist($expiredPath);
        $this->assertFileExists($recentPath);
        unlink($recentPath);
    }
}
