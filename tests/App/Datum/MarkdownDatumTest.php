<?php

declare(strict_types=1);

namespace App\Tests\App\Datum;

use App\Enum\DatumTypeEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MarkdownDatumTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test_markdown_is_rendered_on_item_page(): void
    {
        // Act
        $crawler = $this->showItemWithMarkdown("**Bold** text\n\n- First\n- Second");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Bold', $crawler->filter('.datum-row .markdown strong')->text());
        $this->assertCount(2, $crawler->filter('.datum-row .markdown ul li'));
    }

    public function test_html_is_escaped_in_markdown(): void
    {
        // Act
        $crawler = $this->showItemWithMarkdown('<img src=x onerror=alert(1)><script>alert(2)</script>');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertCount(0, $crawler->filter('.markdown img'));
        $this->assertCount(0, $crawler->filter('.markdown script'));
        $this->assertStringContainsString('<img src=x onerror=alert(1)>', $crawler->filter('.markdown')->text());
    }

    public function test_unsafe_links_are_removed_from_markdown(): void
    {
        // Act
        $crawler = $this->showItemWithMarkdown('[Click](javascript:alert(1))');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('', (string) $crawler->filter('.markdown a')->attr('href'));
    }

    private function showItemWithMarkdown(string $value): Crawler
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $item = ItemFactory::createOne(['owner' => $user, 'collection' => $collection]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 1, 'type' => DatumTypeEnum::TYPE_MARKDOWN, 'label' => 'Notes', 'value' => $value]);

        return $this->client->request(Request::METHOD_GET, '/items/' . $item->getId());
    }
}
