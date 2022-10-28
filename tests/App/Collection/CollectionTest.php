<?php

declare(strict_types=1);

namespace App\Tests\App\Collection;

use App\Enum\DateFormatEnum;
use App\Enum\DatumTypeEnum;
use App\Enum\DisplayModeEnum;
use App\Enum\VisibilityEnum;
use App\Tests\Factory\ChoiceListFactory;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CollectionTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

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

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Collections', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
    }

    public function test_can_edit_collection_index(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        CollectionFactory::createMany(3, ['owner' => $user]);

        // Act
        $this->client->request('GET', '/collections/edit');
        $crawler = $this->client->submitForm('Submit', [
            'display_configuration[displayMode]' => DisplayModeEnum::DISPLAY_MODE_LIST,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Collections', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.list-element'));
    }

    public function test_can_get_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne(['currency' => 'EUR', 'dateFormat' => DateFormatEnum::FORMAT_SLASH_DMY])->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);
        CollectionFactory::createMany(3, ['parent' => $collection, 'owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $collection, 'owner' => $user]);
        $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'choices' => ['In progress', 'Done', 'Abandoned'], 'owner' => $user]);

        // @TODO File
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'position' => 1, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Japanese title', 'value' => '葬送のフリーレン']);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'position' => 2, 'type' => DatumTypeEnum::TYPE_NUMBER, 'label' => 'Volumes', 'value' => '12']);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'position' => 3, 'type' => DatumTypeEnum::TYPE_COUNTRY, 'label' => 'Country', 'value' => 'JP']);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'position' => 4, 'type' => DatumTypeEnum::TYPE_DATE, 'label' => 'Release date', 'value' => '2022-03-03']);
        DatumFactory::createOne(['owner' => $user, 'collection' => $collection, 'position' => 5, 'type' => DatumTypeEnum::TYPE_LIST, 'label' => 'Progress', 'value' => json_encode(['In progress']), 'choiceList' => $choiceList]);

        // Act
        $crawler = $this->client->request('GET', '/collections/'.$collection->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($collection->getTitle(), $crawler->filter('h1')->text());
        $this->assertCount(1, $crawler->filter('.collection-header .visibility .fa-lock'));
        $this->assertCount(3, $crawler->filter('.collection-element'));
        $this->assertCount(3, $crawler->filter('.collection-item'));

        $this->assertCount(5, $crawler->filter('.datum-row'));
        $this->assertSame('Japanese title : 葬送のフリーレン', $crawler->filter('.datum-row')->eq(0)->text());
        $this->assertSame('Volumes : 12', $crawler->filter('.datum-row')->eq(1)->text());
        $this->assertSame('Country : 🇯🇵 (Japan)', $crawler->filter('.datum-row')->eq(2)->text());
        $this->assertSame('Release date : 03/03/2022', $crawler->filter('.datum-row')->eq(3)->text());
        $this->assertSame('Progress : In progress', $crawler->filter('.datum-row')->eq(4)->text());
    }

    public function test_can_get_collection_with_list_view(): void
    {
        // Arrange
        $user = UserFactory::createOne(['currency' => 'EUR', 'dateFormat' => DateFormatEnum::FORMAT_SLASH_DMY])->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $collection->getItemsDisplayConfiguration()
            ->setDisplayMode(DisplayModeEnum::DISPLAY_MODE_LIST)
            ->setColumns(['Author'])
        ;
        $collection->save();
        $collection->getChildrenDisplayConfiguration()
            ->setDisplayMode(DisplayModeEnum::DISPLAY_MODE_LIST)
            ->setColumns(['Author'])
        ;
        $collection->save();

        $child1 = CollectionFactory::createOne(['parent' => $collection, 'owner' => $user]);
        DatumFactory::createOne(['owner' => $user, 'collection' => $child1, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Author']);
        $child2 = CollectionFactory::createOne(['parent' => $collection, 'owner' => $user]);
        DatumFactory::createOne(['owner' => $user, 'collection' => $child2, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Author']);
        $child3 = CollectionFactory::createOne(['parent' => $collection, 'owner' => $user]);
        DatumFactory::createOne(['owner' => $user, 'collection' => $child3, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Author']);

        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item1, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Author']);
        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item2, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Author']);
        $item3 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item3, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Author']);

        // Act
        $crawler = $this->client->request('GET', '/collections/'.$collection->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($collection->getTitle(), $crawler->filter('h1')->text());

        $this->assertCount(3, $crawler->filter('.children-table tbody tr'));
        $this->assertCount(7, $crawler->filter('.children-table thead th'));
        $this->assertSame('', $crawler->filter('.children-table thead th')->eq(0)->text());
        $this->assertSame('Name', $crawler->filter('.children-table thead th')->eq(1)->text());
        $this->assertSame('Author', $crawler->filter('.children-table thead th')->eq(2)->text());
        $this->assertSame('Collections', $crawler->filter('.children-table thead th')->eq(3)->text());
        $this->assertSame('Items', $crawler->filter('.children-table thead th')->eq(4)->text());
        $this->assertSame('Visibility', $crawler->filter('.children-table thead th')->eq(5)->text());
        $this->assertSame('Actions', $crawler->filter('.children-table thead th')->eq(6)->text());

        $this->assertCount(3, $crawler->filter('.items-table tbody tr'));
        $this->assertCount(5, $crawler->filter('.items-table thead th'));
        $this->assertSame('', $crawler->filter('.items-table thead th')->eq(0)->text());
        $this->assertSame('Name', $crawler->filter('.items-table thead th')->eq(1)->text());
        $this->assertSame('Author', $crawler->filter('.items-table thead th')->eq(2)->text());
        $this->assertSame('Visibility', $crawler->filter('.items-table thead th')->eq(3)->text());
        $this->assertSame('Actions', $crawler->filter('.items-table thead th')->eq(4)->text());
    }

    public function test_can_post_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $parent = CollectionFactory::createOne(['owner' => $user]);

        // Act
        $this->client->request('GET', '/collections/add?parent='.$parent->getId());
        $crawler = $this->client->submitForm('Submit', [
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
        $this->client->request('GET', '/collections/'.$collection->getId().'/edit');
        $crawler = $this->client->submitForm('Submit', [
            'collection[title]' => 'Berserk',
            'collection[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC
        ]);

        // Assert
        $this->assertSame('Berserk', $crawler->filter('h1')->text());
    }

    public function test_can_get_collection_items_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $childCollection = CollectionFactory::createOne(['parent' => $collection, 'owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $collection, 'owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $childCollection, 'owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/collections/'.$collection->getId().'/items');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame($collection->getTitle(), $crawler->filter('h1')->text());
        $this->assertCount(6, $crawler->filter('.collection-item'));
    }

    public function test_can_delete_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $collection, 'owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/collections/'.$collection->getId());
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/collections/'.$collection->getId().'/delete');
        $this->client->submitForm('Agree');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_collection_index');
        CollectionFactory::assert()->count(0);
        ItemFactory::assert()->count(0);
    }

    public function test_can_delete_child_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $childCollection = CollectionFactory::createOne(['parent' => $collection, 'owner' => $user]);
        $otherCollection = CollectionFactory::createOne(['owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $collection, 'owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $childCollection, 'owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $otherCollection, 'owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/collections/'.$collection->getId());
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/collections/'.$childCollection->getId().'/delete');
        $this->client->submitForm('Agree');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_collection_show', ['id' => $collection->getId()]);
        CollectionFactory::assert()->count(2);
        ItemFactory::assert()->count(6);
    }

    public function test_can_batch_tag_collection(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $childCollection = CollectionFactory::createOne(['parent' => $collection, 'owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $childItem = ItemFactory::createOne(['collection' => $childCollection, 'owner' => $user]);
        $tag = TagFactory::createOne(['label' => 'Frieren', 'owner' => $user])->object();

        // Act
        $this->client->request('GET', '/collections/'.$collection->getId().'/batch-tagging');
        $this->client->submitForm('Submit', [
            'batch_tagger[tags]' => json_encode(['Frieren']),
            'batch_tagger[recursive]' => true
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame($item->getTags()->first()->getId(), $tag->getId());
        $this->assertSame($childItem->getTags()->first()->getId(), $tag->getId());
    }
}
