<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\DisplayModeEnum;
use App\Enum\VisibilityEnum;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Tests\AppTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TagTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

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

    public function test_ajax_tag_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        TagFactory::createMany(7, ['owner' => $user, 'label' => 'tests']);
        TagFactory::createMany(3, ['owner' => $user, 'label' => 'Manga']);

        // Act
        $crawler = $this->client->xmlHttpRequest('GET', '/tags?search_tag[term]=man');

        // Assert
        $this->assertResponseIsSuccessful();
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

    public function test_can_edit_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $tag = TagFactory::createOne(['owner' => $user]);

        // Act
        $this->client->request('GET', '/tags/'.$tag->getId().'/edit');
        $crawler = $this->client->submitForm('Submit', [
            'tag[label]' => 'Frieren',
            'tag[category]' => '',
            'tag[description]' => 'Description',
            'tag[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC,
            'tag[itemsDisplayConfiguration][displayMode]' => DisplayModeEnum::DISPLAY_MODE_GRID
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Frieren', $crawler->filter('h1')->text());
    }

    public function test_can_delete_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $tag = TagFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/tags/'.$tag->getId());
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/tags/'.$tag->getId().'/delete');
        $this->client->submitForm('Agree');

        // Assert
        $this->assertResponseIsSuccessful();
        TagFactory::assert()->count(0);
    }

    public function test_can_delete_unused_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $tag = TagFactory::createOne(['owner' => $user])->object();
        TagFactory::createOne(['owner' => $user]);
        TagFactory::createOne(['owner' => $user]);
        $collection = CollectionFactory::createOne(['owner' => $user])->object();
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $item->addTag($tag);
        $item->save();

        // Act
        $crawler = $this->client->request('GET', '/tags');
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/tags/delete-unused-tags');
        $this->client->submitForm('Agree');

        // Assert
        $this->assertResponseIsSuccessful();
        TagFactory::assert()->count(1);
    }

    public function test_can_use_tag_autocomplete(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        TagFactory::createOne(['label' => 'Frieren', 'owner' => $user]);
        TagFactory::createOne(['label' => 'Berserk', 'owner' => $user]);
        TagFactory::createOne(['label' => 'Manga', 'owner' => $user]);

        // Act
        $this->client->request('GET', '/tags/autocomplete/fri');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(1, $content);
        $this->assertSame('Frieren', $content[0]['text']);
    }

    public function test_can_show_item_from_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $tag = TagFactory::createOne(['label' => 'Frieren', 'owner' => $user])->object();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user])->object();
        $item = ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user]);
        $item->addTag($tag);
        $item->save();

        // Act
        $crawler = $this->client->request('GET', '/tags/'.$tag->getId().'/items/'.$item->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Frieren #1', $crawler->filter('h1')->text());
        $this->assertSame('From collection : Frieren', $crawler->filter('.title-block div')->text());
    }
}
