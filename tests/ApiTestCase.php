<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

abstract class ApiTestCase extends \ApiPlatform\Symfony\Bundle\Test\ApiTestCase
{
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
