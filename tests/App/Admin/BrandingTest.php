<?php

declare(strict_types=1);

namespace App\Tests\App\Admin;

use App\Entity\Configuration;
use App\Enum\ConfigurationEnum;
use App\Enum\RoleEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\ConfigurationFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class BrandingTest extends AppTestCase
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

    public function test_default_app_name_is_used_without_configuration(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/collections');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Koillection', $crawler->filter('.header-logo span')->text());
        $this->assertSame('Koillection', $crawler->filter('meta[name="apple-mobile-web-app-title"]')->attr('content'));
    }

    public function test_admin_can_set_app_name(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]]);
        $this->client->loginUser($admin);

        // Act
        $this->client->request(Request::METHOD_GET, '/admin/configuration');
        $crawler = $this->client->submitForm('submit', [
            'configuration_admin[appName][value]' => 'My collection',
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        ConfigurationFactory::assert()->exists(['label' => ConfigurationEnum::APP_NAME, 'value' => 'My collection']);
        $this->assertSame('My collection', $crawler->filter('.header-logo span')->text());
        $this->assertSame('My collection', $crawler->filter('meta[name="apple-mobile-web-app-title"]')->attr('content'));
    }

    public function test_login_page_uses_app_name(): void
    {
        // Arrange
        $this->setConfigurationValue(ConfigurationEnum::APP_NAME, 'My collection');

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Welcome to My collection', $crawler->filter('h1')->text());
    }

    public function test_manifest_is_public_and_uses_app_name(): void
    {
        // Arrange
        $this->setConfigurationValue(ConfigurationEnum::APP_NAME, 'My collection');

        // Act
        $this->client->request(Request::METHOD_GET, '/manifest.json');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/manifest+json');
        $manifest = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertSame('My collection', $manifest['name']);
        $this->assertSame('My collection', $manifest['short_name']);
        $this->assertSame('/icons/square/48.png', $manifest['icons'][0]['src']);
    }

    public function test_admin_can_upload_app_logo(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]]);
        $this->client->loginUser($admin);

        // Act
        $this->client->request(Request::METHOD_GET, '/admin/configuration');
        $crawler = $this->client->submitForm('submit', [
            'configuration_admin[appLogoFile]' => $this->createSquareImage(600),
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $paths = json_decode((string) ConfigurationFactory::repository()->findOneBy(['label' => ConfigurationEnum::APP_LOGO])->getValue(), true);
        $this->assertIsArray($paths);
        foreach (ConfigurationEnum::APP_LOGO_SIZES as $size) {
            $this->assertArrayHasKey((string) $size, $paths);
            $this->assertFileExists($paths[(string) $size]);
        }

        $this->assertStringEndsWith('_96.png', (string) $crawler->filter('.header-logo img')->attr('src'));
        $this->assertStringEndsWith('_16.png', (string) $crawler->filter('link[rel="icon"][sizes="16x16"]')->attr('href'));

        $this->client->request(Request::METHOD_GET, '/manifest.json');
        $manifest = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertCount(9, $manifest['icons']);
        $this->assertSame('512x512', end($manifest['icons'])['sizes']);
        $this->assertStringStartsWith(\dirname($paths['original']), end($manifest['icons'])['src']);
    }

    public function test_app_logo_must_be_square(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]]);
        $this->client->loginUser($admin);

        // Act
        $this->client->request(Request::METHOD_GET, '/admin/configuration');
        $this->client->submitForm('submit', [
            'configuration_admin[appLogoFile]' => $this->createFile('png'),
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        ConfigurationFactory::assert()->exists(['label' => ConfigurationEnum::APP_LOGO, 'value' => null]);
    }

    public function test_app_logo_must_be_at_least_512_pixels(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]]);
        $this->client->loginUser($admin);

        // Act
        $this->client->request(Request::METHOD_GET, '/admin/configuration');
        $this->client->submitForm('submit', [
            'configuration_admin[appLogoFile]' => $this->createSquareImage(256),
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        ConfigurationFactory::assert()->exists(['label' => ConfigurationEnum::APP_LOGO, 'value' => null]);
    }

    public function test_admin_can_delete_app_logo(): void
    {
        // Arrange
        $admin = UserFactory::createOne(['roles' => [RoleEnum::ROLE_ADMIN]]);
        $this->client->loginUser($admin);
        $this->client->request(Request::METHOD_GET, '/admin/configuration');
        $this->client->submitForm('submit', [
            'configuration_admin[appLogoFile]' => $this->createSquareImage(600),
        ]);
        $paths = json_decode((string) ConfigurationFactory::repository()->findOneBy(['label' => ConfigurationEnum::APP_LOGO])->getValue(), true);

        // Act
        $crawler = $this->client->submitForm('submit', [
            'configuration_admin[deleteAppLogo]' => true,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        ConfigurationFactory::assert()->exists(['label' => ConfigurationEnum::APP_LOGO, 'value' => null]);
        $this->assertFileDoesNotExist($paths['original']);
        $this->assertStringContainsString('logo.svg', (string) $crawler->filter('.header-logo img')->attr('src'));
    }

    private function setConfigurationValue(string $label, ?string $value): void
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->getRepository(Configuration::class)->findOneBy(['label' => $label])->setValue($value);
        $entityManager->flush();
    }

    private function createSquareImage(int $size): UploadedFile
    {
        $path = '/tmp/' . uniqid() . '.png';
        $image = imagecreatetruecolor($size, $size);
        imagefill($image, 0, 0, imagecolorallocate($image, 200, 40, 40));
        imagefilledellipse($image, intdiv($size, 2), intdiv($size, 2), intdiv($size, 2), intdiv($size, 2), imagecolorallocate($image, 250, 220, 0));
        imagepng($image, $path);

        return new UploadedFile($path, 'logo.png', 'image/png', test: true);
    }
}
