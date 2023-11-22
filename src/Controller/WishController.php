<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Wish;
use App\Enum\DatumTypeEnum;
use App\Form\Type\Entity\ItemType;
use App\Form\Type\Entity\WishType;
use App\Form\Type\Model\ScrapingItemType;
use App\Model\ScrapingItem;
use App\Repository\ChoiceListRepository;
use App\Repository\WishlistRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WishController extends AbstractController
{
    #[Route(path: '/wishes/add', name: 'app_wish_add', methods: ['GET', 'POST'])]
    public function add(Request $request, WishlistRepository $wishlistRepository, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $wishlist = null;
        if ($request->query->has('wishlist')) {
            $wishlist = $wishlistRepository->findOneBy([
                'id' => $request->query->get('wishlist'),
                'owner' => $this->getUser(),
            ]);
        }

        if ($wishlist === null) {
            throw $this->createNotFoundException();
        }

        $wish = new Wish();
        $wish
            ->setWishlist($wishlist)
            ->setVisibility($wishlist->getVisibility())
            ->setParentVisibility($wishlist->getVisibility())
            ->setFinalVisibility($wishlist->getFinalVisibility())
            ->setCurrency($this->getUser()->getCurrency())
        ;

        $form = $this->createForm(WishType::class, $wish);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($wish);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.wish_added', ['wish' => $wish->getName()]));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wish->getWishlist()->getId()]);
        }

        return $this->render('App/Wish/add.html.twig', [
            'form' => $form,
            'wishlist' => $wishlist,
        ]);
    }

    #[Route(path: '/wishes/{id}/edit', name: 'app_wish_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Wish $wish, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wish_edited', ['wish' => $wish->getName()]));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wish->getWishlist()->getId()]);
        }

        return $this->render('App/Wish/edit.html.twig', [
            'form' => $form,
            'wish' => $wish,
        ]);
    }

    #[Route(path: '/wishes/{id}/delete', name: 'app_wish_delete', methods: ['POST'])]
    public function delete(Request $request, Wish $wish, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createDeleteForm('app_wish_delete', $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($wish);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wish_deleted', ['wish' => $wish->getName()]));
        }

        return $this->redirectToRoute('app_wishlist_show', ['id' => $wish->getWishlist()->getId()]);
    }

    #[Route(path: '/wishes/{id}/transfer', name: 'app_wish_transfer_to_collection', methods: ['GET', 'POST'])]
    public function transferToCollection(
        Request $request,
        Wish $wish,
        TranslatorInterface $translator,
        ManagerRegistry $managerRegistry,
        ChoiceListRepository $choiceListRepository
    ): Response {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $item = new Item();

        $item
            ->setVisibility($wish->getVisibility())
            ->setParentVisibility($wish->getVisibility())
            ->setFinalVisibility($wish->getFinalVisibility())
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
                'wish' => $wish->getName(),
                'collection' => $item->getCollection()->getTitle(),
            ]));

            return $this->redirectToRoute('app_item_show', ['id' => $item->getId()]);
        }

        return $this->render('App/Wish/transfer_to_collection.html.twig', [
            'form' => $form,
            'scrapingForm' => $this->createForm(ScrapingItemType::class, new ScrapingItem()),
            'item' => $item,
            'wish' => $wish,
            'fieldsType' => DatumTypeEnum::getTypesLabels(),
            'choiceLists' => $choiceListRepository->findAll(),
        ]);
    }
}
