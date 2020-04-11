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
use App\Entity\TagCategory;
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
 * Class KhorneFixtures
 *
 * @package App\DataFixtures\ORM
 */
class KhorneFixtures extends Fixture implements OrderedFixtureInterface
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
        return 1;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin
            ->setEnabled(true)
            ->setPlainPassword('testtest')
            ->setLocale('en')
            ->setUsername('Khorne')
            ->setEmail('khorne@koillection.com')
            ->setVisibility(VisibilityEnum::VISIBILITY_PUBLIC)
            ->setTimezone('Europe/Paris')
            ->addRole('ROLE_ADMIN')
            ->setDiskSpaceUsed(0)
        ;

        $this->loadCollections($admin, $manager);
        $this->loadWishlists($admin, $manager);
        $this->loadAlbums($admin, $manager);
        $this->loadTemplates($admin, $manager);

        $manager->persist($admin);

        $manager->flush();
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
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
            ->setImage($this->loadMedium($user, $manager, 'khorne/collections/magdala/main.png'))
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
                ->setImage($this->loadMedium($user, $manager, 'khorne/collections/magdala/'.$i.'.jpeg', 'khorne/collections/magdala/'.$i.'_small.jpeg'))
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

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
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
            ->setWishlist($wishlistProxy)
            ->setImage($this->loadMedium($user, $manager, 'khorne/wishlists/proxy/danboard.jpeg', 'khorne/wishlists/proxy/danboard_small.jpeg'))
        ;
        $manager->persist($wishDanboard);
    }

    /**
     * @param User $user
     * @param ObjectManager $manager
     */
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
            ->setImage($this->loadMedium($user, $manager, 'khorne/albums/saint-maur/saint-maur.jpeg', 'khorne/albums/saint-maur/saint-maur_small.jpeg'))
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
