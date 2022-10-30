<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\DatumTypeEnum;
use App\Tests\Factory\FieldFactory;
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
        $this->client->request('GET', '/templates/add');
        $crawler = $this->client->submitForm('Submit', [
            'template[name]' => 'Book'
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Book', $crawler->filter('h1')->text());
    }

    public function test_can_edit_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $template = TemplateFactory::createOne(['owner' => $user]);
        FieldFactory::createOne(['template' => $template, 'owner' => $user]);

        // Act
        $this->client->request('GET', '/templates/'.$template->getId().'/edit');
        $crawler = $this->client->submitForm('Submit', [
            'template[name]' => 'Book',
            'template[fields][0][name]' => 'Author',
            'template[fields][0][type]' => DatumTypeEnum::TYPE_TEXT,
            'template[fields][0][position]' => 1
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Book', $crawler->filter('h1')->text());
        $this->assertCount(1, $crawler->filter('tbody tr'));
    }

    public function test_can_delete_template(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $template = TemplateFactory::createOne(['owner' => $user]);
        FieldFactory::createMany(3, ['template' => $template, 'owner' => $user]);

        // Act
        $crawler = $this->client->request('GET', '/templates/'.$template->getId());
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/templates/'.$template->getId().'/delete');
        $this->client->submitForm('Agree');

        // Assert
        $this->assertResponseIsSuccessful();
        TemplateFactory::assert()->count(0);
        FieldFactory::assert()->count(0);
    }

    public function test_can_get_template_fields(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);
        $template = TemplateFactory::createOne(['owner' => $user]);
        FieldFactory::createOne(['name' => 'Author', 'type' => DatumTypeEnum::TYPE_TEXT, 'position' => 1, 'template' => $template, 'owner' => $user]);
        FieldFactory::createOne(['name' => 'Country', 'type' => DatumTypeEnum::TYPE_COUNTRY, 'position' => 2, 'template' => $template, 'owner' => $user]);
        FieldFactory::createOne(['name' => 'Pages', 'type' => DatumTypeEnum::TYPE_NUMBER, 'position' => 3, 'template' => $template, 'owner' => $user]);

        // Act
        $this->client->request('GET', '/templates/'.$template->getId().'/fields');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(3, $content);
        $this->assertSame('Author', $content[0][1]);
        $this->assertSame('Country', $content[1][1]);
        $this->assertSame('Pages', $content[2][1]);
    }
}
