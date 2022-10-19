<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Tests\Factory\TemplateFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TemplateTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_see_template_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request('GET', '/templates');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Templates', $crawler->filter('h1')->text());
    }

    public function test_can_get_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $template = TemplateFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/templates/'.$template->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame($template->getName(), $crawler->filter('h1')->text());
    }

    public function test_can_add_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request('GET', '/templates/add');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Add a new template', $crawler->filter('h1')->text());
    }

    public function test_can_edit_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $template = TemplateFactory::createOne(['owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/templates/'.$template->getId().'/edit');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Edit template '.$template->getName(), $crawler->filter('h1')->text());
    }
}
