<?php

declare(strict_types=1);

namespace App\Tests\App\Album;

use App\Enum\VisibilityEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\PhotoFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AlbumVisibilityTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    #[TestWith(['public', 'public', 'public'])]
    #[TestWith(['public', 'private', 'private'])]
    #[TestWith(['public', 'internal', 'internal'])]

    #[TestWith(['internal', 'public', 'internal'])]
    #[TestWith(['internal', 'private', 'private'])]
    #[TestWith(['internal', 'internal', 'internal'])]

    #[TestWith(['private', 'public', 'private'])]
    #[TestWith(['private', 'private', 'private'])]
    #[TestWith(['private', 'internal', 'private'])]
    public function test_visibility_add_nested_album(string $album1Visibility, string $album3Visibility, string $album2FinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user, 'visibility' => $album1Visibility]);

        // Act
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user, 'visibility' => $album3Visibility]);

        // Assert
        AlbumFactory::assert()->exists(['id' => $albumLevel2->getId(), 'finalVisibility' => $album2FinalVisibility]);
    }

    #[TestWith(['public', 'public', 'public'])]
    #[TestWith(['public', 'private', 'private'])]
    #[TestWith(['public', 'internal', 'internal'])]

    #[TestWith(['internal', 'public', 'internal'])]
    #[TestWith(['internal', 'private', 'private'])]
    #[TestWith(['internal', 'internal', 'internal'])]

    #[TestWith(['private', 'public', 'private'])]
    #[TestWith(['private', 'private', 'private'])]
    #[TestWith(['private', 'internal', 'private'])]
    public function test_visibility_change_parent_album(string $newAlbumVisibility, string $album2Visibility, string $album1FinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['parent' => null, 'owner' => $user]);

        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user, 'visibility' => $album2Visibility]);
        $photo2 = PhotoFactory::createOne(['album' => $albumLevel2, 'owner' => $user]);

        // Act
        $newParentAlbum = AlbumFactory::createOne(['owner' => $user, 'visibility' => $newAlbumVisibility]);
        $albumLevel2->setParent($newParentAlbum->object());
        $albumLevel2->save();

        // Assert
        AlbumFactory::assert()->exists(['id' => $albumLevel2->getId(), 'finalVisibility' => $album1FinalVisibility]);
        PhotoFactory::assert()->exists(['id' => $photo2->getId(), 'finalVisibility' => $album1FinalVisibility]);
    }

    #[TestWith(['public', 'public', 'public', 'public'])]
    #[TestWith(['public', 'private', 'public', 'private'])]
    #[TestWith(['public', 'internal', 'public', 'internal'])]

    #[TestWith(['internal', 'public', 'internal', 'internal'])]
    #[TestWith(['internal', 'private', 'internal', 'private'])]
    #[TestWith(['internal', 'internal', 'internal', 'internal'])]

    #[TestWith(['private', 'public', 'private', 'private'])]
    #[TestWith(['private', 'private', 'private', 'private'])]
    #[TestWith(['private', 'internal', 'private', 'private'])]
    public function test_visibility_change_album_visibility(string $album1Visibility, string $album2Visibility, string $level1Visibility, string $level2Visibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['parent' => null, 'owner' => $user]);
        $photo1 = PhotoFactory::createOne(['album' => $albumLevel1, 'owner' => $user]);

        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user, 'visibility' => $album2Visibility]);
        $photo2 = PhotoFactory::createOne(['album' => $albumLevel2, 'owner' => $user]);

        // Act
        $albumLevel1->setVisibility($album1Visibility);
        $albumLevel1->save();

        // Assert
        AlbumFactory::assert()->exists(['id' => $albumLevel1->getId(), 'finalVisibility' => $level1Visibility]);
        PhotoFactory::assert()->exists(['id' => $photo1->getId(), 'finalVisibility' => $level1Visibility]);
        AlbumFactory::assert()->exists(['id' => $albumLevel2->getId(), 'finalVisibility' => $level2Visibility]);
        PhotoFactory::assert()->exists(['id' => $photo2->getId(), 'finalVisibility' => $level2Visibility]);
    }

    public function test_shared_albums_list_with_anonymous(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        AlbumFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        AlbumFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        AlbumFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/albums");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Albums', $crawler->filter('h1')->text());
        $this->assertCount(1, $crawler->filter('.collection-element'));
    }

    public function test_shared_albums_list_with_user_logged(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        AlbumFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        AlbumFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        AlbumFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        $otherUser = UserFactory::createOne()->object();
        $this->client->loginUser($otherUser);

        // Act
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/albums");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Albums', $crawler->filter('h1')->text());
        $this->assertCount(2, $crawler->filter('.collection-element'));
    }

    public function test_albums_list_with_owner_logged(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        AlbumFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        AlbumFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        AlbumFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', "/albums");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Albums', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
    }

    #[TestWith(['public', true])]
    #[TestWith(['internal', false])]
    #[TestWith(['private', false])]
    public function test_shared_get_album_with_anonymous(string $visibility, bool $shouldSucceed): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user, 'visibility' => $visibility]);

        AlbumFactory::createOne(['owner' => $user, 'parent' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        AlbumFactory::createOne(['owner' => $user, 'parent' => $album, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        AlbumFactory::createOne(['owner' => $user, 'parent' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        PhotoFactory::createOne(['owner' => $user, 'album' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        PhotoFactory::createOne(['owner' => $user, 'album' => $album, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        PhotoFactory::createOne(['owner' => $user, 'album' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->request('GET', "/user/{$user->getUsername()}/albums"); //Don't know why it's needed, it seems like $album isn't properly initialized, maybe from some cache
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/albums/{$album->getId()}");

        // Assert
        if ($shouldSucceed) {
            $this->assertResponseIsSuccessful();
            $this->assertEquals($album->getTitle(), $crawler->filter('h1')->text());
            $this->assertCount(1, $crawler->filter('.collection-element'));
            $this->assertCount(1, $crawler->filter('.collection-item'));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    #[TestWith(['public', true])]
    #[TestWith(['internal', true])]
    #[TestWith(['private', false])]
    public function test_shared_get_album_with_other_user_logged(string $visibility, bool $shouldSucceed): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user, 'visibility' => $visibility]);

        AlbumFactory::createOne(['owner' => $user, 'parent' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        AlbumFactory::createOne(['owner' => $user, 'parent' => $album, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        AlbumFactory::createOne(['owner' => $user, 'parent' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        PhotoFactory::createOne(['owner' => $user, 'album' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        PhotoFactory::createOne(['owner' => $user, 'album' => $album, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        PhotoFactory::createOne(['owner' => $user, 'album' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $otherUser = UserFactory::createOne()->object();
        $this->client->loginUser($otherUser);
        $this->client->request('GET', "/user/{$user->getUsername()}/albums"); //Don't know why it's needed, it seems like $album isn't properly initialized, maybe from some cache
        $crawler = $this->client->request('GET', "/user/{$user->getUsername()}/albums/{$album->getId()}");

        // Assert
        if ($shouldSucceed) {
            $this->assertResponseIsSuccessful();
            $this->assertEquals($album->getTitle(), $crawler->filter('h1')->text());
            $this->assertCount(2, $crawler->filter('.collection-element'));
            $this->assertCount(2, $crawler->filter('.collection-item'));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    #[TestWith(['public'])]
    #[TestWith(['internal'])]
    #[TestWith(['private'])]
    public function test_get_album_with_owner_logged(string $visibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $album = AlbumFactory::createOne(['owner' => $user, 'visibility' => $visibility]);

        AlbumFactory::createOne(['owner' => $user, 'parent' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        AlbumFactory::createOne(['owner' => $user, 'parent' => $album, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        AlbumFactory::createOne(['owner' => $user, 'parent' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        PhotoFactory::createOne(['owner' => $user, 'album' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PUBLIC]);
        PhotoFactory::createOne(['owner' => $user, 'album' => $album, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);
        PhotoFactory::createOne(['owner' => $user, 'album' => $album, 'visibility' => VisibilityEnum::VISIBILITY_PRIVATE]);

        // Act
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', "/albums/{$album->getId()}");

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertEquals($album->getTitle(), $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-element'));
        $this->assertCount(3, $crawler->filter('.collection-item'));
    }
}
