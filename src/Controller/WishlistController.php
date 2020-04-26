<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Form\Type\Entity\WishlistType;
use App\Service\CounterCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WishlistController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/wishlists",
     *     "fr": "/listes-de-souhaits"
     * }, name="app_wishlist_index", methods={"GET"})
     *
     * @Route({
     *     "en": "/user/{username}/wishlists",
     *     "fr": "/utilisateur/{username}/listes-de-souhaits"
     * }, name="app_user_wishlist_index", methods={"GET"})
     *
     * @Route({
     *     "en": "/preview/wishlists",
     *     "fr": "/apercu/listes-de-souhaits"
     * }, name="app_preview_wishlist_index", methods={"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('App/Wishlist/index.html.twig', [
            'wishlists' => $wishlists
        ]);
    }

    /**
     * @Route({
     *     "en": "/wishlists/add",
     *     "fr": "/listes-de-souhaits/ajouter"
     * }, name="app_wishlist_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
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

    /**
     * @Route({
     *     "en": "/wishlists/{id}",
     *     "fr": "/listes-de-souhaits/{id}"
     * }, name="app_wishlist_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Route({
     *     "en": "/user/{username}/wishlists/{id}",
     *     "fr": "/utilisateur/{username}/listes-de-souhaits/{id}"
     * }, name="app_user_wishlist_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Route({
     *     "en": "/preview/wishlists/{id}",
     *     "fr": "/apercu/listes-de-souhaits/{id}"
     * }, name="app_preview_wishlist_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     *
     * @param Wishlist $wishlist
     * @return Response
     */
    public function show(Wishlist $wishlist) : Response
    {
        return $this->render('App/Wishlist/show.html.twig', [
            'wishlist' => $wishlist,
            'children' => $this->getDoctrine()->getRepository(Wishlist::class)->findBy(['parent' => $wishlist]),
            'wishes' => $this->getDoctrine()->getRepository(Wish::class)->findBy(['wishlist' => $wishlist])
        ]);
    }

    /**
     * @Route({
     *     "en": "/wishlists/{id}/edit",
     *     "fr": "/listes-de-souhaits/{id}/editer"
     * }, name="app_wishlist_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Wishlist $wishlist
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Wishlist $wishlist, TranslatorInterface $translator) : Response
    {
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

    /**
     * @Route({
     *     "en": "/wishlists/{id}/delete",
     *     "fr": "/listes-de-souhaits/{id}/supprimer"
     * }, name="app_wishlist_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Wishlist $wishlist
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Wishlist $wishlist, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($wishlist);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.wishlist_deleted', ['%wishlist%' => '&nbsp;<strong>'.$wishlist->getName().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_wishlist_index');
    }
}
