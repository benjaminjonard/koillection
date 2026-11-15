<?php

declare(strict_types=1);

namespace App\Tests\App\Item;

use App\Enum\VisibilityEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ItemDescriptionTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_edit_item_description(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'name' => 'Frieren #1']);

        // Act
        $this->client->request(Request::METHOD_GET, '/items/' . $item->getId() . '/edit');
        $this->client->submitForm('Submit', [
            'item[name]' => 'Frieren #1',
            'item[collection]' => $collection->getId(),
            'item[quantity]' => 1,
            'item[visibility]' => VisibilityEnum::VISIBILITY_PUBLIC,
            'item[description]' => '**Bold** description',
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        ItemFactory::assert()->exists(['id' => $item->getId(), 'description' => '**Bold** description']);
    }

    public function test_description_is_rendered_as_markdown(): void
    {
        // Act
        $crawler = $this->showItemWithDescription("**Bold** text\n\n- First\n- Second\n\n| A | B |\n| - | - |\n| 1 | 2 |");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Bold', $crawler->filter('.markdown strong')->text());
        $this->assertCount(2, $crawler->filter('.markdown ul li'));
        $this->assertCount(1, $crawler->filter('.markdown table'));
    }

    public function test_html_is_escaped_in_description(): void
    {
        // Act
        $crawler = $this->showItemWithDescription('<img src=x onerror=alert(1)><script>alert(2)</script>');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(0, $crawler->filter('.markdown img'));
        $this->assertCount(0, $crawler->filter('.markdown script'));
        $this->assertStringContainsString('<img src=x onerror=alert(1)>', $crawler->filter('.markdown')->text());
    }

    public function test_unsafe_links_are_removed_from_description(): void
    {
        // Act
        $crawler = $this->showItemWithDescription('[Click](javascript:alert(1))');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('', (string) $crawler->filter('.markdown a')->attr('href'));
    }

    public function test_no_description_block_without_description(): void
    {
        // Act
        $crawler = $this->showItemWithDescription(null);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(0, $crawler->filter('.markdown-block'));
    }

    private function showItemWithDescription(?string $description): Crawler
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['owner' => $user, 'collection' => $collection, 'description' => $description]);

        return $this->client->request(Request::METHOD_GET, '/items/' . $item->getId());
    }
}
