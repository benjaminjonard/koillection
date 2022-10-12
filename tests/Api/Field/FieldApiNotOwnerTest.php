<?php

declare(strict_types=1);

namespace App\Tests\Api\Field;

use Api\Tests\ApiTestCase;
use App\Entity\Album;
use App\Entity\Photo;
use App\Factory\AlbumFactory;
use App\Factory\FieldFactory;
use App\Factory\PhotoFactory;
use App\Factory\TemplateFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class FieldApiNotOwnerTest extends ApiTestCase
{
    use Factories;

    public function test_cant_get_another_user_field(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/fields/' . $field->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_get_another_user_field_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/fields/'.$field->getId().'/template');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_put_another_user_field(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PUT', '/api/fields/'.$field->getId(), ['json' => [
            'name' => 'Author',
        ]]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_patch_another_user_field(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('PATCH', '/api/fields/'.$field->getId(), [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'Author',
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_cant_delete_another_user_field(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $template = TemplateFactory::createOne(['owner' => $owner]);
        $field = FieldFactory::createOne(['template' => $template, 'owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('DELETE', '/api/fields/'.$field->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}