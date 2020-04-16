<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use App\Enum\DatumTypeEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DatumController extends AbstractController
{
    /**
     * @Route("/datum/{type}", name="app_datum_get_html_by_type", methods={"GET"})
     *
     * @param string $type
     * @return JsonResponse
     */
    public function getHtmlByType(string $type) : JsonResponse
    {
        $html = $this->render('App/Datum/datum.html.twig', [
            'iteration' => '__placeholder__',
            'type' => $type
        ])->getContent();

        return new JsonResponse([
            'html' => $html,
            'type' => $type
        ]);
    }

    /**
     * @Route("/datum/load-common-fields/{id}", name="app_datum_load_common_fields", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Entity("collection", expr="repository.findById(id, true)")
     *
     * @param Collection $collection
     * @return JsonResponse
     */
    public function loadCommonFields(Collection $collection) : JsonResponse
    {
        try {
            $commonFields = [];

            $first = $collection->getItems()->first();
            if ($first instanceof Item) {
                foreach ($first->getData() as $datum) {
                    if (!\in_array($datum->getType(), [DatumTypeEnum::TYPE_SIGN, DatumTypeEnum::TYPE_IMAGE], false)) {
                        $field['datum'] = $datum;
                        $field['type'] = $datum->getType();
                        $commonFields[$datum->getLabel()] = $field;
                    }
                }
            }

            foreach ($collection->getItems() as $key => $item) {
                if ($key > 0 && $item->getData()->count() > 0) {
                    foreach ($commonFields as $cfKey => &$commonField) {
                        $existing = null;
                        foreach ($item->getData() as $datum) {
                            if ($datum->getLabel() === $commonField['datum']->getLabel()) {
                                $existing = $commonField;
                                break;
                            }
                        }
                        if (null === $existing) {
                            unset($commonFields[$cfKey]);
                        } elseif (isset($datum) && $datum->getValue() !== $commonField['datum']->getValue()) {
                            $commonField['datum']->setValue(null);
                        }
                    }
                }
            }

            foreach ($commonFields as &$commonField) {
                $commonField['html'] = $this->render('App/Datum/datum.html.twig', [
                            'iteration' => '__placeholder__',
                            'type' => $commonField['type'],
                            'datum' => $commonField['datum']
                        ])->getContent();
                unset($commonField['datum']);
            }

            return new JsonResponse([
                'fields' => $commonFields
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(false, 500);
        }
    }
}
