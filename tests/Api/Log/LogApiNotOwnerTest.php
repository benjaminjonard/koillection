<?php

declare(strict_types=1);

namespace App\Tests\Api\Log;

use Api\Tests\ApiTestCase;
use App\Factory\LogFactory;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

class LogApiNotOwnerTest extends ApiTestCase
{
    use Factories;

    public function test_cant_get_another_user_log(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $owner = UserFactory::createOne()->object();
        $log = LogFactory::createOne(['owner' => $owner]);

        // Act
        $this->createClientWithCredentials($user)->request('GET', '/api/logs/'.$log->getId());

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
