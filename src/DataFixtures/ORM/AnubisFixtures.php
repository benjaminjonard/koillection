<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Field;
use App\Entity\Item;
use App\Entity\Medium;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\Template;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class AnubisFixtures
 *
 * @package App\DataFixtures\ORM
 */
class AnubisFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 3;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $anubis = new User();
        $anubis
            ->setEnabled(true)
            ->setPlainPassword('testtest')
            ->setLocale('en')
            ->setUsername('Anubis')
            ->setEmail('anubis@koillection.com')
            ->setVisibility(VisibilityEnum::VISIBILITY_PUBLIC)
            ->setTimezone('Europe/Paris')
            ->addRole('ROLE_USER')
            ->setDiskSpaceUsed(0)
        ;

        $this->loadCollections($anubis, $manager);
        $this->loadWishlists($anubis, $manager);
        $this->loadAlbums($anubis, $manager);
        $this->loadTemplates($anubis, $manager);

        $manager->persist($anubis);

        $manager->flush();
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
    private function loadCollections(User $user, ObjectManager $manager)
    {
        //TAGS
        $tagVideoGames = new Tag();
        $tagVideoGames
            ->setOwner($user)
            ->setLabel('Video Games')
            ->setSeenCounter(0)
        ;
        $manager->persist($tagVideoGames);

        //COLLECTIONS
        $colletionVideoGames = new Collection();
        $colletionVideoGames
            ->setOwner($user)
            ->setTitle('Video Games')
            ->setSeenCounter(0)
        ;
        $manager->persist($colletionVideoGames);

        //ITEMS
        $itemJourney = new Item();
        $itemJourney
            ->setOwner($user)
            ->setName('Journey')
            ->setCollection($colletionVideoGames)
            ->setImage($this->loadMedium($user, $manager, 'anubis/collections/videogames/journey.jpeg', 'anubis/collections/videogames/journey_small.jpeg'))
            ->addTag($tagVideoGames)
            ->setSeenCounter(0)
        ;
        $manager->persist($itemJourney);
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
    private function loadWishlists(User $user, ObjectManager $manager)
    {
        //WISHLIST
        $wishlistPlushes = new Wishlist();
        $wishlistPlushes
            ->setOwner($user)
            ->setName('Plushes')
            ->setSeenCounter(0)
        ;
        $manager->persist($wishlistPlushes);

        //WISH
        $wishAnubis = new Wish();
        $wishAnubis
            ->setOwner($user)
            ->setName('Cthulhu Figure')
            ->setWishlist($wishlistPlushes)
            ->setImage($this->loadMedium($user, $manager, 'anubis/wishlists/plushes/anubis.jpeg', 'anubis/wishlists/plushes/anubis_small.jpeg'))
        ;
        $manager->persist($wishAnubis);
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
    private function loadAlbums(User $user, ObjectManager $manager)
    {
        //ALBUM
        $albumUnderworld = new Album();
        $albumUnderworld
            ->setOwner($user)
            ->setTitle('The underworld')
            ->setSeenCounter(0)
        ;
        $manager->persist($albumUnderworld);

        //Photo
        $photo1 = new Photo();
        $photo1
            ->setOwner($user)
            ->setTitle('Photo 1')
            ->setAlbum($albumUnderworld)
            ->setImage($this->loadMedium($user, $manager, 'anubis/albums/underworld/underworld.jpeg', 'anubis/albums/underworld/underworld_small.jpeg'))
        ;
        $manager->persist($photo1);
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
    private function loadTemplates(User $user, ObjectManager $manager)
    {
        //TEMPLATE
        $templateVideoGame = new Template();
        $templateVideoGame
            ->setOwner($user)
            ->setName('Video games')
        ;
        $manager->persist($templateVideoGame);

        //Fields
        $fieldStudio = new Field();
        $fieldStudio
            ->setName('Studio')
            ->setTemplate($templateVideoGame)
            ->setType(DatumTypeEnum::TYPE_TEXT)
            ->setPosition(0)
        ;
        $manager->persist($fieldStudio);
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     * @param string $path
     * @param null|string $thumbnailPath
     * @return Medium
     */
    private function loadMedium(User $user, ObjectManager $manager, string $path, ?string $thumbnailPath = null) : Medium
    {
        $file = new File('public/fixtures/'.$path);
        $medium = new Medium();
        $medium
            ->setOwner($user)
            ->setFilename($path)
            ->setMimetype($file->getMimeType())
            ->setPath('fixtures/'.$path)
            ->setSize($file->getSize())
        ;

        if ($thumbnailPath) {
            $thumbnailFile = new File('public/fixtures/'.$thumbnailPath);
            $medium
                ->setThumbnailPath('fixtures/'.$thumbnailPath)
                ->setThumbnailSize($thumbnailFile->getSize())
            ;
        }

        $manager->persist($medium);

        return $medium;
    }
}
