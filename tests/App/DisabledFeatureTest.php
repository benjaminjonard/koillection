<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Entity\Configuration;
use App\Entity\User;
use App\Enum\ConfigurationEnum;
use App\Enum\ScraperTypeEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\ChoiceListFactory;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ScraperFactory;
use App\Tests\Factory\TagFactory;
use App\Tests\Factory\TemplateFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\Factory\WishlistFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DisabledFeatureTest extends AppTestCase
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

    public function test_disabled_features_not_in_menu(): void
    {
        // Arrange
        $user = UserFactory::createOne([
            'wishlistsFeatureEnabled' => false,
            'tagsFeatureEnabled' => false,
            'signsFeatureEnabled' => false,
            'albumsFeatureEnabled' => false,
            'loansFeatureEnabled' => false,
            'templatesFeatureEnabled' => false,
            'historyFeatureEnabled' => false,
            'statisticsFeatureEnabled' => false,
            'importFeatureEnabled' => false,
        ]);
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/');

        // Assert
        $this->assertStringContainsString('Collections', $crawler->filter('.nav-sidebar')->text());
        $this->assertStringNotContainsString('Choice lists', $crawler->filter('.nav-sidebar')->text());
        $this->assertStringNotContainsString('Wishlists', $crawler->filter('.nav-sidebar')->text());
        $this->assertStringNotContainsString('Tags', $crawler->filter('.nav-sidebar')->text());
        $this->assertStringNotContainsString('Signatures', $crawler->filter('.nav-sidebar')->text());
        $this->assertStringNotContainsString('Albums', $crawler->filter('.nav-sidebar')->text());
        $this->assertStringNotContainsString('Loans', $crawler->filter('.nav-sidebar')->text());
        $this->assertStringNotContainsString('Template', $crawler->filter('.nav-sidebar')->text());
        $this->assertStringNotContainsString('History', $crawler->filter('.nav-sidebar')->text());
        $this->assertStringNotContainsString('Statistics', $crawler->filter('.nav-sidebar')->text());
        $this->assertCount(0, $crawler->filter('a[href="/imports"]'));
    }

    public function test_owner_cant_access_disabled_features(): void
    {
        // Arrange
        $user = UserFactory::createOne([
            'wishlistsFeatureEnabled' => false,
            'tagsFeatureEnabled' => false,
            'signsFeatureEnabled' => false,
            'albumsFeatureEnabled' => false,
            'loansFeatureEnabled' => false,
            'templatesFeatureEnabled' => false,
            'historyFeatureEnabled' => false,
            'statisticsFeatureEnabled' => false,
            'scrapingFeatureEnabled' => false,
            'importFeatureEnabled' => false,
        ]);
        $this->client->loginUser($user);

        foreach ($this->getUrls($user) as $url) {
            // Act
            $this->client->request(Request::METHOD_GET, $url);

            // Assert
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    public function test_user_cant_access_features_disabled_on_instance(): void
    {
        // Arrange
        $this->setConfigurationValue(ConfigurationEnum::ENABLE_SCRAPING, '0');
        $this->setConfigurationValue(ConfigurationEnum::ENABLE_IMPORT, '0');
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $scraperId = ScraperFactory::createOne(['owner' => $user])->getId();
        $collectionId = CollectionFactory::createOne(['owner' => $user])->getId();

        foreach (['/scrapers/item-scrapers', "/scrapers/item-scrapers/{$scraperId}", '/imports', "/collections/{$collectionId}/import"] as $url) {
            // Act
            $this->client->request(Request::METHOD_GET, $url);

            // Assert
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    public function test_features_disabled_on_instance_are_hidden(): void
    {
        // Arrange
        $this->setConfigurationValue(ConfigurationEnum::ENABLE_SCRAPING, '0');
        $this->setConfigurationValue(ConfigurationEnum::ENABLE_IMPORT, '0');
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/settings');

        // Assert
        $this->assertStringNotContainsString('Scrapers', $crawler->filter('.nav-sidebar')->text());
        $this->assertCount(0, $crawler->filter('a[href="/imports"]'));
        $this->assertSame('disabled', $crawler->filter('#settings_scrapingFeatureEnabled')->attr('disabled'));
        $this->assertSame('disabled', $crawler->filter('#settings_importFeatureEnabled')->attr('disabled'));
    }

    public function test_settings_keep_value_of_features_disabled_on_instance(): void
    {
        // Arrange
        $this->setConfigurationValue(ConfigurationEnum::ENABLE_IMPORT, '0');
        $user = UserFactory::createOne(['importFeatureEnabled' => true]);
        $this->client->loginUser($user);

        // Act
        $this->client->request(Request::METHOD_GET, '/settings');
        $this->client->submitForm('Submit');

        // Assert
        $this->assertResponseIsSuccessful();
        UserFactory::assert()->exists(['id' => $user->getId(), 'importFeatureEnabled' => true]);
    }

    private function setConfigurationValue(string $label, ?string $value): void
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->getRepository(Configuration::class)->findOneBy(['label' => $label])->setValue($value);
        $entityManager->flush();
    }

    private function getUrls(User $owner): array
    {
        $albumId = AlbumFactory::createOne(['owner' => $owner])->getId();
        $wishlistId = WishlistFactory::createOne(['owner' => $owner])->getId();
        $tagId = TagFactory::createOne(['owner' => $owner])->getId();
        $templateId = TemplateFactory::createOne(['owner' => $owner])->getId();
        $choiceListId = ChoiceListFactory::createOne(['owner' => $owner])->getId();
        $itemScraperId = ScraperFactory::createOne(['owner' => $owner, 'type' => ScraperTypeEnum::TYPE_ITEM])->getId();
        $collectionScraperId = ScraperFactory::createOne(['owner' => $owner, 'type' => ScraperTypeEnum::TYPE_COLLECTION])->getId();
        $wishScraperId = ScraperFactory::createOne(['owner' => $owner, 'type' => ScraperTypeEnum::TYPE_WISH])->getId();
        $collectionId = CollectionFactory::createOne(['owner' => $owner])->getId();

        return [
            '/albums',
            "/albums/{$albumId}",
            '/wishlists',
            "/wishlists/{$wishlistId}",
            '/tags',
            "/tags/{$tagId}",
            '/templates',
            "/templates/{$templateId}",
            '/choice-lists',
            "/choice-lists/{$choiceListId}/edit",
            '/scrapers/item-scrapers',
            "/scrapers/item-scrapers/{$itemScraperId}",
            '/scrapers/collection-scrapers',
            "/scrapers/collection-scrapers/{$collectionScraperId}",
            '/scrapers/wish-scrapers',
            "/scrapers/wish-scrapers/{$wishScraperId}",
            '/loans',
            '/signatures',
            '/statistics',
            '/imports',
            "/collections/{$collectionId}/import",
        ];
    }
}
