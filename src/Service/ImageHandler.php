<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Image;
use App\Enum\ImageTypeEnum;
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
    private RandomStringGenerator $rsg;

    /**
     * @var ThumbnailGenerator
     */
    private ThumbnailGenerator $tg;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    /**
     * @var string
     */
    private string $publicPath;

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
        $this->publicPath = __DIR__ . '/../../public';
    }

    /**
     * @param Image $image
     * @return int
     * @throws \Exception
     */
    public function upload(Image $image) : int
    {
        $sizeUsed = 0;
        if ($image->getUploadedFile() === null) {
            return $sizeUsed;
        }

        $path = 'uploads/'.$this->tokenStorage->getToken()->getUser()->getId().'/';
        $generatedName = $this->rsg->generateString(20);
        $extension = $image->getUploadedFile()->guessExtension();

        $image
            ->setPath($path.$generatedName.'.'.$extension)
            ->setMimetype($image->getUploadedFile()->getMimeType())
            ->setFilename($generatedName.'.'.$extension)
        ;

        $image->getUploadedFile()->move($this->publicPath.'/'.$path, $image->getPath());
        $image->setSize(filesize($this->publicPath.'/'.$image->getPath()));
        $sizeUsed += $image->getSize();

        if ($image->getType() === ImageTypeEnum::TYPE_COMMON) {
            $image->setThumbnailPath($path.$generatedName.'_small.'.$extension);
            $this->tg->generateThumbnail($this->publicPath.'/'.$image->getPath(), $this->publicPath.'/'.$image->getThumbnailPath(), 150);
            $image->setThumbnailSize(filesize($this->publicPath.'/'.$image->getThumbnailPath()));
            $sizeUsed += $image->getThumbnailSize();
        }

        $image->setUploadedFile(null);

        return $sizeUsed;
    }

    /**
     * @param Image $image
     * @return int
     */
    public function remove(Image $image) : int
    {
        $sizeFreed = 0;
        $sizeFreed += $image->getSize();

        if (file_exists($this->publicPath.'/'.$image->getPath())) {
            unlink($this->publicPath.'/'.$image->getPath());
        }

        if ($image->getThumbnailPath()) {
            $sizeFreed += $image->getThumbnailSize();
            if (file_exists($this->publicPath.'/'.$image->getThumbnailPath())) {
                unlink($this->publicPath.'/'.$image->getThumbnailPath());
            }
        }

        $dir = rtrim($this->publicPath.'/'.$image->getPath(), basename($image->getFilename()));

        if (\count(glob("$dir/*")) === 0) {
            rmdir($dir);
        }

        return $sizeFreed;
    }
}
