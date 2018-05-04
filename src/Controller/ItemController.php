<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Loan;
use App\Entity\Log;
use App\Entity\Tag;
use App\Enum\DatumTypeEnum;
use App\Form\Type\Entity\ItemType;
use App\Form\Type\Entity\LoanType;
use App\Service\ItemHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ItemController
 *
 * @package App\Controller
 *
 * @Route("/items")
 */
class ItemController extends AbstractController
{
    /**
     * @Route("/add", name="app_item_add")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param ItemHelper $itemHelper
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator, ItemHelper $itemHelper) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $collection = null;
        if ($request->query->has('collection')) {
            $collection = $em->getRepository(Collection::class)->findOneBy([
                'id' => $request->query->get('collection'),
                'owner' => $this->getUser()
            ]);
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
        if ($request->isMethod('GET')) {
            $item->setTags(new ArrayCollection($this->getDoctrine()->getRepository(Tag::class)->findRelatedToCollection($collection)));
        }

        $form = $this->createForm(ItemType::class, $item, ['isCreation' => true]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($item);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.item_added', ['%item%' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));
            $route = $request->request->has('save_and_add_another') ? 'app_item_add' : 'app_collection_show';

            return $this->redirect($this->generateUrl($route, [
                'id' => $item->getCollection()->getId(),
            ]));
        }

        return $this->render('App/Item/add.html.twig', [
            'form' => $form->createView(),
            'collection' => $collection,
            'data' => $itemHelper->formatData($item->getData()),
            'fieldsType' => DatumTypeEnum::getTypesLabels(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_item_show", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
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
     * @Route("/{id}/edit", name="app_item_edit", requirements={"id"="%uuid_regex%"})
     * @Method({"GET", "POST"})
     * @Entity("item", expr="repository.findById(id)")
     *
     * @param Request $request
     * @param Item $item
     * @param TranslatorInterface $translator
     * @param ItemHelper $itemHelper
     * @return Response
     */
    public function edit(Request $request, Item $item, TranslatorInterface $translator, ItemHelper $itemHelper) : Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.item_edited', ['%item%' => '&nbsp;<strong>'.$item->getName().'</strong>&nbsp;']));

            return $this->redirect($this->generateUrl('app_item_show', [
                'id' => $item->getId(),
            ]));
        }

        return $this->render('App/Item/edit.html.twig', [
            'form' => $form->createView(),
            'data' => $itemHelper->formatData($item->getData()),
            'item' => $item,
            'fieldsType' => DatumTypeEnum::getTypesLabels(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_item_delete", requirements={"id"="%uuid_regex%"})
     * @Method({"GET", "POST"})
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

        return $this->redirect($this->generateUrl('app_collection_show', ['id' => $collection->getId()]));
    }

    /**
     * @Route("/{id}/history", name="app_item_history", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
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
     * @Route("/{id}/loan", name="app_item_loan", requirements={"id"="%uuid_regex%"})
     * @Method({"GET", "POST"})
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

            return $this->redirect($this->generateUrl('app_item_show', [
                'id' => $item->getId(),
            ]));
        }

        return $this->render('App/Loan/loan.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }
}
