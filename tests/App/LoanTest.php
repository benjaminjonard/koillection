<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\LoanFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoanTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

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

    public function test_can_delete_loan(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $loan = LoanFactory::createOne(['item' => $item, 'owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/loans');
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/loans/'.$loan->getId().'/delete');
        $this->client->submitForm('Agree');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_loan_index');
        LoanFactory::assert()->count(0);
    }

    public function test_can_set_loan_as_returned(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['collection' => $collection, 'owner' => $user]);
        $loan = LoanFactory::createOne(['item' => $item, 'owner' => $user]);

        // Act
        $this->client->request('GET', '/loans/'.$loan->getId().'/returned');
        $loan->refresh();

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_loan_index');
        $this->assertNotNull($loan->getReturnedAt());
    }
}
