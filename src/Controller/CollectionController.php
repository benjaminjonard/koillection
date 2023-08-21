<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Enum\DatumTypeEnum;
use App\Enum\ReservedLabelEnum;
use App\Form\Type\Entity\CollectionType;
use App\Form\Type\Entity\DisplayConfigurationType;
use App\Form\Type\Model\BatchTaggerType;
use App\Form\Type\Model\ScrapingType;
use App\Model\BatchTagger;
use App\Model\Scraping;
use App\Repository\ChoiceListRepository;
use App\Repository\CollectionRepository;
use App\Repository\DatumRepository;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CollectionController extends AbstractController
{
    #[Route(path: '/collections', name: 'app_collection_index', methods: ['GET'])]
    #[Route(path: '/collections', name: 'app_homepage', methods: ['GET'])]
    #[Route(path: '/user/{username}/collections', name: 'app_shared_collection_index', methods: ['GET'])]
    #[Route(path: '/user/{username}', name: 'app_shared_homepage', methods: ['GET'])]
    public function index(CollectionRepository $collectionRepository): Response
    {
        $collections = $collectionRepository->findBy(['parent' => null], ['title' => Criteria::ASC]);

        $collectionsCounter = \count($collections);
        $itemsCounter = 0;
        foreach ($collections as $collection) {
            $collectionsCounter += $collection->getCachedValues()['counters']['children'] ?? 0;
            $itemsCounter += $collection->getCachedValues()['counters']['items'] ?? 0;
        }

        return $this->render('App/Collection/index.html.twig', [
            'collections' => $collections,
            'collectionsCounter' => $collectionsCounter,
            'itemsCounter' => $itemsCounter
        ]);
    }

    #[Route(path: '/collections/add', name: 'app_collection_add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        TranslatorInterface $translator,
        CollectionRepository $collectionRepository,
        ChoiceListRepository $choiceListRepository,
        ManagerRegistry $managerRegistry
    ): Response {
        $collection = new Collection();

        if ($request->query->has('parent')) {
            $parent = $collectionRepository->findOneBy([
                'id' => $request->query->get('parent'),
                'owner' => $this->getUser(),
            ]);
            $collection
                ->setParent($parent)
                ->setVisibility($parent->getVisibility())
                ->setParentVisibility($parent->getVisibility())
                ->setFinalVisibility($parent->getFinalVisibility())
                ->getItemsDisplayConfiguration()->setDisplayMode($parent->getItemsDisplayConfiguration()->getDisplayMode())
            ;
        }

        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($collection);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.collection_added', ['collection' => $collection->getTitle()]));

            return $this->redirectToRoute('app_collection_show', ['id' => $collection->getId()]);
        }

        return $this->render('App/Collection/add.html.twig', [
            'collection' => $collection,
            'form' => $form,
            'scrapingForm' => $this->createForm(ScrapingType::class, new Scraping('collection')),
            'suggestedItemsLabels' => $collectionRepository->suggestItemsLabels($collection),
            'suggestedChildrenLabels' => $collectionRepository->suggestChildrenLabels($collection),
            'choiceLists' => $choiceListRepository->findAll()
        ]);
    }

    #[Route(path: '/collections/edit', name: 'app_collection_edit_index', methods: ['GET', 'POST'])]
    public function editIndex(
        Request $request,
        TranslatorInterface $translator,
        ManagerRegistry $managerRegistry,
        DatumRepository $datumRepository
    ): Response {
        $displayConfiguration = $this->getUser()->getCollectionsDisplayConfiguration();
        $form = $this->createForm(DisplayConfigurationType::class, $displayConfiguration, [
            'hasShowVisibility' => true,
            'hasShowActions' => true,
            'hasShowNumberOfChildren' => true,
            'hasShowNumberOfItems' => true,
            'sorting' => array_merge([
                'form.item_sorting.default_value' => null,
                'form.item_sorting.number_of_children' => ReservedLabelEnum::NUMBER_OF_CHILDREN,
                'form.item_sorting.number_of_items' => ReservedLabelEnum::NUMBER_OF_ITEMS,
            ], $datumRepository->findAllChildrenLabelsInCollection(null, DatumTypeEnum::TEXT_TYPES)),
            'columns' => [
                'availableColumnLabels' => $datumRepository->findAllChildrenLabelsInCollection(null, DatumTypeEnum::TEXT_TYPES),
                'selectedColumnsLabels' => $displayConfiguration->getColumns()
            ]
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.collection_index_edited'));

            return $this->redirectToRoute('app_collection_index');
        }

        return $this->render('App/Collection/edit_index.html.twig', [
            'form' => $form,
            'displayConfiguration' => $displayConfiguration
        ]);
    }

    #[Route(path: '/collections/{id}', name: 'app_collection_show', methods: ['GET'])]
    #[Route(path: '/user/{username}/collections/{id}', name: 'app_shared_collection_show', methods: ['GET'])]
    public function show(
        Collection $collection,
        CollectionRepository $collectionRepository,
        ItemRepository $itemRepository,
    ): Response {
        return $this->render('App/Collection/show.html.twig', [
            'collection' => $collection,
            'children' => $collectionRepository->findForOrdering($collection),
            'items' => $itemRepository->findForOrdering($collection)
        ]);
    }

    #[Route(path: '/collections/{id}/items', name: 'app_collection_items', methods: ['GET'])]
    #[Route(path: '/user/{username}/collections/{id}/items', name: 'app_shared_collection_items', methods: ['GET'])]
    public function items(Collection $collection, ItemRepository $itemRepository): Response
    {
        return $this->render('App/Collection/items.html.twig', [
            'collection' => $collection,
            'items' => $itemRepository->findAllByCollection($collection),
            'displayConfiguration' => $collection->getItemsDisplayConfiguration()
        ]);
    }

    #[Route(path: '/collections/{id}/edit', name: 'app_collection_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Collection $collection,
        TranslatorInterface $translator,
        CollectionRepository $collectionRepository,
        ChoiceListRepository $choiceListRepository,
        ManagerRegistry $managerRegistry,
    ): Response {
        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.collection_edited', ['collection' => $collection->getTitle()]));

            return $this->redirectToRoute('app_collection_show', ['id' => $collection->getId()]);
        }

        return $this->render('App/Collection/edit.html.twig', [
            'form' => $form,
            'scrapingForm' => $this->createForm(ScrapingType::class, new Scraping('collection', true)),
            'collection' => $collection,
            'suggestedItemsLabels' => $collectionRepository->suggestItemsLabels($collection),
            'suggestedChildrenTitles' => $collectionRepository->suggestChildrenLabels($collection),
            'choiceLists' => $choiceListRepository->findAll(),
        ]);
    }

    #[Route(path: '/collections/{id}/delete', name: 'app_collection_delete', methods: ['POST'])]
    public function delete(Request $request, Collection $collection, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createDeleteForm('app_collection_delete', $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($collection);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.collection_deleted', ['collection' => $collection->getTitle()]));
        }

        if (!$collection->getParent() instanceof Collection) {
            return $this->redirectToRoute('app_collection_index');
        }

        return $this->redirectToRoute('app_collection_show', ['id' => $collection->getParent()->getId()]);
    }

    #[Route(path: '/collections/{id}/batch-tagging', name: 'app_collection_batch_tagging', methods: ['GET', 'POST'])]
    public function batchTagging(Request $request, Collection $collection, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $batchTagger = new BatchTagger();
        $batchTagger->setCollection($collection);

        $form = $this->createForm(BatchTaggerType::class, $batchTagger);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $itemsTaggedCount = $batchTagger->applyBatch();
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.items_tagged', ['count' => $itemsTaggedCount]));

            return $this->redirectToRoute('app_collection_show', ['id' => $collection->getId()]);
        }

        return $this->render('App/Collection/batch_tagging.html.twig', [
            'form' => $form,
            'collection' => $collection,
        ]);
    }
}
