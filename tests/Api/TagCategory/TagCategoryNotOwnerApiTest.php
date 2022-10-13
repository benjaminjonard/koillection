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

class TagCategoryNotOwnerApiTest extends ApiTestCase
{
    use Factories;

    public function test_cant_get_another_user_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/tag_categories/' . $category->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_tag_category_tags(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $owner]);
        TagFactory::createMany(3, ['category' => $category, 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/tag_categories/'.$category->getId().'/tags');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Tag::class);
    }

    public function test_cant_put_another_user_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/tag_categories/'.$category->getId(), ['json' => [
            'label' => 'Author',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/tag_categories/'.$category->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'label' => 'Author',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/tag_categories/'.$category->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}