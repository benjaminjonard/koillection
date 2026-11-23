<?php

declare(strict_types=1);

namespace App\Tests\App\Security;

use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class WorkerModeTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    public function test_filters_do_not_leak_between_requests_of_the_same_kernel(): void
    {
        // Arrange
        $client = static::createClient();
        $client->disableReboot();
        $user = UserFactory::createOne();
        $otherUser = UserFactory::createOne();
        CollectionFactory::createOne(['owner' => $user, 'title' => 'Frieren']);
        CollectionFactory::createOne(['owner' => $otherUser, 'title' => 'Sousou no Frieren']);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, '/collections');
        $this->assertResponseIsSuccessful();

        // Act
        $client->loginUser($otherUser);
        $crawler = $client->request(Request::METHOD_GET, '/collections');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame(['Sousou no Frieren'], $crawler->filter('.collection-element .collection-title')->each(static fn ($node): string => trim($node->text())));
    }
}
