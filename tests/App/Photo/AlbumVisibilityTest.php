<?php

declare(strict_types=1);

namespace App\Tests\App\Photo;

use App\Tests\AppTestCase;
use App\Tests\Factory\AlbumFactory;
use App\Tests\Factory\PhotoFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AlbumVisibilityTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    #[TestWith(['public', 'public', 'public'])]
    #[TestWith(['public', 'private', 'private'])]
    #[TestWith(['public', 'internal', 'internal'])]

    #[TestWith(['internal', 'public', 'internal'])]
    #[TestWith(['internal', 'private', 'private'])]
    #[TestWith(['internal', 'internal', 'internal'])]

    #[TestWith(['private', 'public', 'private'])]
    #[TestWith(['private', 'private', 'private'])]
    #[TestWith(['private', 'internal', 'private'])]
    public function test_visibility_add_photo(string $album1Visibility, string $album2Visibility, string $photoFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $albumLevel1 = AlbumFactory::createOne(['owner' => $user, 'visibility' => $album1Visibility]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user, 'visibility' => $album2Visibility]);

        // Act
        $photo = PhotoFactory::createOne(['owner' => $user, 'album' => $albumLevel2]);

        // Assert
        PhotoFactory::assert()->exists(['id' => $photo->getId(), 'finalVisibility' => $photoFinalVisibility]);
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
    public function test_visibility_change_photo_album(string $album1Visibility, string $album2Visibility, string $photoFinalVisibility): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $oldAlbum = AlbumFactory::createOne(['owner' => $user]);
        $photo = PhotoFactory::createOne(['album' => $oldAlbum, 'owner' => $user]);

        $albumLevel1 = AlbumFactory::createOne(['owner' => $user, 'visibility' => $album1Visibility]);
        $albumLevel2 = AlbumFactory::createOne(['parent' => $albumLevel1, 'owner' => $user, 'visibility' => $album2Visibility]);

        // Act
        $photo->setAlbum($albumLevel2->object());
        $photo->save();

        // Assert
        PhotoFactory::assert()->exists(['id' => $photo->getId(), 'finalVisibility' => $photoFinalVisibility]);
    }
}
