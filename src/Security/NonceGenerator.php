<?php

namespace App\Security;

/**
 * Class NonceGenerator
 *
 * @package App\Security
 */
class NonceGenerator
{
    /**
     * @var string
     */
    private $nonce;

    /**
     * Generates a random nonce parameter.
     *
     * @return string
     */
    public function getNonce() : string
    {
        if (!$this->nonce) {
            $this->nonce = base64_encode(random_bytes(20));
        }

        return $this->nonce;
    }
}
