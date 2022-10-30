<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\LogFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
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

        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Frieren #2', 'collection' => $collection, 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Frieren #3', 'collection' => $collection, 'owner' => $user])->object();

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

        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Frieren #2', 'collection' => $collection, 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Frieren #3', 'collection' => $collection, 'owner' => $user])->object();

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
    }
}
