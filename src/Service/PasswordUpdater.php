<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class PasswordUpdater
{
    private EncoderFactoryInterface $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function hashPassword(User $user) : User
    {
        $plainPassword = $user->getPlainPassword();

        if ($plainPassword === null) {
            return $user;
        }

        $encoder = $this->encoderFactory->getEncoder($user);
        $user->setSalt(null);

        $hashedPassword = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();

        return $user;
    }
}
