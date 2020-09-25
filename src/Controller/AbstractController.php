<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FeatureChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @var FeatureChecker
     */
    private FeatureChecker $featureChecker;

    public function __construct(FeatureChecker $featureChecker)
    {
        $this->featureChecker = $featureChecker;
    }

    public function denyAccessUnlessFeaturesEnabled(array $features)
    {
        foreach ($features as $feature) {
            if ($this->featureChecker->isFeatureEnabled($feature) === false) {
                throw new AccessDeniedException();
            }
        }
    }
}
