<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\ContextHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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
     * FilterListener constructor.
     * @param EntityManagerInterface $em
     * @param ContextHandler $contextHandler
     */
    public function __construct(EntityManagerInterface $em, ContextHandler $contextHandler)
    {
        $this->em = $em;
        $this->contextHandler = $contextHandler;
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


        //Ownership filter
        $user = $this->contextHandler->getContextUser();
        if ($user && $context !== 'admin') {
            $filter = $filters->enable('ownership');
            $filter->setParameter('id', $user->getId(), 'integer');
        } elseif ($filters->isEnabled('ownership')) {
            $filters->disable('ownership');
        }

        $this->contextHandler->setContextUser();
    }
}
