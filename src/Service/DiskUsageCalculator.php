<?php
namespace App\Service;

use App\Entity\Medium;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $publicPath;

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
        $media = $this->em->getRepository(Medium::class)->findBy(['owner' => $user]);

        foreach ($media as $medium) {
            $size += filesize($this->publicPath.'/'.$medium->getPath());
            if ($medium->getThumbnailPath()) {
                $size += filesize($this->publicPath.'/'.$medium->getThumbnailPath());
            }
        }

        return $size;
    }
}
