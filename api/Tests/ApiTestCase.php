<?php

declare(strict_types=1);

namespace Api\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class ApiTestCase extends \ApiPlatform\Symfony\Bundle\Test\ApiTestCase
{
    protected ?TranslatorInterface $translator = null;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->translator = $this->getContainer()->get('translator');
    }

    protected function createClientWithCredentials(User $user): Client
    {
        $encoder = $this->getContainer()->get(JWTEncoderInterface::class);
        $payload = [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ];

        return static::createClient([], ['headers' => ['Authorization' => 'Bearer '.$encoder->encode($payload)]]);
    }
}
