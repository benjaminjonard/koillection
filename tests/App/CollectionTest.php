<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\RoleEnum;
use App\Enum\VisibilityEnum;
use App\Factory\CollectionFactory;
use App\Factory\ItemFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class CollectionTest extends WebTestCase
{
    use Factories;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->user1 = UserFactory::createOne(['username' => 'user1', 'email' => 'user1@test.com', 'roles' => [RoleEnum::ROLE_USER]])->object();
        $this->user2 = UserFactory::createOne(['username' => 'user2', 'email' => 'user2@test.com','roles' => [RoleEnum::ROLE_USER]])->object();
    }

    public function test_can_get_collection_list(): void
    {
        $this->client->loginUser($this->user1);

        CollectionFactory::createMany(3, ['parent' => null, 'owner' => $this->user1]);

        // Should not appear in page and counters
        CollectionFactory::createMany(3, ['parent' => null, 'owner' => $this->user2]);

        $crawler = $this->client->request('GET', '/collections');
        $this->assertResponseIsSuccessful();

        $this->assertCount(3, $crawler->filter('.collection-element'));
        $this->assertEquals('0 items', $crawler->filter('.nav-pills li')->eq(0)->text());
        $this->assertEquals('3 collections', $crawler->filter('.nav-pills li')->eq(1)->text());

        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/collections');
    }

    public function test_can_get_collection(): void
    {
        $this->client->loginUser($this->user1);

        $collection = CollectionFactory::createOne(['parent' => null, 'owner' => $this->user1]);
        CollectionFactory::createMany(3, ['parent' => $collection, 'owner' => $this->user1]);
        ItemFactory::createMany(3, ['collection' => $collection, 'owner' => $this->user1]);

        // Should not appear in page and counters
        CollectionFactory::createMany(3, ['parent' => $collection, 'owner' => $this->user2]);
        ItemFactory::createMany(3, ['collection' => $collection, 'owner' => $this->user2]);

        $crawler = $this->client->request('GET', '/collections/' . $collection->getId());
        $this->assertResponseIsSuccessful();

        $this->assertEquals($collection->getTitle(), $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-item'));
        $this->assertEquals('3 items', $crawler->filter('.nav-pills li')->eq(0)->text());
        $this->assertEquals('3 collections', $crawler->filter('.nav-pills li')->eq(1)->text());
    }

    public function test_can_post_collection(): void
    {
        $this->client->loginUser($this->user1);
        $parent = CollectionFactory::createOne(['parent' => null, 'owner' => $this->user1]);

        $crawler = $this->client->request('GET', '/collections/add');
        $this->assertResponseIsSuccessful();

        $this->client->executeScript("document.querySelector('#btn-add-field-text').click()");
        $this->client->executeScript("document.querySelector('#btn-add-field-country').click()");

        $this->client->submitForm('submit', [
            'collection[title]' => 'Frieren',
            'collection[parent]' => $parent->getId(),
            'collection[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC,
            'collection[data][0][position]' => 1,
            'collection[data][0][type]' => 'text',
            'collection[data][0][label]' => 'Japanese title',
            'collection[data][0][value]' => '葬送のフリーレン',
            'collection[data][1][position]' => 2,
            'collection[data][1][type]' => 'country',
            'collection[data][1][label]' => 'Country',
            'collection[data][1][value]' => 'JP'
        ]);

        CollectionFactory::assert()->exists([
            'title' => 'Frieren'
        ]);
    }
}
