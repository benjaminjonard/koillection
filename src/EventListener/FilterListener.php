<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Service\ContextHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class FilterListener
 *
 * @package App\EventListener
 */
class FilterListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ContextHandler
     */
    private $contextHandler;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * FilterListener constructor.
     * @param EntityManagerInterface $em
     * @param ContextHandler $contextHandler
     */
    public function __construct(EntityManagerInterface $em, ContextHandler $contextHandler, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->contextHandler = $contextHandler;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
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
            $user = $this->em->getRepository(User::class)->findOneByUsername($this->contextHandler->getUsername());
            if (!$user) {
                throw new NotFoundHttpException();
            }
        } elseif ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser() instanceof User) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $this->contextHandler->setContextUser($user);
    }
}
