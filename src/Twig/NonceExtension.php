<?php

declare(strict_types=1);

namespace App\Twig;

use App\Security\NonceGenerator;

/**
 * Class NonceExtension
 *
 * @package App\Twig
 */
class NonceExtension extends \Twig_Extension
{
    /**
     * @var NonceGenerator
     */
    private $nonceGenerator;

    /**
     * NonceExtension constructor.
     * @param NonceGenerator $nonceGenerator
     */
    public function __construct(NonceGenerator $nonceGenerator)
    {
        $this->nonceGenerator = $nonceGenerator;
    }

    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('csp_nonce', [$this, 'getNonce']),
        ];
    }

    /**
     * @return string
     */
    public function getNonce() : string
    {
        return $this->nonceGenerator->getNonce();
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'nonce_extension';
    }
}
