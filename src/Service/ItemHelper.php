<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Collection;
use App\Entity\Tag;
use App\Entity\Template;
use App\Enum\DatumTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ItemHelper
 *
 * @package App\Service
 */
class ItemHelper
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;


    /**
     * ItemHelper constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
}
