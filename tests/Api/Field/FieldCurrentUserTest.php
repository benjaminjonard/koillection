<?php

declare(strict_types=1);

namespace App\Tests\Api\Field;

use Api\Tests\ApiTestCase;
use App\Entity\Field;
use App\Entity\Template;
use App\Enum\DatumTypeEnum;
use Symfony\Component\HttpFoundation\Response;

class FieldCurrentUserTest extends ApiTestCase
{
    public function testGetFields(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/fields');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertSame(50, $data['hydra:totalItems']);
        $this->assertCount(30, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Field::class);
    }

    public function testGetField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testGetFieldTemplate(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

        $this->createClientWithCredentials()->request('GET', $iri.'/template');

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(Template::class);
    }

    public function testPostField(): void
    {
        $template = $this->em->getRepository(Template::class)->findBy(['owner' => $this->user], [], 1)[0];
        $templateIri = $this->iriConverter->getIriFromItem($template);

        $this->createClientWithCredentials()->request('POST', '/api/fields', [
            'json' => [
                'name' => 'New field',
                'type' => DatumTypeEnum::TYPE_TEXT,
                'position' => 0,
                'template' => $templateIri,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'name' => 'New field',
        ]);
    }

    public function testPutField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PUT',
        ]);
    }

    public function testPatchField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);

        $this->createClientWithCredentials()->request('PATCH', $iri, [
            'headers' => ['Content-Type: application/merge-patch+json'],
            'json' => [
                'name' => 'updated name with PATCH',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PATCH',
        ]);
    }

    public function testDeleteField(): void
    {
        $field = $this->em->getRepository(Field::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($field);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
