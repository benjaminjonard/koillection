<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @param string $publicPath
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em, string $publicPath)
    {
        $this->translator = $translator;
        $this->em = $em;
        $this->publicPath = $publicPath;
    }

    /**
     * @param User $user
     * @return float
     */
    public function getSpaceUsedByUser(User $user) : float
    {
        return disk_total_space($this->publicPath . '/' . $user->getId());
    }

    /**
     * @param User $user
     * @param File $file
     * @throws \Exception
     */
    public function hasEnoughSpaceForUpload(User $user, File $file) : void
    {
        if ($user->getDiskSpaceAllowed() - $this->getSpaceUsedByUser($user) < $file->getSize()) {
            throw new \Exception($this->translator->trans('error.not_enough_space'));
        }
    }
}
