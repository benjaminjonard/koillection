<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Field;
use App\Entity\Inventory;
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
use App\Service\InventoryHandler;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class CthulhuFixtures
 *
 * @package App\DataFixtures\ORM
 */
class CthulhuFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @var InventoryHandler
     */
    private $inventoryHandler;

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
        return 2;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $cthulhu = new User();
        $cthulhu
            ->setEnabled(true)
            ->setPlainPassword('testtest')
            ->setLocale('en')
            ->setUsername('Cthulhu')
            ->setEmail('cthulhu@koillection.com')
            ->setVisibility(VisibilityEnum::VISIBILITY_PUBLIC)
            ->setTimezone('Europe/Paris')
            ->addRole('ROLE_USER')
            ->setDiskSpaceUsed(0)
        ;

        $this->loadCollections($cthulhu, $manager);
        $this->loadWishlists($cthulhu, $manager);
        $this->loadAlbums($cthulhu, $manager);
        $this->loadTemplates($cthulhu, $manager);

        $manager->persist($cthulhu);

        $manager->flush();
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
    private function loadCollections(User $user, ObjectManager $manager)
    {
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
            ->setImage($this->loadMedium($user, $manager, 'cthulhu/collections/lovecraft/main.png'))
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
            ->setImage($this->loadMedium($user, $manager, 'cthulhu/collections/lovecraft/mythe.jpeg', 'cthulhu/collections/lovecraft/mythe_small.jpeg'))
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

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
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
            ->setImage($this->loadMedium($user, $manager, 'cthulhu/wishlists/figures/cthulhu.jpeg', 'cthulhu/wishlists/figures/cthulhu_small.jpeg'))
        ;
        $manager->persist($wishCthulhu);
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
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
            ->setImage($this->loadMedium($user, $manager, 'cthulhu/albums/rlyeh/rlyeh.jpeg', 'cthulhu/albums/rlyeh/rlyeh_small.jpeg'))
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
