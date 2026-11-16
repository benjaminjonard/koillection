<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\DisplayConfiguration;
use App\Entity\Search;
use App\Enum\AdvancedItemSearch\OperatorEnum;
use App\Enum\AdvancedItemSearch\TypeEnum;
use App\Enum\DatumTypeEnum;
use App\Enum\DisplayModeEnum;
use App\Form\Type\Entity\SearchType;
use App\Repository\DatumRepository;
use App\Repository\SearchRepository;
use App\Service\AdvancedItemSearcher;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdvancedItemSearchController extends AbstractController
{
    #[Route(path: '/advanced-item-search/saved-searches', name: 'app_advanced_item_search_saved_searches', methods: ['GET'])]
    public function savedSearches(SearchRepository $searchRepository): Response
    {
        return $this->render('App/AdvancedItemSearch/saved_searches.html.twig', [
            'searches' => $searchRepository->findAll()
        ]);
    }

    #[Route(path: '/advanced-item-search', name: 'app_advanced_item_search_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        ManagerRegistry $managerRegistry,
        SearchRepository $searchRepository,
        AdvancedItemSearcher $advancedItemSearcher
    ): Response {
        $results = null;

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('saveAndSubmit')->isClicked()) {
                $managerRegistry->getManager()->persist($search);
                $managerRegistry->getManager()->flush();

                return $this->redirectToRoute('app_advanced_item_search_show', ['id' => $search->getId()]);
            }

            $results = $advancedItemSearcher->search($search);
        }

        return $this->render('App/AdvancedItemSearch/index.html.twig', [
            'form' => $form,
            'search' => $search,
            'results' => $results,
            'searches' => $searchRepository->findAll()
        ]);
    }

    #[Route(path: '/advanced-item-search/{id}', name: 'app_advanced_item_search_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        ManagerRegistry $managerRegistry,
        Search $search,
        SearchRepository $searchRepository,
        AdvancedItemSearcher $advancedItemSearcher
    ): Response {
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
        }

        $results = $advancedItemSearcher->search($search);
        $search->setNumberOfResults(\count($results));
        $managerRegistry->getManager()->flush();

        return $this->render('App/AdvancedItemSearch/show.html.twig', [
            'form' => $form,
            'search' => $search,
            'results' => $results,
            'searches' => $searchRepository->findAll(),
        ]);
    }

    #[Route(path: '/advanced-item-search/{id}/delete', name: 'app_advanced_item_search_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        ManagerRegistry $managerRegistry,
        Search $search,
        TranslatorInterface $translator
    ): Response {
        $form = $this->createDeleteForm('app_advanced_item_search_delete', $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($search);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.search_deleted', ['search' => $search->getName()]));
        }

        return $this->redirectToRoute('app_advanced_item_search_saved_searches');
    }

    #[Route(path: '/advanced-item-search/load-type-inputs/{type}', name: 'app_advanced_item_search_load_type_inputs', methods: ['GET', 'POST'])]
    public function loadTypeInputs(DatumRepository $datumRepository, string $type) : Response
    {
        $operatorInput = null;
        $valueInput = null;
        $datumInput = null;

        if ($type === TypeEnum::TYPE_NAME) {
            $operatorInput = $this->render('App/AdvancedItemSearch/_input_operator.html.twig', [
                'operators' => OperatorEnum::getOperatorsByType('item_name')
            ])->getContent();

            $valueInput = $this->render('App/AdvancedItemSearch/_input_text.html.twig')->getContent();
        }

        if ($type === TypeEnum::TYPE_COLLECTION) {
            $operatorInput = $this->render('App/AdvancedItemSearch/_input_operator.html.twig', [
                'operators' => OperatorEnum::getOperatorsByType('collection_name')
            ])->getContent();

            $valueInput = $this->render('App/AdvancedItemSearch/_input_collection.html.twig')->getContent();
        }

        if ($type === TypeEnum::TYPE_TAG) {
            $operatorInput = $this->render('App/AdvancedItemSearch/_input_operator.html.twig', [
                'operators' => OperatorEnum::getOperatorsByType('tag_name')
            ])->getContent();

            $valueInput = $this->render('App/AdvancedItemSearch/_input_tag.html.twig')->getContent();
        }

        if ($type === TypeEnum::TYPE_DATUM) {
            $labels = [];
            foreach ($datumRepository->findAllUniqueLabels() as $datum) {
                $labels["{$datum['label']}_koillection_separator_{$datum['type']}"]['value'] = $datum['label'];
                $labels["{$datum['label']}_koillection_separator_{$datum['type']}"]['type'] = $datum['type'];
            }

            $datumInput = $this->render('App/AdvancedItemSearch/_input_datum.html.twig', ['labels' => $labels])->getContent();
        }

        return new JsonResponse([
            'operatorInput' => $operatorInput,
            'valueInput' => $valueInput,
            'datumInput' => $datumInput
        ]);
    }

    #[Route(path: '/advanced-item-search/load-datum-inputs/{value}', name: 'app_advanced_item_search_load_datum_inputs', methods: ['GET', 'POST'], requirements: ['value' => '.+'])]
    public function loadDatumInputs(string $value) : Response
    {
        list($label, $type) = explode('_koillection_separator_', $value);

        $operatorInput = $this->render('App/AdvancedItemSearch/_input_operator.html.twig', [
            'operators' => OperatorEnum::getOperatorsByType($type)
        ])->getContent();

        $valueInput = match ($type) {
            DatumTypeEnum::TYPE_TEXT, DatumTypeEnum::TYPE_TEXTAREA, DatumTypeEnum::TYPE_MARKDOWN, DatumTypeEnum::TYPE_LINK => $this->render('App/AdvancedItemSearch/_input_text.html.twig')->getContent(),
            DatumTypeEnum::TYPE_COUNTRY => $this->render('App/AdvancedItemSearch/_input_country.html.twig')->getContent(),
            DatumTypeEnum::TYPE_DATE => $this->render('App/AdvancedItemSearch/_input_date.html.twig')->getContent(),
            DatumTypeEnum::TYPE_NUMBER => $this->render('App/AdvancedItemSearch/_input_number.html.twig')->getContent(),
            DatumTypeEnum::TYPE_LIST, DatumTypeEnum::TYPE_CHOICE_LIST => $this->render('App/AdvancedItemSearch/_input_list.html.twig', ['datumLabel' => $label, 'datumType' => $type])->getContent(),
            DatumTypeEnum::TYPE_RATING => $this->render('App/AdvancedItemSearch/_input_rating.html.twig')->getContent(),
            DatumTypeEnum::TYPE_CHECKBOX => $this->render('App/AdvancedItemSearch/_input_checkbox.html.twig')->getContent(),
            default => ''
        };

        return new JsonResponse([
            'operatorInput' => $operatorInput,
            'valueInput' => $valueInput
        ]);
    }
}