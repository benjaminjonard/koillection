<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\VisibilityEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class StatisticsTest extends AppTestCase
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

    public function test_can_see_statistics(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/statistics');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Statistics', $crawler->filter('h1')->text());
    }

    public function test_shared_statistics_only_count_visible_content(): void
    {
        $owner = UserFactory::createOne(['visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        $publicCollection = CollectionFactory::createOne(['owner' => $owner, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        ItemFactory::createOne(['owner' => $owner, 'collection' => $publicCollection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        $privateCollection = CollectionFactory::createOne(['owner' => $owner, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        ItemFactory::createOne(['owner' => $owner, 'collection' => $privateCollection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        $this->client->request(Request::METHOD_GET, '/user/' . $owner->getUsername() . '/statistics');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', '1 collection');
        $this->assertSelectorTextContains('body', '1 item');
        $this->assertSelectorTextNotContains('body', '2 items');
    }

    public function test_owner_statistics_count_all_content(): void
    {
        $owner = UserFactory::createOne(['visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        $publicCollection = CollectionFactory::createOne(['owner' => $owner, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        ItemFactory::createOne(['owner' => $owner, 'collection' => $publicCollection, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        $privateCollection = CollectionFactory::createOne(['owner' => $owner, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        ItemFactory::createOne(['owner' => $owner, 'collection' => $privateCollection, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        $this->client->loginUser($owner);

        $this->client->request(Request::METHOD_GET, '/statistics');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', '2 collections');
        $this->assertSelectorTextContains('body', '2 items');
    }
}
