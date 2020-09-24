<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Loan;
use App\Entity\Log;
use App\Entity\Tag;
use App\Enum\DatumTypeEnum;
use App\Form\Type\Entity\ItemType;
use App\Form\Type\Entity\LoanType;
use App\Service\ItemNameGuesser;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ItemController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/items/add",
     *     "fr": "/objets/ajouter"
     * }, name="app_item_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param ItemNameGuesser $itemNameGuesser
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator, ItemNameGuesser $itemNameGuesser) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $collection = null;
        if ($request->query->has('collection')) {
            $collection = $em->getRepository(Collection::class)->find($request->query->get('collection'));
        }

        if (!$collection) {
            throw $this->createNotFoundException();
        }

        $item = new Item();
        $item
            ->setCollection($collection)
            ->setVisibility($collection->getVisibility())
        ;

        //Preload tags shared by all items in that collection
        $suggestedNames = [];
        if ($request->isMethod('GET')) {
            $item->setTags(new ArrayCollection($this->getDoctrine()->getRepository(Tag::class)->findRelatedToCollection($collection)));
            $suggestedNames = $itemNameGuesser->guess($item);
        }

        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($item);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.item_added', ['%item%' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));

            if ($request->request->has('save_and_add_another')) {
                return $this->redirectToRoute('app_item_add', ['collection' => $item->getCollection()->getId()]);
            }

            return $this->redirectToRoute('app_collection_show', ['id' => $item->getCollection()->getId()]);
        }

        return $this->render('App/Item/add.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
            'collection' => $collection,
            'suggestedNames' => $suggestedNames
        ]);
    }

    /**
     * @Route({
     *     "en": "/items/{id}",
     *     "fr": "/objets/{id}"
     * }, name="app_item_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Route({
     *     "en": "/user/{username}/items/{id}",
     *     "fr": "/utilisateur/{username}/objets/{id}"
     * }, name="app_user_item_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Route({
     *     "en": "/preview/items/{id}",
     *     "fr": "/apercu/objets/{id}"
     * }, name="app_preview_item_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Entity("item", expr="repository.findById(id)")
     *
     * @param Item $item
     * @return Response
     */
    public function show(Item $item) : Response
    {
        $nextAndPrevious = $this->getDoctrine()->getRepository(Item::class)->findNextAndPrevious($item);

        return $this->render('App/Item/show.html.twig', [
            'item' => $item,
            'previousItem' => $nextAndPrevious['previous'],
            'nextItem' => $nextAndPrevious['next']
        ]);
    }

    /**
     * @Route({
     *     "en": "/items/{id}/edit",
     *     "fr": "/objets/{id}/editer"
     * }, name="app_item_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @Entity("item", expr="repository.findById(id)")
     *
     * @param Request $request
     * @param Item $item
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Item $item, TranslatorInterface $translator) : Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.item_edited', ['%item%' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_item_show', ['id' => $item->getId()]);
        }

        return $this->render('App/Item/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
            'collection' => $item->getCollection(),
        ]);
    }

    /**
     * @Route({
     *     "en": "/items/{id}/delete",
     *     "fr": "/objets/{id}/supprimer"
     * }, name="app_item_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Item $item
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Item $item, TranslatorInterface $translator) : Response
    {
        $collection = $item->getCollection();
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.item_deleted', ['%item%' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_collection_show', ['id' => $collection->getId()]);
    }

    /**
     * @Route({
     *     "en": "/items/{id}/history",
     *     "fr": "/objets/{id}/historique"
     * }, name="app_item_history", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @param Item $item
     * @return Response
     */
    public function history(Item $item) : Response
    {
        return $this->render('App/Item/history.html.twig', [
            'item' => $item,
            'logs' => $this->getDoctrine()->getRepository(Log::class)->findBy([
                'objectId' => $item->getId(),
                'objectClass' => $this->getDoctrine()->getManager()->getClassMetadata(\get_class($item))->getName(),
            ], [
                'loggedAt' => 'DESC',
                'type' => 'DESC'
            ])
        ]);
    }

    /**
     * @Route({
     *     "en": "/items/{id}/loan",
     *     "fr": "/objets/{id}/preter"
     * }, name="app_item_loan", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Item $item
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function loan(Request $request, Item $item, TranslatorInterface $translator) : Response
    {
        $loan = new Loan();
        $loan->setItem($item);
        $form = $this->createForm(LoanType::class, $loan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($loan);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.loan', ['%item%' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_item_show', ['id' => $item->getId()]);
        }

        return $this->render('App/Loan/loan.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }
}
