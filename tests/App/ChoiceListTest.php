<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Tests\Factory\ChoiceListFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ChoiceListTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

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

    public function test_can_post_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/choice-lists/add');
        $crawler = $this->client->submitForm('Submit', [
            'choice_list[name]' => 'Progress'
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Progress', $crawler->filter('.list-element')->eq(0)->filter('td')->eq(0)->text());
    }

    public function test_can_edit_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $choiceList = ChoiceListFactory::createOne(['owner' => $user, 'choices' => ['New', 'Test' , 'Done']]);

        // Act
        $this->client->request('GET', '/choice-lists/'.$choiceList->getId().'/edit');
        $crawler = $this->client->submitForm('Submit', [
            'choice_list[name]' => 'Progress',
            'choice_list[choices][0]' => 'New',
            'choice_list[choices][1]' => 'In progress',
            'choice_list[choices][2]' => 'Done',
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Progress', $crawler->filter('.list-element')->eq(0)->filter('td')->eq(0)->text());
        $this->assertSame('New, In progress, Done', $crawler->filter('.list-element')->eq(0)->filter('td')->eq(1)->text());
    }

    public function test_can_delete_choice_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $choiceList = ChoiceListFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/choice-lists/');
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/choice-lists/'.$choiceList->getId().'/delete');
        $this->client->submitForm('Agree');

        // Assert
        $this->assertResponseIsSuccessful();
        ChoiceListFactory::assert()->count(0);
    }
}
