<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Log;
use App\Form\Type\Entity\CollectionType;
use App\Form\Type\Model\BatchTaggerType;
use App\Model\BatchTagger;
use App\Service\CounterCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CollectionController
 *
 * @package App\Controller
 *
 * @Route("/collections")
 */
class CollectionController extends AbstractController
{
    /**
     * @Route("", name="app_collection_index")
     * @Method({"GET"})
     *
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function index(CounterCalculator $counterCalculator) : Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findAllParent();

        return $this->render('App/Collection/index.html.twig', [
            'collections' => $collections,
            'counters' => $counterCalculator->collectionsCounters($collections)
        ]);
    }

    /**
     * @Route("/export", name="app_collection_export")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function export() : Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findAllWithItems();

        return $this->render('App/Collection/export.html.twig', [
            'collections' => $collections,
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/add", name="app_collection_add")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $collection = new Collection();
        $em = $this->getDoctrine()->getManager();

        if ($request->query->has('parent')) {
            $parent = $em->getRepository(Collection::class)->findOneBy([
                'id' => $request->query->get('parent'),
                'owner' => $this->getUser()
            ]);
            $collection
                ->setParent($parent)
                ->setVisibility($parent->getVisibility())
            ;
        }

        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($collection);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.collection_added', ['%collection%' => '&nbsp;<strong>'.$collection->getTitle().'</strong>&nbsp;']));

            return $this->redirect($this->generateUrl('app_collection_show', [
                'id' => $collection->getId(),
            ]));
        }

        return $this->render('App/Collection/add.html.twig', [
            'form' => $form->createView(),
            'suggestedItemsTitles' => $em->getRepository(Collection::class)->suggestItemsTitles($collection),
            'suggestedChildrenTitles' => $em->getRepository(Collection::class)->suggestChildrenTitles($collection),
        ]);
    }

    /**
     * @Route("/{id}", name="app_collection_show", requirements={"id"="%uuid_regex%"}, options={"expose"=true})
     * @Method({"GET"})
     * @Entity("collection", expr="repository.findById(id)")
     *
     * @param Collection $collection
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function show(Collection $collection, CounterCalculator $counterCalculator) : Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('App/Collection/show.html.twig', [
            'collection' => $collection,
            'children' => $em->getRepository(Collection::class)->findChildrenByCollectionId($collection->getId()),
            'items' => $em->getRepository(Item::class)->findItemsByCollectionId($collection->getId()),
            'counters' => $counterCalculator->collectionCounters($collection)
        ]);
    }

    /**
     * @Route("/{id}/items", name="app_collection_items", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     *
     * @param Collection $collection
     * @return Response
     */
    public function items(Collection $collection) : Response
    {
        return $this->render('App/Collection/items.html.twig', [
            'collection' => $collection,
            'items' => $this->getDoctrine()->getRepository(Item::class)->findAllByCollection($collection),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_collection_edit", requirements={"id"="%uuid_regex%"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Collection $collection
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Collection $collection, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(CollectionType::class, $collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.collection_edited', ['%collection%' => '&nbsp;<strong>'.$collection->getTitle().'</strong>&nbsp;']));

            return $this->redirect($this->generateUrl('app_collection_show', ['id' => $collection->getId()]));
        }

        return $this->render('App/Collection/edit.html.twig', [
            'form' => $form->createView(),
            'collection' => $collection,
            'suggestedItemsTitles' => $em->getRepository(Collection::class)->suggestItemsTitles($collection),
            'suggestedChildrenTitles' => $em->getRepository(Collection::class)->suggestChildrenTitles($collection),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_collection_delete", requirements={"id"="%uuid_regex%"})
     * @Method({"GET", "POST"})
     *
     * @param Collection $collection
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Collection $collection, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($collection);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.collection_deleted', ['%collection%' => '&nbsp;<strong>'.$collection->getTitle().'</strong>&nbsp;']));

        if (null === $collection->getParent()) {
            return $this->redirect($this->generateUrl('app_collection_index'));
        } else {
            return $this->redirect($this->generateUrl('app_collection_show', ['id' => $collection->getParent()->getId()]));
        }
    }

    /**
     * @Route("/{id}/batch-tagging", name="app_collection_batch_tagging", requirements={"id"="%uuid_regex%"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Collection $collection
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function batchTagging(Request $request, Collection $collection, TranslatorInterface $translator) : Response
    {
        $batchTagger = new BatchTagger();
        $batchTagger->setCollection($collection);
        $form = $this->createForm(BatchTaggerType::class, $batchTagger);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $itemsTaggedCount = $batchTagger->applyBatch();
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->transchoice('message.items_tagged', $itemsTaggedCount, ['%count%' => $itemsTaggedCount]));

            return $this->redirect($this->generateUrl('app_collection_show', [
                'id' => $collection->getId(),
            ]));
        }

        return $this->render('App/Collection/batch-tagging.html.twig', [
            'form' => $form->createView(),
            'collection' => $collection,
        ]);
    }

    /**
     * @Route("/{id}/history", name="app_collection_history", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     *
     * @param Collection $collection
     * @return Response
     */
    public function history(Collection $collection) : Response
    {
        return $this->render('App/Collection/history.html.twig', [
            'collection' => $collection,
            'logs' => $this->getDoctrine()->getRepository(Log::class)->findBy([
                'objectId' => $collection->getId(),
                'objectClass' => $this->getDoctrine()->getManager()->getClassMetadata(\get_class($collection))->getName(),
            ], [
                'loggedAt' => 'DESC',
                'type' => 'DESC'
            ])
        ]);
    }
}
