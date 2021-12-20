<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Wish;
use App\Enum\DatumTypeEnum;
use App\Form\Type\Entity\ItemType;
use App\Form\Type\Entity\WishType;
use App\Repository\WishlistRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WishController extends AbstractController
{
    #[Route(
        path: ['en' => '/wishes/add', 'fr' => '/souhaits/ajouter'],
        name: 'app_wish_add', methods: ['GET', 'POST']
    )]
    public function add(Request $request, WishlistRepository $wishlistRepository, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $wishlist = null;
        if ($request->query->has('wishlist')) {
            $wishlist = $wishlistRepository->findOneBy([
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
            ->setCurrency($this->getUser()->getCurrency())
        ;

        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($wish);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.wish_added', ['%wish%' => '&nbsp;<strong>'.$wish->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wish->getWishlist()->getId()]);
        }

        return $this->render('App/Wish/add.html.twig', [
            'form' => $form->createView(),
            'wishlist' => $wishlist,
        ]);
    }

    #[Route(
        path: ['en' => '/wishes/{id}/edit', 'fr' => '/souhaits/{id}/editer'],
        name: 'app_wish_edit', requirements: ['id' => '%uuid_regex%'], methods: ['GET', 'POST']
    )]
    public function edit(Request $request, Wish $wish, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wish_edited', ['%wish%' => '&nbsp;<strong>'.$wish->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wish->getWishlist()->getId()]);
        }

        return $this->render('App/Wish/edit.html.twig', [
            'form' => $form->createView(),
            'wish' => $wish,
        ]);
    }

    #[Route(
        path: ['en' => '/wishes/{id}/delete', 'fr' => '/souhaits/{id}/supprimer'],
        name: 'app_wish_delete', requirements: ['id' => '%uuid_regex%'], methods: ['POST']
    )]
    public function delete(Request $request, Wish $wish, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createDeleteForm('app_wish_delete', $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($wish);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wish_deleted', ['%wish%' => '&nbsp;<strong>'.$wish->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_wishlist_show', ['id' => $wish->getWishlist()->getId()]);
    }

    #[Route(
        path: ['en' => '/wishes/{id}/transfer', 'fr' => '/souhaits/{id}/transferer'],
        name: 'app_wish_transfer_to_collection', requirements: ['id' => '%uuid_regex%'], methods: ['GET', 'POST']
    )]
    public function transferToCollection(Request $request, Wish $wish, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $item = new Item();

        $item
            ->setVisibility($wish->getVisibility())
            ->setName($wish->getName())
            ->setImage($wish->getImage())
            ->setImageSmallThumbnail($wish->getImageSmallThumbnail())
        ;

        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($item);
            $managerRegistry->getManager()->remove($wish);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.wish_transfered', [
                '%wish%' => '&nbsp;<strong>'.$wish->getName().'</strong>&nbsp;',
                '%collection%' => '&nbsp;<strong>'.$item->getCollection()->getTitle().'</strong>&nbsp;'
            ]));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wish->getWishlist()->getId()]);
        }

        return $this->render('App/Wish/transfer_to_collection.html.twig', [
            'form' => $form->createView(),
            'item' => $item,
            'wish' => $wish,
            'fieldsType' => DatumTypeEnum::getTypesLabels(),
        ]);
    }
}
