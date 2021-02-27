<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DatumController extends AbstractController
{
    #[Route(
        path: ['en' => '/datum/{type}', 'fr' => '/datum/{type}'],
        name: 'app_datum_get_html_by_type', methods: ['GET']
    )]
    public function getHtmlByType(string $type) : JsonResponse
    {
        $html = $this->render('App/Datum/_datum.html.twig', [
            'entity' => '__entity_placeholder__',
            'iteration' => '__placeholder__',
            'type' => $type
        ])->getContent();

        return new JsonResponse([
            'html' => $html,
            'type' => $type
        ]);
    }

    #[Route(
        path: ['en' => '/datum/load-common-fields/{id}', 'fr' => '/datum/charger-les-champs-communs/{id}'],
        name: 'app_datum_load_common_fields', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Entity('collection', expr: 'repository.findWithItemsAndData(id)', class: Collection::class)]
    public function loadCommonFields(Collection $collection) : JsonResponse
    {
        try {
            $commonFields = [];

            $first = $collection->getItems()->first();
            if ($first instanceof Item) {
                foreach ($first->getDataTexts() as $datum) {
                    $field['datum'] = $datum;
                    $field['type'] = $datum->getType();
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

            foreach ($commonFields as &$commonField) {
                $commonField['html'] = $this->render('App/Datum/_datum.html.twig', [
                            'entity' => 'item',
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

    #[Route(
        path: ['en' => '/datum/load-collection-fields/{id}', 'fr' => '/datum/charger-les-champs-de-la-collection/{id}'],
        name: 'app_datum_load_collection_fields', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Entity('collection', expr: 'repository.findWithItemsAndData(id)', class: Collection::class)]
    public function loadCollectionFields(Collection $collection) : JsonResponse
    {
        try {
            $fields = [];
            foreach ($collection->getData() as $datum) {
                $fields[$datum->getLabel()]['type'] = $datum->getType();
                $fields[$datum->getLabel()]['html'] = $this->render('App/Datum/_datum.html.twig', [
                    'entity' => 'item',
                    'iteration' => '__placeholder__',
                    'type' => $datum->getType(),
                    'datum' => $datum
                ])->getContent();
            }

            return new JsonResponse([
                'fields' => $fields
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(false, 500);
        }
    }
}
