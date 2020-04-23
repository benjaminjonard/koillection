<?php

declare(strict_types=1);

namespace App\Security;

class NonceGenerator
{
    /**
     * @var string
     */
    private ?string $nonce = null;

    /**
     * Generates a random nonce parameter.
     *
     * @return string
     * @throws \Exception
     */
    public function getNonce() : string
    {
        if (!$this->nonce) {
            $this->nonce = base64_encode(random_bytes(20));
        }

        return $this->nonce;
    }
}
