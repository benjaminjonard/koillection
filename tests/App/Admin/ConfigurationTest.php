<?php

declare(strict_types=1);

namespace App\Tests\App\Admin;

use App\Enum\ConfigurationEnum;
use App\Enum\RoleEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\ConfigurationFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ConfigurationTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_admin_can_access_configuration(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]]);
        $this->client->loginUser($admin);

        // Act
        $this->client->request(Request::METHOD_GET, '/admin/configuration');

        // Assert
        $this->assertResponseIsSuccessful();
    }

    public function test_admin_can_edit_configuration(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]]);
        $this->client->loginUser($admin);

        // Act
        $this->client->request(Request::METHOD_GET, '/admin/configuration');
        $this->client->submitForm('submit', [
            'configuration_admin[thumbnailsFormat][value]' => ConfigurationEnum::THUMBNAILS_FORMAT_WEBP,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        ConfigurationFactory::assert()->exists(['label' => ConfigurationEnum::THUMBNAILS_FORMAT, 'value' => ConfigurationEnum::THUMBNAILS_FORMAT_WEBP]);
    }

    public function test_admin_can_disable_features_on_instance(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]]);
        $this->client->loginUser($admin);

        // Act
        $this->client->request(Request::METHOD_GET, '/admin/configuration');
        $this->client->submitForm('submit', [
            'configuration_admin[enableScraping][value]' => false,
            'configuration_admin[enableImport][value]' => false,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        ConfigurationFactory::assert()->exists(['label' => ConfigurationEnum::ENABLE_SCRAPING, 'value' => '0']);
        ConfigurationFactory::assert()->exists(['label' => ConfigurationEnum::ENABLE_IMPORT, 'value' => '0']);
    }
}
