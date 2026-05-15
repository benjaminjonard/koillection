<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Symfony's FrankenPhpWorkerRunner (< 8.1) does not call $kernel->reset()
 * between requests, so services tagged kernel.reset keep state across
 * requests in worker mode. This listener fires services_resetter at the top
 * of each request to undo that.
 *
 * Doctrine's Registry::reset() only clears the identity map, not the
 * EntityManager's FilterCollection — so SQLFilter parameters (e.g.
 * OwnershipFilter's user id) leak between requests. We explicitly disable
 * any leftover filters here so the firewall's user provider runs against
 * an unfiltered EntityManager; FilterListener re-enables them later once
 * the auth user for this request is known.
 *
 * Remove once symfony/runtime ≥ 8.1 is in use and FRANKENPHP_RESET_KERNEL
 * is set in the environment.
 */
#[AsEventListener(event: 'kernel.request', priority: 4096)]
final readonly class WorkerResetListener
{
    public function __construct(
        #[Autowire(service: 'services_resetter')]
        private ResetInterface $servicesResetter,
        private ManagerRegistry $managerRegistry,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // Only act in FrankenPHP worker mode. FPM (and the test client) get a
        // fresh PHP process per request — they don't need this and triggering
        // services_resetter during tests breaks the DAMA-wrapped transaction.
        if (!\function_exists('frankenphp_handle_request')) {
            return;
        }

        $this->servicesResetter->reset();

        $filters = $this->managerRegistry->getManager()->getFilters();
        foreach (array_keys($filters->getEnabledFilters()) as $name) {
            $filters->disable($name);
        }
    }
}
