<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\AdvancedItemSearch\ConditionEnum;
use App\Enum\AdvancedItemSearch\OperatorEnum;
use App\Enum\AdvancedItemSearch\TypeEnum;
use App\Enum\DatumTypeEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\ChoiceListFactory;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\SearchBlockFactory;
use App\Tests\Factory\SearchFactory;
use App\Tests\Factory\SearchFilterFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AdvancedItemSearchTest extends AppTestCase
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

    public function test_can_see_advanced_item_search_index(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Advanced item search', $crawler->filter('h1')->text());
    }

    public function test_can_see_advanced_item_search(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $search = SearchFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Advanced item search', $crawler->filter('h1')->text());
    }

    public function test_can_see_saved_searches(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        SearchFactory::createMany(5, ['owner' => $user]);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/saved-searches');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $crawler->filter('tbody tr'));
    }

    public function test_can_delete_saved_searches(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $search = SearchFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/saved-searches');
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/advanced-item-search/' . $search->getId() . '/delete');
        $this->client->submitForm('OK');

        // Assert
        $this->assertResponseIsSuccessful();
        SearchFactory::assert()->notExists(0);
    }

    public function test_search_by_name(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_NAME, 'operator' => OperatorEnum::OPERATOR_CONTAINS, 'value' => 'Frieren']);

        $collection = CollectionFactory::createOne(['owner' => $user]);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Berserk #1']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Frieren #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_search_by_name_ignores_accents_and_case(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_NAME, 'operator' => OperatorEnum::OPERATOR_EQUAL, 'value' => 'SOSO NO FRIEREN #1']);

        $collection = CollectionFactory::createOne(['owner' => $user]);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Sōsō no Frieren #1']);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Sōsō no Frieren #2']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Sōsō no Frieren #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_search_with_or(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_NAME, 'operator' => OperatorEnum::OPERATOR_CONTAINS, 'value' => 'Frieren']);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_NAME, 'operator' => OperatorEnum::OPERATOR_EQUAL, 'value' => 'Astérix #1', 'condition' => ConditionEnum::CONDITION_OR]);

        $collection = CollectionFactory::createOne(['owner' => $user]);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Berserk #1']);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #2']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.collection-item'));
        $this->assertSame('Astérix #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
        $this->assertSame('Frieren #1', $crawler->filter('.collection-item')->eq(1)->filter('img')->attr('title'));
    }

    public function test_search_with_different_datum_labels(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_EQUAL, 'datumLabel' => 'Country', 'datumType' => DatumTypeEnum::TYPE_COUNTRY, 'value' => 'FR']);

        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Les champs de la Lune']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_COUNTRY, 'value' => 'FR', 'label' => 'Language']);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_COUNTRY, 'value' => 'FR', 'label' => 'Country']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Astérix #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_search_with_country(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_EQUAL, 'datumLabel' => 'Country', 'datumType' => DatumTypeEnum::TYPE_COUNTRY, 'value' => 'FR']);

        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_COUNTRY, 'value' => 'JP', 'label' => 'Country']);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_COUNTRY, 'value' => 'FR', 'label' => 'Country']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Astérix #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_search_with_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $choiceList = ChoiceListFactory::createOne(['name' => 'Progress', 'choices' => ['New', 'In progress', 'Done'], 'owner' => $user]);
        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_CONTAINS, 'datumLabel' => 'Progress', 'datumType' => DatumTypeEnum::TYPE_CHOICE_LIST, 'value' => 'In progress']);

        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Les champs de la Lune']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_CHOICE_LIST, 'value' => json_encode(['In progress']), 'label' => 'Progress']);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_CHOICE_LIST, 'value' => json_encode(['Done']), 'label' => 'Progress']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Les champs de la Lune', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_search_with_choice_list_ignores_accents(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $choiceList = ChoiceListFactory::createOne(['name' => 'Demographic', 'choices' => ['Shōnen', 'Seinen'], 'owner' => $user]);
        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_CONTAINS, 'datumLabel' => 'Demographic', 'datumType' => DatumTypeEnum::TYPE_CHOICE_LIST, 'value' => 'shonen']);

        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_CHOICE_LIST, 'value' => json_encode(['Shōnen']), 'label' => 'Demographic', 'choiceList' => $choiceList]);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #2']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_CHOICE_LIST, 'value' => json_encode(['Seinen']), 'label' => 'Demographic', 'choiceList' => $choiceList]);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Frieren #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_search_with_rating(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_SUPERIOR_OR_EQUAL, 'datumLabel' => 'Rating', 'datumType' => DatumTypeEnum::TYPE_RATING, 'value' => '9']);


        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Les champs de la Lune']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_RATING, 'value' => '9', 'label' => 'Rating']);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_RATING, 'value' => '7', 'label' => 'Rating']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Les champs de la Lune', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_search_with_number(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_INFERIOR, 'datumLabel' => 'Pages', 'datumType' => DatumTypeEnum::TYPE_NUMBER, 'value' => 100]);


        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Les champs de la Lune']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => 250, 'label' => 'Pages']);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => 56, 'label' => 'Pages']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Astérix #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_search_with_checkbox(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_INFERIOR, 'datumLabel' => 'Pages', 'datumType' => DatumTypeEnum::TYPE_NUMBER, 'value' => 100]);


        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Les champs de la Lune']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => 250, 'label' => 'Pages']);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => 56, 'label' => 'Pages']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Astérix #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_search_multiple_blocks(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        $search = SearchFactory::createOne(['owner' => $user]);
        $block1 = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block1, 'type' => TypeEnum::TYPE_NAME, 'operator' => OperatorEnum::OPERATOR_CONTAINS, 'value' => 'Frieren']);
        SearchFilterFactory::createOne(['block' => $block1, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_EQUAL, 'datumLabel' => 'Country', 'datumType' => DatumTypeEnum::TYPE_COUNTRY, 'value' => 'JP', 'condition' => ConditionEnum::CONDITION_AND]);

        $block2 = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_OR]);
        SearchFilterFactory::createOne(['block' => $block2, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_CONTAINS, 'datumLabel' => 'Author', 'datumType' => DatumTypeEnum::TYPE_TEXT, 'value' => 'Liu']);

        $collection = CollectionFactory::createOne(['owner' => $user]);

        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_COUNTRY, 'value' => 'JP', 'label' => 'Country']);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Artbook Frieren']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_COUNTRY, 'value' => 'FR', 'label' => 'Country']);

        $item3 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Le problème à trois corps']);
        DatumFactory::createOne(['item' => $item3, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_TEXT, 'value' => 'Liu Cixin', 'label' => 'Author']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('.collection-item'));
        $this->assertSame('Frieren #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
        $this->assertSame('Le problème à trois corps', $crawler->filter('.collection-item')->eq(1)->filter('img')->attr('title'));
    }

    public function test_name_does_not_contain(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_NAME, 'operator' => OperatorEnum::OPERATOR_DOES_NOT_CONTAIN, 'value' => 'Frieren']);

        $collection = CollectionFactory::createOne(['owner' => $user]);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Berserk #1']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Berserk #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_name_not_equal(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_NAME, 'operator' => OperatorEnum::OPERATOR_NOT_EQUAL, 'value' => 'Frieren #1']);

        $collection = CollectionFactory::createOne(['owner' => $user]);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Berserk #1']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Berserk #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_exists(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_EXISTS, 'datumLabel' => 'Pages', 'datumType' => DatumTypeEnum::TYPE_NUMBER]);


        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => 250, 'label' => 'Pages']);

        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Frieren #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_does_not_exists(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_DOES_NOT_EXIST, 'datumLabel' => 'Pages', 'datumType' => DatumTypeEnum::TYPE_NUMBER]);


        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => 250, 'label' => 'Pages']);

        ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Astérix #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_empty(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_EMPTY, 'datumLabel' => 'Pages', 'datumType' => DatumTypeEnum::TYPE_NUMBER]);


        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => 250, 'label' => 'Pages']);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => null, 'label' => 'Pages']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Astérix #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }

    public function test_not_empty(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        $search = SearchFactory::createOne(['owner' => $user]);
        $block = SearchBlockFactory::createOne(['search' => $search, 'condition' => ConditionEnum::CONDITION_AND]);
        SearchFilterFactory::createOne(['block' => $block, 'type' => TypeEnum::TYPE_DATUM, 'operator' => OperatorEnum::OPERATOR_NOT_EMPTY, 'datumLabel' => 'Pages', 'datumType' => DatumTypeEnum::TYPE_NUMBER]);


        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item1 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Frieren #1']);
        DatumFactory::createOne(['item' => $item1, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => 250, 'label' => 'Pages']);

        $item2 = ItemFactory::createOne(['collection' => $collection, 'owner' => $user, 'name' => 'Astérix #1']);
        DatumFactory::createOne(['item' => $item2, 'owner' => $user, 'type' => DatumTypeEnum::TYPE_NUMBER, 'value' => null, 'label' => 'Pages']);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/advanced-item-search/' . $search->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.collection-item'));
        $this->assertSame('Frieren #1', $crawler->filter('.collection-item')->eq(0)->filter('img')->attr('title'));
    }
}
