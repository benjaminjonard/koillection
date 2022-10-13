<?php

declare(strict_types=1);

namespace App\Tests\Api\TagCategory;

use Api\Tests\ApiTestCase;
use App\Entity\ChoiceList;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Factory\ChoiceListFactory;
use App\Factory\CollectionFactory;
use App\Factory\ItemFactory;
use App\Factory\TagCategoryFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class TagCategoryApiTest extends ApiTestCase
{
    use Factories;

    public function test_get_tag_categories(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        TagCategoryFactory::createMany(3, ['owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/tag_categories');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(TagCategory::class);
    }

    public function test_get_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/tag_categories/' . $category->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(TagCategory::class);
        $this->assertJsonContains([
            'id' => $category->getId()
        ]);
    }

    public function test_get_tag_category_tags(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $user]);
        TagFactory::createMany(3, ['category' => $category, 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/tag_categories/'.$category->getId().'/tags');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Tag::class);
    }

    public function test_post_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/tag_categories', ['json' => [
            'label' => 'Artist',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(TagCategory::class);
        $this->assertJsonContains([
            'label' => 'Artist',
        ]);
    }

    public function test_put_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/tag_categories/'.$category->getId(), ['json' => [
            'label' => 'Artist',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(TagCategory::class);
        $this->assertJsonContains([
            'id' => $category->getId(),
            'label' => 'Artist',
        ]);
    }

    public function test_patch_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/tag_categories/'.$category->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'label' => 'Artist',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(TagCategory::class);
        $this->assertJsonContains([
            'id' => $category->getId(),
            'label' => 'Artist',
        ]);
    }

    public function test_delete_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/tag_categories/'.$category->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}