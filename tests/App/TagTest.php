<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\VisibilityEnum;
use App\Factory\UserFactory;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\TagFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TagTest extends WebTestCase
{
    use Factories, ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_see_tag_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        TagFactory::createMany(3, ['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/tags');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Tags', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.list-element'));
    }

    public function test_can_see_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $tag = TagFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        $tagRelated = TagFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        ItemFactory::createMany(3, [
            'owner' => $user,
            'tags' => [$tag, $tagRelated],
            'collection' => CollectionFactory::createOne(['owner' => $user])->object(),
        ]);

        // Act
        $crawler = $this->client->request('GET', '/tags/'.$tag->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame($tag->getLabel(), $crawler->filter('h1')->text());
        $this->assertCount(1, $crawler->filter('.collection-header .fa-lock'));

        $this->assertSame('Info', $crawler->filter('h2')->eq(0)->text());
        $this->assertSame($tag->getDescription(), $crawler->filter('.tag-description')->text());

        $this->assertSame('Related tags', $crawler->filter('h2')->eq(1)->text());
        $this->assertCount(1, $crawler->filter('.tag'));
        $this->assertSame($tagRelated->getLabel(), $crawler->filter('.tag a')->text());

        $this->assertSame('Items', $crawler->filter('h2')->eq(2)->text());
        $this->assertCount(3, $crawler->filter('.collection-item'));
    }
}
