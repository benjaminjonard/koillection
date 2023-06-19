<?php

declare(strict_types=1);

namespace App\Tests\Api\Tag;

use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Tests\ApiTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\TagCategoryFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TagApiTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_get_tags(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        TagFactory::createMany(3, ['owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/tags');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Tag::class);
    }

    public function test_get_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $tag = TagFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/tags/'.$tag->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Tag::class);
        $this->assertJsonContains([
            'id' => $tag->getId()
        ]);
    }

    public function test_get_tag_category(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $category = TagCategoryFactory::createOne(['owner' => $user]);
        $tag = TagFactory::createOne(['category' => $category, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/tags/'.$tag->getId().'/category');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(TagCategory::class);
        $this->assertJsonContains([
            'id' => $category->getId()
        ]);
    }

    public function test_get_tag_items(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $tag = TagFactory::createOne(['owner' => $user]);
        ItemFactory::createMany(3, ['collection' => $collection, 'tags' => [$tag], 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/tags/'.$tag->getId().'/items');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Item::class);
    }

    public function test_post_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/tags', ['json' => [
            'label' => 'Manga'
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Tag::class);
        $this->assertJsonContains([
            'label' => 'Manga'
        ]);
    }

    public function test_put_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $tag = TagFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/tags/'.$tag->getId(), ['json' => [
            'label' => 'Manga'
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Tag::class);
        $this->assertJsonContains([
            'id' => $tag->getId(),
            'label' => 'Manga'
        ]);
    }

    public function test_patch_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $tag = TagFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/tags/'.$tag->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'label' => 'Manga'
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Tag::class);
        $this->assertJsonContains([
            'id' => $tag->getId(),
            'label' => 'Manga'
        ]);
    }

    public function test_delete_tag(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $tag = TagFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/tags/'.$tag->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function test_post_tag_image(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $tag = TagFactory::createOne(['owner' => $user]);
        $uploadedFile = $this->createFile('png');

        // Act
        $crawler = $this->createClientWithCredentials($user)->request('POST', '/api/tags/'.$tag->getId().'/image', [
            'headers' => ['Content-Type: multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $uploadedFile,
                ],
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Tag::class);
        $this->assertNotNull(json_decode($crawler->getContent(), true)['image']);
        $this->assertFileExists(json_decode($crawler->getContent(), true)['image']);
    }
}
