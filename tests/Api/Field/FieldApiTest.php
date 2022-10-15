<?php

declare(strict_types=1);

namespace App\Tests\Api\Field;

use App\Entity\Field;
use App\Entity\Template;
use App\Enum\DatumTypeEnum;
use App\Factory\FieldFactory;
use App\Factory\TemplateFactory;
use App\Factory\UserFactory;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class FieldApiTest extends ApiTestCase
{
    use Factories, ResetDatabase;

    public function test_get_fields(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $user]);
        FieldFactory::createMany(3, ['template' => $template, 'owner' => $user]);

        // Act
        $response = $this->createClientWithCredentials($user)->request('GET', '/api/fields');
        $data = $response->toArray();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(3, $data['hydra:totalItems']);
        $this->assertCount(3, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Field::class);
    }

    public function test_get_field(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $user]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/fields/'.$field->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Field::class);
        $this->assertJsonContains([
            'id' => $field->getId()
        ]);
    }

    public function test_get_field_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $user]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/fields/'.$field->getId().'/template');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Template::class);
        $this->assertJsonContains([
            'id' => $template->getId()
        ]);
    }

    public function test_post_field(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('POST', '/api/fields', ['json' => [
            'template' => '/api/templates/'.$template->getId(),
            'name' => 'Title',
            'position' => 1,
            'type' => DatumTypeEnum::TYPE_TEXT,
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Field::class);
        $this->assertJsonContains([
            'name' => 'Title',
        ]);
    }

    public function test_put_field(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $user]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/fields/'.$field->getId(), ['json' => [
            'template' => '/api/templates/'.$template->getId(),
            'name' => 'Author',
        ]]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Field::class);
        $this->assertJsonContains([
            'id' => $field->getId(),
            'name' => 'Author',
        ]);
    }

    public function test_patch_field(): void
    {
        $user = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $user]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/fields/'.$field->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'Author',
            ],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Field::class);
        $this->assertJsonContains([
            'id' => $field->getId(),
            'name' => 'Author',
        ]);
    }

    public function test_delete_field(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $user]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $user]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/fields/'.$field->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
