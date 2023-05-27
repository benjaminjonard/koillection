<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Tests\AppTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProfileTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_edit_profile(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../assets/fixtures/nyancat.png', "/tmp/{$uniqId}.png");

        $user = UserFactory::createOne(['avatar' => "/tmp/{$uniqId}.png"])->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/profile');
        $this->client->submitForm('Submit', [
            'profile[username]' => 'Stitch',
            'profile[email]' => 'stitch@koillection.com'
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        UserFactory::assert()->exists(['username' => 'Stitch', 'email' => 'stitch@koillection.com']);
        $this->assertFileExists("/tmp/{$uniqId}.png");
    }

    public function test_can_delete_avatar_image(): void
    {
        // Arrange
        $filesystem = new Filesystem();
        $uniqId = uniqid();
        $filesystem->copy(__DIR__.'/../../assets/fixtures/nyancat.png', "/tmp/{$uniqId}.png");
        $user = UserFactory::createOne(['username' => 'Stitch', 'avatar' => "/tmp/{$uniqId}.png"])->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/profile');
        $crawler = $this->client->submitForm('Submit', [
            'profile[deleteAvatar]' => true,
        ]);

        // Assert
        $this->assertSame('S', $crawler->filter('.user-avatar')->text());
        $this->assertFileDoesNotExist("/tmp/{$uniqId}.png");
    }
}
