<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ChoiceList;
use App\Entity\Collection;
use App\Entity\Item;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DatumController extends AbstractController
{
    #[Route(path: '/datum/{type}', name: 'app_datum_get_html_by_type', methods: ['GET'])]
    public function getHtmlByType(string $type): JsonResponse
    {
        $html = $this->render('App/Datum/_datum.html.twig', [
            'entity' => '__entity_placeholder__',
            'iteration' => '__placeholder__',
            'type' => $type,
            'label' => null,
            'visibility' => VisibilityEnum::VISIBILITY_PUBLIC
        ])->getContent();

        return new JsonResponse([
            'html' => $html,
            'type' => \in_array($type, [DatumTypeEnum::TYPE_IMAGE, DatumTypeEnum::TYPE_SIGN, DatumTypeEnum::TYPE_VIDEO]) ? 'image' : 'text',
        ]);
    }

    #[Route(path: '/datum/choice-list/{id}', name: 'app_datum_choice_list_get_html', methods: ['GET'])]
    public function getChoiceListHtml(ChoiceList $choiceList): JsonResponse
    {
        $html = $this->render('App/Datum/_datum.html.twig', [
            'entity' => '__entity_placeholder__',
            'iteration' => '__placeholder__',
            'type' => DatumTypeEnum::TYPE_CHOICE_LIST,
            'choiceList' => $choiceList,
            'label' => $choiceList->getName(),
            'visibility' => VisibilityEnum::VISIBILITY_PUBLIC
        ])->getContent();

        return new JsonResponse([
            'html' => $html,
        ]);
    }

    #[Route(path: '/datum/load-common-fields/{id}', name: 'app_datum_load_common_fields', methods: ['GET'])]
    public function loadCommonFields(
        #[MapEntity(expr: 'repository.findWithItemsAndData(id)')] Collection $collection
    ): JsonResponse {
        $commonFields = [];

        $first = $collection->getItems()->first();
        if ($first instanceof Item) {
            foreach ($first->getDataTexts() as $datum) {
                $field = [
                    'datum' => $datum,
                    'type' => $datum->getType(),
                    'label' => $datum->getLabel(),
                    'choiceList' => $datum->getChoiceList(),
                ];
                $commonFields[$datum->getLabel()] = $field;
            }
        }

        foreach ($collection->getItems() as $key => $item) {
            if ($key > 0 && $item->getDataTexts()->count() > 0) {
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

        $result = [];
        $i = 0;

        foreach ($commonFields as $label => $field) {
            $result[$i][] = \in_array($field['type'], [DatumTypeEnum::TYPE_IMAGE, DatumTypeEnum::TYPE_SIGN]) ? 'image' : 'text';
            $result[$i][] = $label;
            $result[$i][] = $this->render('App/Datum/_datum.html.twig', [
                'entity' => 'item',
                'iteration' => '__placeholder__',
                'type' => $field['type'],
                'datum' => $field['datum'],
                'label' => $field['label'],
                'choiceList' => $field['choiceList'],
                'visibility' => $field['visibility']
            ])->getContent();
            ++$i;
        }

        return new JsonResponse($result);
    }

    #[Route(path: '/datum/load-collection-fields/{id}', name: 'app_datum_load_collection_fields', methods: ['GET'])]
    public function loadCollectionFields(
        #[MapEntity(expr: 'repository.findWithItemsAndData(id)')] Collection $collection
    ): JsonResponse {
        $fields = [];
        foreach ($collection->getData() as $key => $datum) {
            $fields[$key][] = \in_array($datum->getType(), [DatumTypeEnum::TYPE_IMAGE, DatumTypeEnum::TYPE_SIGN]) ? 'image' : 'text';
            $fields[$key][] = $datum->getLabel();
            $fields[$key][] = $this->render('App/Datum/_datum.html.twig', [
                'entity' => 'item',
                'iteration' => '__placeholder__',
                'type' => $datum->getType(),
                'datum' => $datum,
                'label' => $datum->getLabel(),
                'choiceList' => $datum->getChoiceList(),
                'visibility' => $datum->getVisibility()
            ])->getContent();
        }

        return new JsonResponse($fields);
    }
}
