<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Wishlist;
use App\Form\Type\Entity\DisplayConfigurationType;
use App\Form\Type\Entity\WishlistType;
use App\Repository\WishlistRepository;
use App\Repository\WishRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WishlistController extends AbstractController
{
    #[Route(path: '/wishlists', name: 'app_wishlist_index', methods: ['GET'])]
    #[Route(path: '/user/{username}/wishlists', name: 'app_shared_wishlist_index', methods: ['GET'])]
    public function index(WishlistRepository $wishlistRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $wishlists = $wishlistRepository->findBy(['parent' => null], ['name' => Criteria::ASC]);

        $wishlistsCounter = \count($wishlists);
        $wishesCounter = 0;
        foreach ($wishlists as $wishlist) {
            $wishlistsCounter += $wishlist->getCachedValues()['counters']['children'] ?? 0;
            $wishesCounter += $wishlist->getCachedValues()['counters']['wishes'] ?? 0;
        }

        return $this->render('App/Wishlist/index.html.twig', [
            'wishlists' => $wishlists,
            'wishlistsCounter' => $wishlistsCounter,
            'wishesCounter' => $wishesCounter,
        ]);
    }

    #[Route(path: '/wishlists/edit', name: 'app_wishlist_edit_index', methods: ['GET', 'POST'])]
    public function editIndex(
        Request $request,
        TranslatorInterface $translator,
        ManagerRegistry $managerRegistry
    ): Response {
        $form = $this->createForm(DisplayConfigurationType::class, $this->getUser()->getWishlistsDisplayConfiguration());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wishlist_index_edited'));

            return $this->redirectToRoute('app_wishlist_index');
        }

        return $this->render('App/Wishlist/edit_index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/wishlists/add', name: 'app_wishlist_add', methods: ['GET', 'POST'])]
    public function add(Request $request, WishlistRepository $wishlistRepository, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $wishlist = new Wishlist();
        if ($request->query->has('parent')) {
            $parent = $wishlistRepository->findOneBy([
                'id' => $request->query->get('parent'),
                'owner' => $this->getUser(),
            ]);
            $wishlist
                ->setParent($parent)
                ->setVisibility($parent->getVisibility())
                ->setParentVisibility($parent->getVisibility())
                ->setFinalVisibility($parent->getFinalVisibility())
            ;
        }

        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($wishlist);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.wishlist_added', ['wishlist' => $wishlist->getName()]));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }

        return $this->render('App/Wishlist/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/wishlists/{id}', name: 'app_wishlist_show', methods: ['GET'])]
    #[Route(path: '/user/{username}/wishlists/{id}', name: 'app_shared_wishlist_show', methods: ['GET'])]
    public function show(Wishlist $wishlist, WishlistRepository $wishlistRepository, WishRepository $wishRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        return $this->render('App/Wishlist/show.html.twig', [
            'wishlist' => $wishlist,
            'children' => $wishlistRepository->findBy(['parent' => $wishlist]),
            'wishes' => $wishRepository->findBy(['wishlist' => $wishlist]),
        ]);
    }

    #[Route(path: '/wishlists/{id}/edit', name: 'app_wishlist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Wishlist $wishlist, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wishlist_edited', ['wishlist' => $wishlist->getName()]));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }

        return $this->render('App/Wishlist/edit.html.twig', [
            'form' => $form->createView(),
            'wishlist' => $wishlist,
        ]);
    }

    #[Route(path: '/wishlists/{id}/delete', name: 'app_wishlist_delete', methods: ['POST'])]
    public function delete(Request $request, Wishlist $wishlist, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createDeleteForm('app_wish_delete', $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($wishlist);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wishlist_deleted', ['wishlist' => $wishlist->getName()]));
        }

        return $this->redirectToRoute('app_wishlist_index');
    }
}
