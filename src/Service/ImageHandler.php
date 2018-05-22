<?php

namespace App\Service;

use App\Entity\Medium;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Datum;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ImageHandler
 *
 * @package App\Service
 */
class ImageHandler
{
    /**
     * @var RandomStringGenerator
     */
    protected $rsg;

    /**
     * @var ThumbnailGenerator
     */
    protected $tg;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * ImageHandler constructor.
     * @param RandomStringGenerator $rsg
     * @param ThumbnailGenerator $tg
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(RandomStringGenerator $rsg, ThumbnailGenerator $tg, TokenStorageInterface $tokenStorage)
    {
        $this->rsg = $rsg;
        $this->tg = $tg;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Medium $medium
     * @return int
     */
    public function upload(Medium $medium) : int
    {
        $sizeUsed = 0;
        if ($medium->getUploadedFile() === null) {
            return $sizeUsed;
        }

        $path = 'uploads/'.$this->rsg->generateString(5).'/';
        $generatedName = $this->rsg->generateString(12);
        $extension = $medium->getUploadedFile()->guessExtension();

        $medium
            ->setPath($path.$generatedName.'.'.$extension)
            ->setMimetype($medium->getUploadedFile()->getMimeType())
            ->setFilename($generatedName.'.'.$extension)
            ->setType(Medium::TYPE_IMAGE)
        ;

        $medium->getUploadedFile()->move($path, $medium->getPath());
        $medium->setSize(filesize($medium->getPath()));
        $sizeUsed += $medium->getSize();

        if ($medium->getMustGenerateAThumbnail()) {
            $medium->setThumbnailPath($path.'/'.$generatedName.'_small.'.$extension);
            $this->tg->generateThumbnail($medium->getPath(), $medium->getThumbnailPath());
            $medium->setThumbnailSize(filesize($medium->getThumbnailPath()));
            $sizeUsed += $medium->getThumbnailSize();
        }

        $medium->setUploadedFile(null);

        return $sizeUsed;
    }

    /**
     * @param Medium $medium
     * @return int
     */
    public function remove(Medium $medium) : int
    {
        $sizeFreed = 0;

        $sizeFreed += $medium->getSize();


        unlink($medium->getPath());
        if ($medium->getThumbnailPath()) {
            $sizeFreed += $medium->getThumbnailSize();
            unlink($medium->getThumbnailPath());
        }

        $dir = rtrim($medium->getPath(), basename($medium->getFilename()));

        if (\count(glob("$dir/*")) === 0) {
            rmdir($dir);
        }

        return $sizeFreed;
    }
}
