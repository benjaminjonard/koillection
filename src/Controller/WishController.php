<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Medium;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\DatumTypeEnum;
use App\Form\Type\Entity\ItemType;
use App\Form\Type\Entity\WishType;
use App\Service\ItemHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class WishController
 *
 * @package App\Controller
 *
 * @Route("/wishes")
 */
class WishController extends AbstractController
{
    /**
     * @Route("/add", name="app_wish_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $wishlist = null;
        if ($request->query->has('wishlist')) {
            $wishlist = $em->getRepository(Wishlist::class)->findOneBy([
                'id' => $request->query->get('wishlist'),
                'owner' => $this->getUser()
            ]);
        }

        if (!$wishlist) {
            throw $this->createNotFoundException();
        }

        $wish = new Wish();
        $wish
            ->setWishlist($wishlist)
            ->setVisibility($wishlist->getVisibility())
        ;

        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($wish);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.wish_added', ['%wish%' => '&nbsp;<strong>'.$wish->getName().'</strong>&nbsp;']));

            return $this->redirect($this->generateUrl('app_wishlist_show', [
                'id' => $wish->getWishlist()->getId(),
            ]));
        }

        return $this->render('App/Wish/add.html.twig', [
            'form' => $form->createView(),
            'wishlist' => $wishlist,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_wish_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Wish $wish
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Wish $wish, TranslatorInterface $translator) : Response
    {
        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wish_edited', ['%wish%' => '&nbsp;<strong>'.$wish->getName().'</strong>&nbsp;']));

            return $this->redirect($this->generateUrl('app_wishlist_show', [
                'id' => $wish->getWishlist()->getId(),
            ]));
        }

        return $this->render('App/Wish/edit.html.twig', [
            'form' => $form->createView(),
            'wish' => $wish,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_wish_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Wish $wish
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Wish $wish, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($wish);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.wish_deleted', ['%wish%' => '&nbsp;<strong>'.$wish->getName().'</strong>&nbsp;']));

        return $this->redirect($this->generateUrl('app_wishlist_show', ['id' => $wish->getWishlist()->getId()]));
    }

    /**
     * @Route("/{id}/transfer-to-collection", name="app_wish_transfer_to_collection", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Wish $wish
     * @param TranslatorInterface $translator
     * @param ItemHelper $itemHelper
     * @return Response
     */
    public function transferToCollection(Request $request, Wish $wish, TranslatorInterface $translator, ItemHelper $itemHelper) : Response
    {
        $item = new Item();
        $item
            ->setVisibility($wish->getVisibility())
            ->setName($wish->getName())
        ;

        if ($wish->getImage() instanceof Medium) {
            $item->setImage(
                (new Medium())
                    ->setPath($wish->getImage()->getPath())
                    ->setThumbnailPath($wish->getImage()->getThumbnailPath())
                    ->setSize($wish->getImage()->getSize())
                    ->setThumbnailSize($wish->getImage()->getThumbnailSize())
                    ->setMimetype($wish->getImage()->getFilename())
                    ->setFilename($wish->getImage()->getFilename())
                    ->setType($wish->getImage()->getType())
                    ->preventFileRemoval()
            );
        }

        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$item->getImage()->fileCanBeDeleted()) {
                $wish->getImage()->preventFileRemoval();
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->remove($wish);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.wish_transfered', [
                '%wish%' => '&nbsp;<strong>'.$wish->getName().'</strong>&nbsp;',
                '%collection%' => '&nbsp;<strong>'.$item->getCollection()->getTitle().'</strong>&nbsp;'
            ]));

            return $this->redirect($this->generateUrl('app_wishlist_show', [
                'id' => $wish->getWishlist()->getId(),
            ]));
        }

        return $this->render('App/Wish/transfer-to-collection.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
            'wish' => $wish,
            'data' => $itemHelper->formatData($item->getData()),
            'fieldsType' => DatumTypeEnum::getTypesLabels(),
        ]);
    }
}
