<?php

declare(strict_types=1);

namespace App\Tests\App\Album;

use App\Enum\VisibilityEnum;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AlbumTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_get_album_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        AlbumFactory::createMany(3, ['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/albums');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Albums', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
    }

    public function test_can_get_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $album = AlbumFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/albums/'.$album->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($album->getTitle(), $crawler->filter('h1')->text());
    }

    public function test_can_post_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/albums/add');
        $crawler = $this->client->submitForm('Submit', [
            'album[title]' => 'Home album',
            'album[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Home album', $crawler->filter('h1')->text());
    }

    public function test_can_edit_album(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $album = AlbumFactory::createOne(['owner' => $user]);

        // Act
        $this->client->request('GET', '/albums/'.$album->getId().'/edit');
        $crawler = $this->client->submitForm('Submit', [
            'album[title]' => 'Other album',
            'album[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertSame('Other album', $crawler->filter('h1')->text());
    }
}
