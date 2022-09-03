<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ContextHandler;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class FilterListener
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly ContextHandler $contextHandler,
        private readonly Security $security,
        private readonly UserRepository $userRepository
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->getRequest()->attributes->has('exception')) {
            return;
        }

        $filters = $this->managerRegistry->getManager()->getFilters();
        $context = $this->contextHandler->getContext();

        // Visibility filter
        if ('shared' === $context) {
            $filter = $filters->enable('visibility');
            $filter->setParameter('user', $this->security->getUser() instanceof User ? $this->security->getUser()->getId() : null, 'string');
        } elseif ($filters->isEnabled('visibility')) {
            $filters->disable('visibility');
        }

        $this->setContextUser();

        // Ownership filter
        $user = $this->contextHandler->getContextUser();
        if ($user && 'admin' !== $context) {
            $filter = $filters->enable('ownership');
            $filter->setParameter('id', $user->getId(), 'string');
        } elseif ($filters->isEnabled('ownership')) {
            $filters->disable('ownership');
        }
    }

    public function setContextUser(): void
    {
        $user = null;
        if ('shared' === $this->contextHandler->getContext()) {
            $user = $this->userRepository->findOneBy(['username' => $this->contextHandler->getUsername()]);
            if ($user === null) {
                throw new NotFoundHttpException();
            }
        } elseif ($this->security->getUser() instanceof User) {
            $user = $this->security->getUser();
        }

        $this->contextHandler->setContextUser($user);
    }
}
