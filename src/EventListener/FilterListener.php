<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ContextHandler;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class FilterListener
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private ContextHandler $contextHandler,
        private Security $security,
        private UserRepository $userRepository
    ) {}

    public function onKernelRequest()
    {
        $filters = $this->managerRegistry->getManager()->getFilters();
        $context = $this->contextHandler->getContext();

        //Visibility filter
        if ($context === 'shared') {
            $filter = $filters->enable('visibility');
            $filter->setParameter('user', $this->security->getUser() instanceof User ? $this->security->getUser()->getId() : null, 'string');
        } elseif ($filters->isEnabled('visibility')) {
            $filters->disable('visibility');
        }
        $this->setContextUser();

        //Ownership filter
        $user = $this->contextHandler->getContextUser();
        if ($user && $context !== 'admin') {
            $filter = $filters->enable('ownership');
            $filter->setParameter('id', $user->getId(), 'string');
        } elseif ($filters->isEnabled('ownership')) {
            $filters->disable('ownership');
        }
    }

    public function setContextUser()
    {
        $user = null;
        if ($this->contextHandler->getContext() === 'shared') {
            $user = $this->userRepository->findOneBy(['username' => $this->contextHandler->getUsername()]);
            if (!$user) {
                throw new NotFoundHttpException();
            }
        } elseif ($this->security->getUser() instanceof User) {
            $user = $this->security->getUser();
        }

        $this->contextHandler->setContextUser($user);
    }
}
