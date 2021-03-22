<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Log;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Form\Type\Entity\WishlistType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WishlistController extends AbstractController
{
    #[Route(
        path: ['en' => '/wishlists', 'fr' => '/listes-de-souhaits'],
        name: 'app_wishlist_index', methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/user/{username}/wishlists', 'fr' => '/utilisateur/{username}/listes-de-souhaits'],
        name: 'app_user_wishlist_index', methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/preview/wishlists', 'fr' => '/apercu/listes-de-souhaits'],
        name: 'app_preview_wishlist_index', methods: ['GET']
    )]
    public function index() : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('App/Wishlist/index.html.twig', [
            'wishlists' => $wishlists
        ]);
    }

    #[Route(
        path: ['en' => '/wishlists/add', 'fr' => '/listes-de-souhaits/ajouter'],
        name: 'app_wishlist_add', methods: ['GET', 'POST']
    )]
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $wishlist = new Wishlist();
        $em = $this->getDoctrine()->getManager();

        if ($request->query->has('parent')) {
            $parent = $em->getRepository(Wishlist::class)->findOneBy([
                'id' => $request->query->get('parent'),
                'owner' => $this->getUser()
            ]);
            $wishlist
                ->setParent($parent)
                ->setVisibility($parent->getVisibility())
            ;
        }

        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($wishlist);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.wishlist_added', ['%wishlist%' => '&nbsp;<strong>'.$wishlist->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }

        return $this->render('App/Wishlist/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(
        path: ['en' => '/wishlists/{id}', 'fr' => '/listes-de-souhaits/{id}'],
        name: 'app_wishlist_show', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/user/{username}/wishlists/{id}', 'fr' => '/utilisateur/{username}/listes-de-souhaits/{id}'],
        name: 'app_user_wishlist_show', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/preview/wishlists/{id}', 'fr' => '/apercu/listes-de-souhaits/{id}'],
        name: 'app_preview_wishlist_show', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    public function show(Wishlist $wishlist) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        return $this->render('App/Wishlist/show.html.twig', [
            'wishlist' => $wishlist,
            'children' => $this->getDoctrine()->getRepository(Wishlist::class)->findBy(['parent' => $wishlist]),
            'wishes' => $this->getDoctrine()->getRepository(Wish::class)->findBy(['wishlist' => $wishlist])
        ]);
    }

    #[Route(
        path: ['en' => '/wishlists/{id}/edit', 'fr' => '/listes-de-souhaits/{id}/editer'],
        name: 'app_wishlist_edit', requirements: ['id' => '%uuid_regex%'], methods: ['GET', 'POST']
    )]
    public function edit(Request $request, Wishlist $wishlist, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.wishlist_edited', ['%wishlist%' => '&nbsp;<strong>'.$wishlist->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }

        return $this->render('App/Wishlist/edit.html.twig', [
            'form' => $form->createView(),
            'wishlist' => $wishlist,
        ]);
    }

    #[Route(
        path: ['en' => '/wishlists/{id}/delete', 'fr' => '/listes-de-souhaits/{id}/supprimer'],
        name: 'app_wishlist_delete', requirements: ['id' => '%uuid_regex%'], methods: ['DELETE']
    )]
    public function delete(Request $request, Wishlist $wishlist, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists']);

        $form = $this->createDeleteForm('app_wish_delete', $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($wishlist);
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.wishlist_deleted', ['%wishlist%' => '&nbsp;<strong>'.$wishlist->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_wishlist_index');
    }

    #[Route(
        path: ['en' => '/wishlists/{id}/history', 'fr' => '/listes-de-souhaits/{id}/historique'],
        name: 'app_wishlist_history', requirements: ['id' => '%uuid_regex%'], methods: ['GET', 'POST']
    )]
    public function history(Wishlist $wishlist) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['wishlists', 'history']);

        return $this->render('App/Wishlist/history.html.twig', [
            'wishlist' => $wishlist,
            'logs' => $this->getDoctrine()->getRepository(Log::class)->findBy([
                'objectId' => $wishlist->getId(),
                'objectClass' => $this->getDoctrine()->getManager()->getClassMetadata(\get_class($wishlist))->getName(),
            ], [
                'loggedAt' => 'DESC',
                'type' => 'DESC'
            ])
        ]);
    }
}
