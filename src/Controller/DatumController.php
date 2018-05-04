<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use App\Enum\DatumTypeEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DatumController
 *
 * @package App\Controller
 *
 * @Route("/datum")
 */
class DatumController extends AbstractController
{
    /**
     * @Route("/{type}", name="app_datum_get_html_by_type", options={"expose"=true})
     * @Method({"GET"})
     *
     * @param string $type
     * @return JsonResponse
     */
    public function getHtmlByType(string $type) : JsonResponse
    {
        try {
            $html = $this->render('App/Datum/'.DatumTypeEnum::getTypeSlug($type).'.html.twig', ['iteration' => '__placeholder__'])->getContent();
            $isImage = \in_array($type, [DatumTypeEnum::TYPE_SIGN, DatumTypeEnum::TYPE_IMAGE], false) ? true : false;

            return new JsonResponse([
                'html' => $html,
                'isImage' => $isImage,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(false, 500);
        }
    }

    /**
     * @Route("/load-common-fields/{id}", name="app_datum_load_common_fields", requirements={"id"="%uuid_regex%"}, options={"expose"=true})
     * @Method({"GET"})
     * @Entity("collection", expr="repository.findById(id, true)")
     *
     * @param Collection $collection
     * @return JsonResponse
     */
    public function loadCommonFields(Collection $collection)
    {
        try {
            $commonFields = [];

            $first = $collection->getItems()->first();
            if ($first instanceof Item) {
                foreach ($first->getData() as $datum) {
                    if (!\in_array($datum->getType(), [DatumTypeEnum::TYPE_SIGN, DatumTypeEnum::TYPE_IMAGE], false)) {
                        $field['isImage'] = false;
                        $field['datum'] = $datum;
                        $commonFields[$datum->getLabel()] = $field;
                    }
                }
            }

            foreach ($collection->getItems() as $key => $item) {
                if ($key > 0 && $item->getData()->count() > 0) {
                    foreach ($commonFields as $cfKey => &$commonField) {
                        $existing = null;
                        foreach ($item->getData() as $datum) {
                            if ($datum->getLabel() == $commonField['datum']->getLabel()) {
                                $existing = $commonField;
                                break;
                            }
                        }
                        if (null === $existing) {
                            unset($commonFields[$cfKey]);
                        } elseif ($datum->getValue() !== $commonField['datum']->getValue()) {
                            $commonField['datum']->setValue(null);
                        }
                    }
                }
            }

            foreach ($commonFields as &$commonField) {
                $commonField['html'] = $this->render('App/Datum/'.DatumTypeEnum::getTypeSlug($commonField['datum']->getType()).'.html.twig', [
                            'iteration' => '__placeholder__',
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
