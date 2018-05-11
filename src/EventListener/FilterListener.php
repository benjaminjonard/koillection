<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\ContextHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var ContextHandler
     */
    private $contextHandler;

    /**
     * FilterListener constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param ContextHandler $contextHandler
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, ContextHandler $contextHandler)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->contextHandler = $contextHandler;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $filters = $this->em->getFilters();
        $context = $this->contextHandler->getContext();

        //Visibility filter
        if (\in_array($context, ['preview', 'user'], false)) {
            $filter = $filters->enable('visibility');
            $filter->setParameter('context', $context, 'string');
        } elseif ($filters->isEnabled('visibility')) {
            $filters->disable('visibility');
        }

        //Ownership filter
        $user = $this->getUser($request, $context);
        if ($user && !\in_array($context, ['admin'], false)) {
            $filter = $filters->enable('ownership');
            $filter->setParameter('id', $user->getId(), 'integer');
        } elseif ($filters->isEnabled('ownership')) {
            $filters->disable('ownership');
        }
    }

    /**
     * @param Request $request
     * @param $context
     * @return null|UserInterface
     */
    private function getUser(Request $request, $context) : ?UserInterface
    {
        if ($context === 'user') {
            preg_match("/^\/user\/(\w+)/", $request->getRequestUri(), $matches);
            return $this->em->getRepository(User::class)->findOneByUsername($matches[1]);
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (!($user instanceof UserInterface)) {
            return null;
        }

        return $user;
    }
}
