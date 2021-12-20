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

class CthulhuFixtures extends Fixture implements OrderedFixtureInterface
{
    public function __construct(
        private InventoryHandler $inventoryHandler
    ) {}

    public function getOrder(): int
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $cthulhu = new User();
        $cthulhu
            ->setEnabled(true)
            ->setPlainPassword('testtest')
            ->setLocale(LocaleEnum::LOCALE_EN_GB)
            ->setUsername('Cthulhu')
            ->setEmail('cthulhu@koillection.com')
            ->setVisibility(VisibilityEnum::VISIBILITY_PUBLIC)
            ->setTimezone('Europe/Paris')
            ->addRole('ROLE_USER')
        ;

        $this->loadCollections($cthulhu, $manager);
        $this->loadWishlists($cthulhu, $manager);
        $this->loadAlbums($cthulhu, $manager);
        $this->loadTemplates($cthulhu, $manager);

        $manager->persist($cthulhu);

        $manager->flush();
    }

    private function loadCollections(User $user, ObjectManager $manager)
    {
        //TAGS CATEGORY
        $categoryAuthor = new TagCategory();
        $categoryAuthor
            ->setOwner($user)
            ->setLabel('Author')
            ->setDescription('Book author')
            ->setColor('#32a852')
        ;

        //TAGS
        $tagBooks = new Tag();
        $tagBooks
            ->setOwner($user)
            ->setLabel('Books')
            ->setSeenCounter(0)
        ;
        $manager->persist($tagBooks);

        $tagLovecraft = new Tag();
        $tagLovecraft
            ->setOwner($user)
            ->setLabel('H.P. Lovecraft')
            ->setSeenCounter(0)
            ->setCategory($categoryAuthor)
        ;
        $manager->persist($tagLovecraft);

        //COLLECTIONS
        $colletionBooks = new Collection();
        $colletionBooks
            ->setOwner($user)
            ->setTitle('Books')
            ->setSeenCounter(0)
        ;
        $manager->persist($colletionBooks);

        $collectionLovecraft = new Collection();
        $collectionLovecraft
            ->setOwner($user)
            ->setTitle('H.P. Lovecraft')
            ->setParent($colletionBooks)
            ->setImage('cthulhu/collections/lovecraft/main.png')
            ->setSeenCounter(0)
        ;
        $colletionBooks->addChild($collectionLovecraft);
        $manager->persist($collectionLovecraft);

        //ITEMS
        $itemCthulhu = new Item();
        $itemCthulhu
            ->setOwner($user)
            ->setName('Le mythe de Cthulhu')
            ->setCollection($collectionLovecraft)
            ->setImage('cthulhu/collections/lovecraft/mythe.jpeg')
            ->setImageSmallThumbnail( 'cthulhu/collections/lovecraft/mythe_small.jpeg')
            ->addTag($tagBooks)
            ->addTag($tagLovecraft)
            ->setSeenCounter(0)
        ;
        $manager->persist($itemCthulhu);
        $collectionLovecraft->addItem($itemCthulhu);

        //Inventory
        $ids = [];
        $ids[] = $colletionBooks->getId();
        $ids[] = $collectionLovecraft->getId();

        $content = $this->inventoryHandler->buildInventory([$colletionBooks, $collectionLovecraft], $ids);
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
        $wishlistFigure = new Wishlist();
        $wishlistFigure
            ->setOwner($user)
            ->setName('Figures')
            ->setSeenCounter(0)
        ;
        $manager->persist($wishlistFigure);

        //WISH
        $wishCthulhu = new Wish();
        $wishCthulhu
            ->setOwner($user)
            ->setName('Cthulhu Figure')
            ->setWishlist($wishlistFigure)
            ->setCurrency('EUR')
            ->setImage('cthulhu/wishlists/figures/cthulhu.jpeg')
            ->setImageSmallThumbnail( 'cthulhu/wishlists/figures/cthulhu_small.jpeg')
        ;
        $manager->persist($wishCthulhu);
    }

    private function loadAlbums(User $user, ObjectManager $manager)
    {
        //ALBUM
        $albumRlyeh = new Album();
        $albumRlyeh
            ->setOwner($user)
            ->setTitle('R\'lyeh')
            ->setSeenCounter(0)
        ;
        $manager->persist($albumRlyeh);

        //Photo
        $photo1 = new Photo();
        $photo1
            ->setOwner($user)
            ->setTitle('Photo 1')
            ->setAlbum($albumRlyeh)
            ->setImage('cthulhu/albums/rlyeh/rlyeh.jpeg')
            ->setImageSmallThumbnail( 'cthulhu/albums/rlyeh/rlyeh_small.jpeg')
        ;
        $manager->persist($photo1);
    }

    private function loadTemplates(User $user, ObjectManager $manager)
    {
        //TEMPLATE
        $templateBooks = new Template();
        $templateBooks
            ->setOwner($user)
            ->setName('Books')
        ;
        $manager->persist($templateBooks);

        //Fields
        $fieldAuthor = new Field();
        $fieldAuthor
            ->setName('Author')
            ->setTemplate($templateBooks)
            ->setType(DatumTypeEnum::TYPE_TEXT)
            ->setPosition(0)
        ;
        $manager->persist($fieldAuthor);
    }
}
