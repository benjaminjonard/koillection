<?php

declare(strict_types=1);

namespace Api\Tests;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ApiTestCase extends \ApiPlatform\Symfony\Bundle\Test\ApiTestCase
{
    use RefreshDatabaseTrait;

    private ?string $token = null;

    protected ?User $user = null;

    protected ?User $otherUser = null;

    protected ?TranslatorInterface $translator = null;

    protected ?EntityManagerInterface $em = null;

    protected ?IriConverterInterface $iriConverter = null;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->translator = $this->getContainer()->get('translator');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $this->iriConverter = $this->getContainer()->get('api_platform.iri_converter');

        $this->user = $this->em->getRepository(User::class)->findOneBy([
            'username' => 'User',
        ]);

        $this->otherUser = $this->em->getRepository(User::class)->findOneBy([
            'username' => 'Admin',
        ]);
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();

        return static::createClient([], ['headers' => ['Host' => 'localhost', 'Authorization' => 'Bearer '.$token]]);
    }

    protected function getToken($body = []): string
    {
        if ($this->token) {
            return $this->token;
        }

        $response = static::createClient()->request('POST', '/api/authentication_token', ['json' => $body ?: [
            'username' => 'User',
            'password' => 'password',
        ]]);

        $data = json_decode($response->getContent(), true);
        $this->token = $data['token'];

        return $this->token;
    }
}
