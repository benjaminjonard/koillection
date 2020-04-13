<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Image;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DiskUsageCalculator
 *
 * @package App\Service
 */
class DiskUsageCalculator
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var string
     */
    private string $publicPath;

    /**
     * DiskUsageCalculator constructor.
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->em = $em;
        $this->publicPath = __DIR__ . '/../../public';
    }

    /**
     * @param User $user
     * @return int
     */
    public function getSpaceUsedByUser(User $user) : int
    {
        $size = 0;
        $images = $this->em->getRepository(Image::class)->findBy(['owner' => $user]);

        foreach ($images as $image) {
            $size += filesize($this->publicPath.'/'.$image->getPath());
            if ($image->getThumbnailPath()) {
                $size += filesize($this->publicPath.'/'.$image->getThumbnailPath());
            }
        }

        return $size;
    }
}
