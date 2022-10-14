<?php

declare(strict_types=1);

namespace App\Tests\App\Security;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class FirstConnectionTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    /*public function test_can_complete_first_connection(): void
    {
        // Arrange
        UserFactory::createOne()->object();

        // Act
        $this->client->request('GET', '/first-connection');
        $crawler = $this->client->submitForm('Sign in', [
            '_login' => $user->getUsername(),
            '_password' => $user->getPlainPassword()
        ]);

        // Assert
        $this->assertRouteSame('app_security_login');
    }*/

    public function test_cant_redo_first_connection(): void
    {
        // Arrange
        UserFactory::createOne()->object();

        // Act
        $crawler = $this->client->request('GET', '/first-connection');

        // Assert
        $this->assertRouteSame('app_security_login');
    }
}
