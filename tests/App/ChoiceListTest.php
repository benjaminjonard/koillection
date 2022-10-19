<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Factory\UserFactory;
use App\Tests\Factory\ChoiceListFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ChoiceListTest extends WebTestCase
{
    use Factories, ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_see_choice_list_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request('GET', '/choice-lists');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Choice lists', $crawler->filter('h1')->text());
    }

    public function test_can_get_add_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request('GET', '/choice-lists/add');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Add a new choice list', $crawler->filter('h1')->text());
    }

    public function test_can_edit_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $choiceList = ChoiceListFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/choice-lists/'.$choiceList->getId(). '/edit');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Edit choice list ' . $choiceList->getName(), $crawler->filter('h1')->text());
    }
}
