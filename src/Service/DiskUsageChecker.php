<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Medium;
use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DiskUsageChecker
 *
 * @package App\Service
 */
class DiskUsageChecker
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * DiskUsageChecker constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param User $user
     * @param array $media
     * @throws \Exception
     */
    public function hasEnoughSpaceForUpload(User $user, array $media) : void
    {
        $sizeRequested = 0;
        foreach ($media as $medium) {
            if ($medium instanceof Medium && $medium->getUploadedFile() !== null) {
                $sizeRequested += $medium->getUploadedFile()->getSize();
            }
        }

        if ($user->getDiskSpaceAllowed() - $user->getDiskSpaceUsed() < $sizeRequested) {
            throw new \Exception($this->translator->trans('error.not_enough_space'));
        }
    }
}
