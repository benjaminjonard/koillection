<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FeatureChecker;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function isFeatureEnabled(string $feature)
    {
        return $this->container->get(FeatureChecker::class)->isFeatureEnabled($feature);
    }
}
