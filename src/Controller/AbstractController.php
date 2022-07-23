<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Album;
use App\Entity\ChoiceList;
use App\Entity\Collection;
use App\Entity\Inventory;
use App\Entity\Item;
use App\Entity\Loan;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Entity\Template;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Service\FeatureChecker;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    public function __construct(
        protected FeatureChecker $featureChecker
    ) {
    }

    public function denyAccessUnlessFeaturesEnabled(array $features): void
    {
        foreach ($features as $feature) {
            if (false === $this->featureChecker->isFeatureEnabled($feature)) {
                throw new AccessDeniedException();
            }
        }
    }

    public function createDeleteForm(
        string $url,
        User|Album|Collection|Inventory|Item|Loan|Photo|TagCategory|Tag|Template|Wish|Wishlist|ChoiceList $entity = null
    ): FormInterface
    {
        $params = [];
        if ($entity) {
            $params['id'] = $entity->getId();
        }

        return $this->createFormBuilder()
            ->setAction($this->generateUrl($url, $params))
            ->setMethod('POST')
            ->getForm()
        ;
    }
}
