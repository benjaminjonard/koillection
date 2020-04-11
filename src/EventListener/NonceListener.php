<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Security\NonceGenerator;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Class NonceListener
 *
 * @package App\EventListener
 */
class NonceListener
{
    /**
     * @var NonceGenerator
     */
    private NonceGenerator $nonceGenerator;

    /**
     * BeforeResponseListener constructor.
     * @param NonceGenerator $nonceGenerator
     */
    public function __construct(NonceGenerator $nonceGenerator)
    {
        $this->nonceGenerator = $nonceGenerator;
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        // get the Response object from the event
        $response = $event->getResponse();

        // create a CSP rule, using the nonce generator service
        $nonce = $this->nonceGenerator->getNonce();
        $cspHeader = "script-src 'nonce-".$nonce."' 'strict-dynamic'; object-src 'none';";

        // set CSP header on the response object
        $response->headers->set("Content-Security-Policy", $cspHeader);
    }
}
