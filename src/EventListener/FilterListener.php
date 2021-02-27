<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Service\ContextHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class FilterListener
{
    private EntityManagerInterface $em;

    private ContextHandler $contextHandler;

    private Security $security;

    public function __construct(EntityManagerInterface $em, ContextHandler $contextHandler, Security $security)
    {
        $this->em = $em;
        $this->contextHandler = $contextHandler;
        $this->security = $security;
    }

    public function onKernelRequest()
    {
        $filters = $this->em->getFilters();
        $context = $this->contextHandler->getContext();

        //Visibility filter
        if (\in_array($context, ['preview', 'user'], false)) {
            $filter = $filters->enable('visibility');
            $filter->setParameter('context', $context, 'string');
        } elseif ($filters->isEnabled('visibility')) {
            $filters->disable('visibility');
        }
        $this->setContextUser();

        //Ownership filter
        $user = $this->contextHandler->getContextUser();
        if ($user && $context !== 'admin') {
            $filter = $filters->enable('ownership');
            $filter->setParameter('id', $user->getId(), 'integer');
        } elseif ($filters->isEnabled('ownership')) {
            $filters->disable('ownership');
        }
    }

    public function setContextUser()
    {
        $user = null;
        if ($this->contextHandler->getContext() === 'user') {
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => $this->contextHandler->getUsername()]);
            if (!$user) {
                throw new NotFoundHttpException();
            }
        } elseif ($this->security->getUser() instanceof User) {
            $user = $this->security->getUser();
        }

        $this->contextHandler->setContextUser($user);
    }
}
