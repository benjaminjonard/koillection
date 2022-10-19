<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoanTest extends WebTestCase
{
    use Factories, ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_see_loan_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request('GET', '/loans');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Loans', $crawler->filter('h1')->text());
    }
}
