<?php

declare(strict_types=1);

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
     * @var string
     */
    protected $publicPath;

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
        $this->publicPath = __DIR__.'/../../public';
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

        $path = 'uploads/'.$this->tokenStorage->getToken()->getUser()->getId().'/';
        $generatedName = $this->rsg->generateString(20);
        $extension = $medium->getUploadedFile()->guessExtension();

        $medium
            ->setPath($path.$generatedName.'.'.$extension)
            ->setMimetype($medium->getUploadedFile()->getMimeType())
            ->setFilename($generatedName.'.'.$extension)
            ->setType(Medium::TYPE_IMAGE)
        ;

        $medium->getUploadedFile()->move($this->publicPath.'/'.$path, $medium->getPath());
        $medium->setSize(filesize($this->publicPath.'/'.$medium->getPath()));
        $sizeUsed += $medium->getSize();

        if ($medium->getMustGenerateAThumbnail()) {
            $medium->setThumbnailPath($path.$generatedName.'_small.'.$extension);
            $this->tg->generateThumbnail($this->publicPath.'/'.$medium->getPath(), $this->publicPath.'/'.$medium->getThumbnailPath());
            $medium->setThumbnailSize(filesize($this->publicPath.'/'.$medium->getThumbnailPath()));
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


        unlink($this->publicPath.'/'.$medium->getPath());
        if ($medium->getThumbnailPath()) {
            $sizeFreed += $medium->getThumbnailSize();
            unlink($this->publicPath.'/'.$medium->getThumbnailPath());
        }

        $dir = rtrim($this->publicPath.'/'.$medium->getPath(), basename($medium->getFilename()));

        if (\count(glob("$dir/*")) === 0) {
            rmdir($dir);
        }

        return $sizeFreed;
    }
}
