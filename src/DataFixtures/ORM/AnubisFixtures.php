<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Field;
use App\Entity\Inventory;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\Template;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\DatumTypeEnum;
use App\Enum\LocaleEnum;
use App\Enum\VisibilityEnum;
use App\Service\InventoryHandler;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

class AnubisFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @var InventoryHandler
     */
    private InventoryHandler $inventoryHandler;

    /**
     * AnubisFixtures constructor.
     * @param InventoryHandler $inventoryHandler
     */
    public function __construct(InventoryHandler $inventoryHandler)
    {
        $this->inventoryHandler = $inventoryHandler;
    }

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
            ->setLocale(LocaleEnum::LOCALE_EN_GB)
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
            ->setImage('anubis/collections/videogames/journey.jpeg')
            ->setImageSmallThumbnail( 'anubis/collections/videogames/journey_small.jpeg')
            ->setImageMediumThumbnail( 'anubis/collections/videogames/journey_small.jpeg')
            ->addTag($tagVideoGames)
            ->setSeenCounter(0)
        ;
        $manager->persist($itemJourney);
        $colletionVideoGames->addItem($itemJourney);

        //Inventory
        $ids = [];
        $ids[] = $colletionVideoGames->getId();

        $content = $this->inventoryHandler->buildInventory([$colletionVideoGames], $ids);
        $inventory = new Inventory();
        $inventory
            ->setName('Inventory')
            ->setOwner($user)
            ->setContent(json_encode($content))
        ;

        $manager->persist($inventory);
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
            ->setCurrency('EUR')
            ->setWishlist($wishlistPlushes)
            ->setImage('anubis/wishlists/plushes/anubis.jpeg')
            ->setImageSmallThumbnail( 'anubis/wishlists/plushes/anubis_small.jpeg')
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
            ->setImage('anubis/albums/underworld/underworld.jpeg')
            ->setImageSmallThumbnail( 'anubis/albums/underworld/underworld_small.jpeg')
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
}
