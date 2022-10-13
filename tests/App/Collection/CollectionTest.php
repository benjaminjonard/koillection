<?php

declare(strict_types=1);

namespace App\Tests\App\Collection;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Enum\VisibilityEnum;
use App\Factory\CollectionFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class CollectionTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_get_collection_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        CollectionFactory::createMany(3, ['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/collections');

        //Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Collections', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
    }

    public function test_can_get_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/collections/' . $collection->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($collection->getTitle(), $crawler->filter('h1')->text());
    }

    public function test_can_post_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/collections/add');

        $crawler = $this->client->submitForm('submit', [
            'collection[title]' => 'Frieren',
            'collection[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertSame('Frieren', $crawler->filter('h1')->text());
    }

    public function test_can_edit_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        // Act
        $this->client->request('GET', '/collections/' . $collection->getId() . '/edit');
        $crawler = $this->client->submitForm('submit', [
            'collection[title]' => 'Berserk',
            'collection[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertSame('Berserk', $crawler->filter('h1')->text());
    }
}
