<?php

declare(strict_types=1);

namespace App\Tests\App\Admin;

use App\Enum\ConfigurationEnum;
use App\Enum\RoleEnum;
use App\Tests\Factory\ConfigurationFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ConfigurationTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_admin_can_access_configuration(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]])->object();
        $this->client->loginUser($admin);

        // Act
        $this->client->request('GET', '/admin/configuration');

        // Assert
        $this->assertResponseIsSuccessful();
    }

    public function test_admin_can_edit_configuration(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]])->object();
        $this->client->loginUser($admin);

        // Act
        $this->client->request('GET', '/admin/configuration');
        $this->client->submitForm('submit', [
            'configuration[thumbnailsFormat]' => ConfigurationEnum::THUMBNAILS_FORMAT_WEBP,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        ConfigurationFactory::assert()->exists(['label' => ConfigurationEnum::THUMBNAILS_FORMAT, 'value' => ConfigurationEnum::THUMBNAILS_FORMAT_WEBP]);
    }
}
