<?php
namespace App\Service;

use App\Entity\Medium;
use App\Entity\User;
use Symfony\Component\Translation\TranslatorInterface;

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
    protected $translator;

    /**
     * DiskUsageCalculator constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param User $user
     * @return int
     */
    public function getSpaceUsedByUser(User $user) : int
    {
        $size = 0;

        foreach ($user->getMedia() as $medium) {
            $size += filesize($medium->getPath());
            if ($medium->getThumbnailPath()) {
                $size += filesize($medium->getThumbnailPath());
            }
        }

        return $size;
    }
}
