<?php

declare(strict_types=1);

namespace App\Tests\Api\Template;

use Api\Tests\ApiTestCase;
use App\Entity\ChoiceList;
use App\Entity\Field;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Entity\Template;
use App\Factory\ChoiceListFactory;
use App\Factory\CollectionFactory;
use App\Factory\FieldFactory;
use App\Factory\ItemFactory;
use App\Factory\TagCategoryFactory;
use App\Factory\TagFactory;
use App\Factory\TemplateFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class TemplateApiNotOwnerTest extends ApiTestCase
{
    use Factories;

    public function test_cant_get_another_user_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/templates/' . $template->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_template_fields(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);
        FieldFactory::createMany(3, ['template' => $template, 'owner' => $owner]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/templates/'.$template->getId().'/fields');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $data['hydra:totalItems']);
        $this->assertCount(0, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Field::class);
    }

    public function test_cant_put_another_user_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/templates/'.$template->getId(), ['json' => [
            'name' => 'Video game',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/templates/'.$template->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'Video game',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/templates/'.$template->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}