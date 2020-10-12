<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ActivityListener
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * ActionListener constructor.
     * @param Security $security
     * @param EntityManagerInterface $em
     */
    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security= $security;
        $this->em= $em;
    }

    /**
     * @return null
     * @throws \Exception
     */
    public function onKernelRequest()
    {
        $user = $this->security->getUser();
        $now = (new \DateTime())->setTime(0, 0, 0, 0);

        if (!$user instanceof User || $user->getLastDateOfActivity() == $now) {
            return null;
        }

        $user->setLastDateOfActivity($now);
        $this->em->flush();
    }
}
