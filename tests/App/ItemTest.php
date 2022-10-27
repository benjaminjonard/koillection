<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\DateFormatEnum;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ItemTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_get_item(): void
    {
        // Arrange
        $user = UserFactory::createOne(['currency' => 'EUR', 'dateFormat' => DateFormatEnum::FORMAT_SLASH_DMY])->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user])->object();
        $relatedItem = ItemFactory::createOne(['name' => 'Calendar Frieren 2023', 'collection' => $collection, 'owner' => $user])->object();
        ItemFactory::createOne(['name' => 'Frieren #4', 'collection' => $collection, 'owner' => $user]);
        ItemFactory::createOne(['name' => 'Frieren #6', 'collection' => $collection, 'owner' => $user]);

        $item = ItemFactory::createOne([
            'name' => 'Frieren #5',
            'collection' => $collection,
            'owner' => $user,
            'visibility' => VisibilityEnum::VISIBILITY_PRIVATE,
            'quantity' => 2,
        ]);
        $tag = TagFactory::createOne(['owner' => $user, 'label' => 'Abe Tsukasa'])->object();
        $item->addTag($tag);
        $item->addTag(TagFactory::createOne(['owner' => $user, 'label' => 'Yamada Kanehito'])->object());
        $item->addRelatedItem($relatedItem);
        $item->save();

        // @TODO File, Image, Signature
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 1, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Authors', 'value' => 'Abe Tsukasa, Yamada Kanehito']);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 2, 'type' => DatumTypeEnum::TYPE_TEXTAREA, 'label' => 'Description', 'value' => 'Frieren est un shōnen manga écrit par Yamada Kanehito et dessiné par Abe Tsukasa.']);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 3, 'type' => DatumTypeEnum::TYPE_NUMBER, 'label' => 'Volume', 'value' => '1']);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 4, 'type' => DatumTypeEnum::TYPE_PRICE, 'label' => 'Price', 'value' => '7.95']);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 5, 'type' => DatumTypeEnum::TYPE_COUNTRY, 'label' => 'Country', 'value' => 'JP']);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 6, 'type' => DatumTypeEnum::TYPE_DATE, 'label' => 'Release date', 'value' => '2022-03-03']);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 7, 'type' => DatumTypeEnum::TYPE_RATING, 'label' => 'Rating', 'value' => '10']);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 8, 'type' => DatumTypeEnum::TYPE_LINK, 'label' => 'Wiki page', 'value' => 'https://ja.wikipedia.org/wiki/%E8%91%AC%E9%80%81%E3%81%AE%E3%83%95%E3%83%AA%E3%83%BC%E3%83%AC%E3%83%B3']);

        // Act
        $crawler = $this->client->request('GET', '/items/'.$item->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Frieren #5', $crawler->filter('h1')->innerText());
        $this->assertSame('(x2)', $crawler->filter('h1 .quantity')->innerText());
        $this->assertCount(1, $crawler->filter('.collection-header .visibility .fa-lock'));

        $this->assertCount(2, $crawler->filter('.tag'));
        $this->assertSame('Abe Tsukasa', $crawler->filter('.tag')->eq(0)->text());
        $this->assertSame('Yamada Kanehito', $crawler->filter('.tag')->eq(1)->text());

        $this->assertCount(8, $crawler->filter('.datum-row'));
        $this->assertSame('Authors : Abe Tsukasa, Yamada Kanehito', $crawler->filter('.datum-row')->eq(0)->text());
        $this->assertCount(2, $crawler->filter('.datum-row')->eq(0)->filter('a'));
        $this->assertSame('Abe Tsukasa', $crawler->filter('.datum-row')->eq(0)->filter('a')->eq(0)->text());
        $this->assertSame('/tags/'.$tag->getId(), $crawler->filter('.datum-row')->eq(0)->filter('a')->eq(0)->attr('href'));

        $this->assertSame('Description : Frieren est un shōnen manga écrit par Yamada Kanehito et dessiné par Abe Tsukasa.', $crawler->filter('.datum-row')->eq(1)->text());
        $this->assertSame('Volume : 1', $crawler->filter('.datum-row')->eq(2)->text());
        $this->assertSame('Price : €7.95', $crawler->filter('.datum-row')->eq(3)->text());
        $this->assertSame('Country : 🇯🇵 (Japan)', $crawler->filter('.datum-row')->eq(4)->text());
        $this->assertSame('Release date : 03/03/2022', $crawler->filter('.datum-row')->eq(5)->text());
        $this->assertSame('Rating :', $crawler->filter('.datum-row .label')->eq(6)->text());
        $this->assertCount(5, $crawler->filter('.datum-row')->eq(6)->filter('.fa-star.colored'));
        $this->assertSame('Wiki page :', $crawler->filter('.datum-row .label')->eq(7)->text());
        $this->assertSame(substr('https://ja.wikipedia.org/wiki/%E8%91%AC%E9%80%81%E3%81%AE%E3%83%95%E3%83%AA%E3%83%BC%E3%83%AC%E3%83%B3', 0, 47).'...', $crawler->filter('.datum-row')->eq(7)->filter('a')->text());

        $this->assertCount(1, $crawler->filter('.related-items a'));
        $this->assertSame('Calendar Frieren 2023', $crawler->filter('.related-items a')->eq(0)->text());

        $this->assertCount(1, $crawler->filter('[data-swipe-target="previous"]'));
        $this->assertSame('Frieren #4', $crawler->filter('[data-swipe-target="previous"]')->eq(0)->text());
        $this->assertCount(1, $crawler->filter('[data-swipe-target="next"]'));
        $this->assertSame('Frieren #6', $crawler->filter('[data-swipe-target="next"]')->eq(0)->text());
    }

    public function test_can_create_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user])->object();

        // Act
        $this->client->request('GET', '/items/add?collection='.$collection->getId());
        $this->client->submitForm('Submit', [
            'item[name]' => 'Frieren #1',
            'item[collection]' => $collection->getId(),
            'item[quantity]' => 1,
            'item[visibility]' => VisibilityEnum::VISIBILITY_PRIVATE,
            'item[tags]' => json_encode(['Manga', 'Frieren'])
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        ItemFactory::assert()->exists([
            'name' => 'Frieren #1',
            'collection' => $collection->getId(),
            'visibility' => VisibilityEnum::VISIBILITY_PRIVATE,
            'owner' => $user
        ]);
        TagFactory::assert()->exists(['label' => 'Manga', 'owner' => $user]);
        TagFactory::assert()->exists(['label' => 'Frieren', 'owner' => $user]);
    }

    public function test_can_edit_item(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user])->object();
        $item = ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user])->object();

        // Act
        $this->client->request('GET', '/items/'.$item->getId().'/edit');
        $this->client->submitForm('Submit', [
            'item[name]' => 'Berserk #1',
            'item[collection]' => $collection->getId()
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        ItemFactory::assert()->exists([
            'name' => 'Berserk #1',
            'collection' => $collection->getId(),
            'owner' => $user
        ]);
    }
}
