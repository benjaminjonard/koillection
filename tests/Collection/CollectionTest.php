<?php

declare(strict_types=1);

namespace App\Tests\Collection;

use App\Enum\RoleEnum;
use App\Enum\VisibilityEnum;
use App\Factory\CollectionFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class CollectionTest extends WebTestCase
{
    use Factories;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();

        $this->user1 = UserFactory::createOne(['username' => 'user1', 'email' => 'user1@test.com', 'roles' => [RoleEnum::ROLE_USER]])->object();
        $this->user2 = UserFactory::createOne(['username' => 'user2', 'email' => 'user2@test.com','roles' => [RoleEnum::ROLE_USER]])->object();
    }

    public function test_can_get_collection_list(): void
    {
        // Arrange
        $this->client->loginUser($this->user1);
        CollectionFactory::createMany(3, ['parent' => null, 'owner' => $this->user1]);

        // Act
        $crawler = $this->client->request('GET', '/collections');

        //Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals('Collections', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
    }

    public function test_can_get_collection(): void
    {
        // Arrange
        $this->client->loginUser($this->user1);
        $collection = CollectionFactory::createOne(['parent' => null, 'owner' => $this->user1]);

        // Act
        $crawler = $this->client->request('GET', '/collections/' . $collection->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($collection->getTitle(), $crawler->filter('h1')->text());
    }

    public function test_can_post_collection(): void
    {
        // Arrange
        $this->client->loginUser($this->user1);

        // Act
        $this->client->request('GET', '/collections/add');
        $crawler = $this->client->submitForm('submit', [
            'collection[title]' => 'Frieren',
            'collection[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertEquals('Frieren', $crawler->filter('h1')->text());
    }

    public function test_can_edit_collection(): void
    {
        // Arrange
        $this->client->loginUser($this->user1);
        $collection = CollectionFactory::createOne(['parent' => null, 'owner' => $this->user1]);

        // Act
        $this->client->request('GET', '/collections/' . $collection->getId() . '/edit');
        $crawler = $this->client->submitForm('submit', [
            'collection[title]' => 'Berserk',
            'collection[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertEquals('Berserk', $crawler->filter('h1')->text());
    }
}
