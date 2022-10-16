<?php

declare(strict_types=1);

namespace App\Tests\App\Security;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class FirstConnectionTest extends WebTestCase
{
    use Factories, ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_complete_first_connection(): void
    {
        // Arrange

        // Act
        $this->client->request('GET', '/first-connection');
        $this->client->submitForm('Submit', [
            'user[username]' => 'Stitch',
            'user[email]' => 'stitch@koillection.com',
            'user[plainPassword][first]' => 'password1234',
            'user[plainPassword][second]' => 'password1234',
            'user[timezone]' => 'Pacific/Honolulu',
            'user[dateFormat]' => 'd/m/Y',
        ]);

        // Assert
        $this->assertRouteSame('app_collection_index');
    }

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
