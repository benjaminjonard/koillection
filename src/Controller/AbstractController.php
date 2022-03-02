<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FeatureChecker;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function __construct(
        protected FeatureChecker $featureChecker
    ) {
    }

    public function denyAccessUnlessFeaturesEnabled(array $features)
    {
        foreach ($features as $feature) {
            if (false === $this->featureChecker->isFeatureEnabled($feature)) {
                throw new AccessDeniedException();
            }
        }
    }

    public function createDeleteForm($url, $entity): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($url, ['id' => $entity->getId()]))
            ->setMethod('POST')
            ->getForm()
        ;
    }
}
