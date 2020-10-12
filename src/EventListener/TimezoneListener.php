<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\Extension\CoreExtension;

class TimezoneListener
{
    /**
     * @var Environment
     */
    private Environment $twig;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * ActionListener constructor.
     * @param Security $security
     * @param Environment $twig
     */
    public function __construct(Security $security, Environment $twig)
    {
        $this->security = $security;
        $this->twig = $twig;
    }

    /**
     * @return null
     * @throws \Exception
     */
    public function onKernelRequest()
    {
        if ($this->security->getUser() instanceof User) {
            $this->twig->getExtension(CoreExtension::class)->setTimezone($this->security->getUser()->getTimezone());
        }
    }
}
