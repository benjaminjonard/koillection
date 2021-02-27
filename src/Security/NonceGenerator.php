<?php

declare(strict_types=1);

namespace App\Security;

class NonceGenerator
{
    private ?string $nonce = null;

    public function getNonce() : string
    {
        if (!$this->nonce) {
            $this->nonce = base64_encode(random_bytes(20));
        }

        return $this->nonce;
    }
}
