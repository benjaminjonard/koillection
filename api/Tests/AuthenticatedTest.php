<?php

namespace Api\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

abstract class AuthenticatedTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    protected $jwtToken;

    public function __construct()
    {
        $this->jwtToken = $this->getJwtToken();
    }

    public function getJwtToken(): string
    {
        $response = static::createClient()->request('POST', '/api/authentication_token', ['json' => [
            'email' => 'user1@koillection.com',
            'password' => 'password',
        ]]);

        dd($response);

        return '';
    }
}