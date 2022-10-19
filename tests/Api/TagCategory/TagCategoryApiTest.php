<?php

declare(strict_types=1);

namespace App\Tests\Api\TagCategory;

use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Tests\ApiTestCase;
use App\Tests\Factory\TagCategoryFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TagCategoryApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

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
        $this->createClientWithCredentials($user)->request('GET', '/api/tag_categories/'.$category->getId());

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
