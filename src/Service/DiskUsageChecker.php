<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Image;
use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiskUsageChecker
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

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
     * @param array $images
     * @throws \Exception
     */
    public function hasEnoughSpaceForUpload(User $user, array $images) : void
    {
        $sizeRequested = 0;
        foreach ($images as $image) {
            if ($image instanceof Image && $image->getUploadedFile() !== null) {
                $sizeRequested += $image->getUploadedFile()->getSize();
            }
        }

        if ($user->getDiskSpaceAllowed() - $user->getDiskSpaceUsed() < $sizeRequested) {
            throw new \Exception($this->translator->trans('error.not_enough_space'));
        }
    }
}
