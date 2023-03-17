<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\User;
use App\Enum\DateFormatEnum;
use App\Enum\LocaleEnum;
use App\Enum\RoleEnum;
use App\Enum\ThemeEnum;
use App\Enum\VisibilityEnum;
use App\Repository\UserRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method static User|Proxy                     createOne(array $attributes = [])
 * @method static User[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[]                 createSequence(array|callable $sequence)
 * @method static User|Proxy                     find(object|array|mixed $criteria)
 * @method static User|Proxy                     findOrCreate(array $attributes)
 * @method static User|Proxy                     first(string $sortedField = 'id')
 * @method static User|Proxy                     last(string $sortedField = 'id')
 * @method static User|Proxy                     random(array $attributes = [])
 * @method static User|Proxy                     randomOrCreate(array $attributes = [])
 * @method static User[]|Proxy[]                 all()
 * @method static User[]|Proxy[]                 findBy(array $attributes)
 * @method static User[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 * @method static User[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method        User|Proxy                     create(array|callable $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'username' => self::faker()->unique()->word(),
            'email' => self::faker()->unique()->email(),
            'plainPassword' => self::faker()->password(),
            'enabled' => true,
            'roles' => [RoleEnum::ROLE_USER],
            'currency' => self::faker()->currencyCode(),
            'locale' => LocaleEnum::LOCALE_EN,
            'timezone' => self::faker()->timezone(),
            'dateFormat' => DateFormatEnum::FORMAT_HYPHEN_YMD,
            'diskSpaceAllowed' => 536870912,
            'visibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'theme' => ThemeEnum::THEME_BROWSER,
            'wishlistsFeatureEnabled' => true,
            'tagsFeatureEnabled' => true,
            'signsFeatureEnabled' => true,
            'albumsFeatureEnabled' => true,
            'loansFeatureEnabled' => true,
            'templatesFeatureEnabled' => true,
            'historyFeatureEnabled' => true,
            'statisticsFeatureEnabled' => true,
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    protected function initialize(): self
    {
        return $this;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
