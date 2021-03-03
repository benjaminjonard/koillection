<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ActivityListener
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security= $security;
        $this->em= $em;
    }

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
