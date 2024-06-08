<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Enum\DatumTypeEnum;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\LogFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CommandTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_command_clean_up_is_successful(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:clean-up');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
    }

    public function test_command_refresh_cached_values_is_successful(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:refresh-cached-values');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
    }

    public function test_command_regenerate_logs_is_successful(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:regenerate-logs');
        $commandTester = new CommandTester($command);

        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->_real();
        ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user])->_real();
        ItemFactory::createOne(['name' => 'Frieren #2', 'collection' => $collection, 'owner' => $user])->_real();
        ItemFactory::createOne(['name' => 'Frieren #3', 'collection' => $collection, 'owner' => $user])->_real();
        LogFactory::truncate();

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        LogFactory::assert()->count(4);
    }

    public function test_command_regenerate_thumbnails_is_successful(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:regenerate-thumbnails');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);

        $user = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->_real();
        $item = ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user]);
        $datum = DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 1, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Japanese title', 'value' => '葬送のフリーレン']);

        $filesystem = new Filesystem();
        $uniqId = uniqid();

        $filesystem->copy(__DIR__ . '/../../assets/fixtures/nyancat.png', "/tmp/{$uniqId}.png");
        $uploadedFile = new UploadedFile("/tmp/{$uniqId}.png", "{$uniqId}.png", null, null, true);
        $item->_real()->setFile($uploadedFile);
        $item->_save();

        $filesystem->copy(__DIR__ . '/../../assets/fixtures/nyancat.png', "/tmp/{$uniqId}.png");
        $uploadedFile = new UploadedFile("/tmp/{$uniqId}.png", "{$uniqId}.png", null, null, true);
        $datum->_real()->setFileImage($uploadedFile);
        $datum->_save();

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
    }
}
