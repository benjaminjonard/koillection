<?php

declare(strict_types=1);

namespace App\Tests\Api\Tag;

use App\Entity\Item;
use App\Tests\ApiTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\TagCategoryFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TagApiNotOwnerTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_cant_get_another_user_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $tag = TagFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/tags/' . $tag->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $category = TagCategoryFactory::createOne(['owner' => $owner]);
        $tag = TagFactory::createOne(['category' => $category, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/tags/' . $tag->getId() . '/category');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_post_tag_in_another_user_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $category = TagCategoryFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/tags/', ['json' => [
            'category' => '/api/tag_categories/' . $category->getId()
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_tag_items(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $tag = TagFactory::createOne(['owner' => $owner]);
        ItemFactory::createMany(3, ['collection' => $collection, 'tags' => [$tag], 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/tags/' . $tag->getId() . '/items');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function test_cant_put_another_user_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $tag = TagFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/tags/' . $tag->getId(), ['json' => [
            'label' => 'Manga',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $tag = TagFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/tags/' . $tag->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'label' => 'Manga',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->_real();
        $owner = UserFactory::createOne()->_real();
        $tag = TagFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/tags/' . $tag->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
