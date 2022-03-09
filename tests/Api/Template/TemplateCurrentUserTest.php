<?php

declare(strict_types=1);

namespace App\Tests\Api\Template;

use Api\Tests\AuthenticatedTest;
use App\Entity\Field;
use App\Entity\Template;
use Symfony\Component\HttpFoundation\Response;

class TemplateCurrentUserTest extends AuthenticatedTest
{
    public function testGetTemplates(): void
    {
        $response = $this->createClientWithCredentials()->request('GET', '/api/templates');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(10, $data['hydra:totalItems']);
        $this->assertCount(10, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Template::class);
    }

    public function testGetTemplate(): void
    {
        $template = $this->em->getRepository(Template::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($template);

        $this->createClientWithCredentials()->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
        ]);
    }

    public function testGetTemplateFields(): void
    {
        $template = $this->em->getRepository(Template::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($template);

        $response = $this->createClientWithCredentials()->request('GET', $iri.'/fields');
        $data = $response->toArray();

        $this->assertResponseIsSuccessful();
        $this->assertEquals(5, $data['hydra:totalItems']);
        $this->assertCount(5, $data['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Field::class);
    }

    public function testPutTemplate(): void
    {
        $template = $this->em->getRepository(Template::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($template);

        $this->createClientWithCredentials()->request('PUT', $iri, ['json' => [
            'name' => 'updated name with PUT',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name with PUT',
        ]);
    }

    public function testPatchTemplate(): void
    {
        $template = $this->em->getRepository(Template::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($template);

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

    public function testDeleteTemplate(): void
    {
        $template = $this->em->getRepository(Template::class)->findBy(['owner' => $this->user], [], 1)[0];
        $iri = $this->iriConverter->getIriFromItem($template);
        $this->createClientWithCredentials()->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
