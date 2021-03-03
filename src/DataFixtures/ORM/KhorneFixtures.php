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
use App\Entity\TagCategory;
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

class KhorneFixtures extends Fixture implements OrderedFixtureInterface
{
    private InventoryHandler $inventoryHandler;

    public function __construct(InventoryHandler $inventoryHandler)
    {
        $this->inventoryHandler = $inventoryHandler;
    }

    public function getOrder(): int
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin
            ->setEnabled(true)
            ->setPlainPassword('testtest')
            ->setLocale(LocaleEnum::LOCALE_EN_GB)
            ->setUsername('Khorne')
            ->setEmail('khorne@koillection.com')
            ->setVisibility(VisibilityEnum::VISIBILITY_PUBLIC)
            ->setTimezone('Europe/Paris')
            ->addRole('ROLE_ADMIN')
        ;

        $this->loadCollections($admin, $manager);
        $this->loadWishlists($admin, $manager);
        $this->loadAlbums($admin, $manager);
        $this->loadTemplates($admin, $manager);

        $manager->persist($admin);

        $manager->flush();
    }

    private function loadCollections(User $user, ObjectManager $manager)
    {
        //TAGS CATEGORY
        $categorySerie = new TagCategory();
        $categorySerie
            ->setOwner($user)
            ->setLabel('Serie')
            ->setDescription('Book serie')
            ->setColor('#3232a8')
        ;

        //TAGS
        $tagManga = new Tag();
        $tagManga
            ->setOwner($user)
            ->setLabel('Manga')
            ->setSeenCounter(0)
        ;
        $manager->persist($tagManga);

        $tagMagdala = new Tag();
        $tagMagdala
            ->setOwner($user)
            ->setLabel('Magdala Alchemist Path')
            ->setSeenCounter(0)
            ->setCategory($categorySerie)
        ;
        $manager->persist($tagMagdala);

        //COLLECTIONS
        $collectionManga = new Collection();
        $collectionManga
            ->setOwner($user)
            ->setTitle('Manga')
            ->setSeenCounter(0)
        ;
        $manager->persist($collectionManga);

        $collectionMagdala = new Collection();
        $collectionMagdala
            ->setOwner($user)
            ->setTitle('Magdala, Alchemist Path')
            ->setParent($collectionManga)
            ->setImage('khorne/collections/magdala/main.png')
            ->setSeenCounter(0)
        ;
        $collectionManga->addChild($collectionMagdala);
        $manager->persist($collectionMagdala);

        //ITEMS
        for ($i = 1; $i <= 4; $i++) {
            $itemMagdala = new Item();
            $itemMagdala
                ->setOwner($user)
                ->setName('Magdala, Alchemist Path #'.$i)
                ->setCollection($collectionMagdala)
                ->setImage('khorne/collections/magdala/'.$i.'.jpeg')
                ->setImageSmallThumbnail( 'khorne/collections/magdala/'.$i.'_small.jpeg')
                ->addTag($tagManga)
                ->addTag($tagMagdala)
                ->setSeenCounter(0)
            ;
            $manager->persist($itemMagdala);
            $collectionMagdala->addItem($itemMagdala);
        }

        //Inventory
        $ids = [];
        $ids[] = $collectionManga->getId();
        $ids[] = $collectionMagdala->getId();

        $content = $this->inventoryHandler->buildInventory([$collectionManga, $collectionMagdala], $ids);
        $inventory = new Inventory();
        $inventory
            ->setName('Inventory')
            ->setOwner($user)
            ->setContent(json_encode($content))
        ;

        $manager->persist($inventory);
    }

    private function loadWishlists(User $user, ObjectManager $manager)
    {
        //WISHLIST
        $wishlistProxy = new Wishlist();
        $wishlistProxy
            ->setOwner($user)
            ->setName('Proxy')
            ->setSeenCounter(0)
        ;
        $manager->persist($wishlistProxy);

        //WISH
        $wishDanboard = new Wish();
        $wishDanboard
            ->setOwner($user)
            ->setName('Danboard')
            ->setCurrency('EUR')
            ->setWishlist($wishlistProxy)
            ->setImage('khorne/wishlists/proxy/danboard.jpeg')
            ->setImageSmallThumbnail( 'khorne/wishlists/proxy/danboard_small.jpeg')
        ;
        $manager->persist($wishDanboard);
    }

    private function loadAlbums(User $user, ObjectManager $manager)
    {
        //ALBUM
        $albumSaintMaur = new Album();
        $albumSaintMaur
            ->setOwner($user)
            ->setTitle('Saint-Maur')
            ->setSeenCounter(0)
        ;
        $manager->persist($albumSaintMaur);

        //Photo
        $photo1 = new Photo();
        $photo1
            ->setOwner($user)
            ->setTitle('Photo 1')
            ->setAlbum($albumSaintMaur)
            ->setImage('khorne/albums/saint-maur/saint-maur.jpeg')
            ->setImageSmallThumbnail( 'khorne/albums/saint-maur/saint-maur_small.jpeg')
        ;
        $manager->persist($photo1);
    }

    private function loadTemplates(User $user, ObjectManager $manager)
    {
        //TEMPLATE
        $templateManga = new Template();
        $templateManga
            ->setOwner($user)
            ->setName('Manga')
        ;
        $manager->persist($templateManga);

        //Fields
        $fieldAuthor = new Field();
        $fieldAuthor
            ->setName('Author')
            ->setTemplate($templateManga)
            ->setType(DatumTypeEnum::TYPE_TEXT)
            ->setPosition(0)
        ;
        $manager->persist($fieldAuthor);
    }
}
