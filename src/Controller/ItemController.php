<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Loan;
use App\Entity\Template;
use App\Form\Type\Entity\ItemType;
use App\Form\Type\Entity\LoanType;
use App\Repository\ChoiceListRepository;
use App\Repository\CollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use App\Service\ItemNameGuesser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ItemController extends AbstractController
{
    #[Route(path: '/items/add', name: 'app_item_add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        CollectionRepository $collectionRepository,
        ChoiceListRepository $choiceListRepository,
        TagRepository $tagRepository,
        TranslatorInterface $translator,
        ItemNameGuesser $itemNameGuesser,
        ManagerRegistry $managerRegistry
    ): Response {
        $collection = null;
        if ($request->query->has('collection')) {
            $collection = $collectionRepository->find($request->query->get('collection'));
        }

        if ($collection === null) {
            throw $this->createNotFoundException();
        }

        $item = new Item();
        $item
            ->setCollection($collection)
            ->setVisibility($collection->getVisibility())
            ->setParentVisibility($collection->getVisibility())
            ->setFinalVisibility($collection->getFinalVisibility())
        ;

        $template = $collection->getItemsDefaultTemplate();
        if ($template instanceof Template) {
            foreach ($template->getFields() as $field) {
                $item->addData((new Datum())
                    ->setLabel($field->getName())
                    ->setType($field->getType())
                    ->setPosition($field->getPosition())
                    ->setChoiceList($field->getChoiceList())
                );
            }
        }

        // Preload tags shared by all items in that collection
        $suggestedNames = [];
        if ($request->isMethod('GET')) {
            $item->setTags(new ArrayCollection($tagRepository->findRelatedToCollection($collection)));
            $suggestedNames = $itemNameGuesser->guess($item);
        }

        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($item);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.item_added', ['item' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));

            if ($request->request->has('save_and_add_another')) {
                return $this->redirectToRoute('app_item_add', ['collection' => $item->getCollection()->getId()]);
            }

            return $this->redirectToRoute('app_collection_show', ['id' => $item->getCollection()->getId()]);
        }

        return $this->render('App/Item/add.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
            'collection' => $collection,
            'suggestedNames' => $suggestedNames,
            'choiceLists' => $choiceListRepository->findAll(),
        ]);
    }

    #[Route(path: '/items/{id}', name: 'app_item_show', methods: ['GET'])]
    #[Route(path: '/user/{username}/items/{id}', name: 'app_shared_item_show', methods: ['GET'])]
    #[Entity('item', expr: 'repository.findById(id)', class: Item::class)]
    public function show(Item $item, ItemRepository $itemRepository): Response
    {
        $nextAndPrevious = $itemRepository->findNextAndPrevious($item, $item->getCollection());

        return $this->render('App/Item/show.html.twig', [
            'item' => $item,
            'previousItem' => $nextAndPrevious['previous'],
            'nextItem' => $nextAndPrevious['next'],
        ]);
    }

    #[Route(path: '/items/{id}/edit', name: 'app_item_edit', methods: ['GET', 'POST'])]
    #[Entity('item', expr: 'repository.findById(id)', class: Item::class)]
    public function edit(
        Request $request,
        Item $item,
        TranslatorInterface $translator,
        ManagerRegistry $managerRegistry,
        ChoiceListRepository $choiceListRepository
    ): Response {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.item_edited', ['item' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_item_show', ['id' => $item->getId()]);
        }

        return $this->render('App/Item/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
            'collection' => $item->getCollection(),
            'choiceLists' => $choiceListRepository->findAll(),
        ]);
    }

    #[Route(path: '/items/{id}/delete', name: 'app_item_delete', methods: ['POST'])]
    public function delete(Request $request, Item $item, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $collection = $item->getCollection();

        $form = $this->createDeleteForm('app_item_delete', $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($item);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.item_deleted', ['item' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_collection_show', ['id' => $collection->getId()]);
    }

    #[Route(path: '/items/{id}/loan', name: 'app_item_loan', methods: ['GET', 'POST'])]
    public function loan(Request $request, Item $item, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['loans']);

        $loan = new Loan();
        $loan->setItem($item);

        $form = $this->createForm(LoanType::class, $loan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($loan);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.loan', ['item' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_item_show', ['id' => $item->getId()]);
        }

        return $this->render('App/Loan/loan.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/items/autocomplete/{search}', name: 'app_item_autocomplete', methods: ['GET'])]
    public function autocomplete(string $search, ItemRepository $itemRepository, Packages $assetManager): JsonResponse
    {
        $items = $itemRepository->findLike($search);
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'thumbnail' => $item->getImageSmallThumbnail() ? $assetManager->getUrl($item->getImageSmallThumbnail()) : null,
            ];
        }

        return new JsonResponse($data);
    }
}
