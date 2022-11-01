<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\DateFormatEnum;
use App\Enum\LocaleEnum;
use App\Enum\VisibilityEnum;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SettingsTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_edit_settings(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/settings');

        $crawler = $this->client->submitForm('Submit', [
            'settings[locale]' => LocaleEnum::LOCALE_FR,
            'settings[currency]' => 'EUR',
            'settings[timezone]' => 'Europe/Paris',
            'settings[dateFormat]' => DateFormatEnum::FORMAT_SLASH_DMY,
            'settings[visibility]' => VisibilityEnum::VISIBILITY_INTERNAL,
            'settings[darkModeEnabled]' => 1,
            'settings[automaticDarkModeStartAt]' => '00:00',
            'settings[automaticDarkModeEndAt]' => '23:59',
            'settings[wishlistsFeatureEnabled]' => 1,
            'settings[tagsFeatureEnabled]' => 1,
            'settings[signsFeatureEnabled]' => 1,
            'settings[albumsFeatureEnabled]' => 1,
            'settings[loansFeatureEnabled]' => 1,
            'settings[templatesFeatureEnabled]' => 1,
            'settings[historyFeatureEnabled]' => 1,
            'settings[statisticsFeatureEnabled]' => 1,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Paramètres', $crawler->filter('h1')->text());
        UserFactory::assert()->exists([
            'id' => $user->getId(),
            'locale' => LocaleEnum::LOCALE_FR,
            'currency' => 'EUR',
            'timezone' => 'Europe/Paris',
            'dateFormat' => DateFormatEnum::FORMAT_SLASH_DMY,
            'visibility' => VisibilityEnum::VISIBILITY_INTERNAL,
            'darkModeEnabled' => true,
            'wishlistsFeatureEnabled' => true,
            'tagsFeatureEnabled' => true,
            'signsFeatureEnabled' => true,
            'albumsFeatureEnabled' => true,
            'loansFeatureEnabled' => true,
            'templatesFeatureEnabled' => true,
            'historyFeatureEnabled' => true,
            'statisticsFeatureEnabled' => true,
        ]);
    }
}
