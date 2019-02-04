<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Form\Type\Entity\WishlistType;
use App\Service\CounterCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class WishlistController
 *
 * @package App\Controller
 */
class WishlistController extends AbstractController
{
    /**
     * @Route("/wishlists", name="app_wishlist_index", methods={"GET"})
     * @Route("/user/{username}/wishlists", name="app_user_wishlist_index", methods={"GET"})
     * @Route("/preview/wishlists", name="app_preview_wishlist_index", methods={"GET"})
     *
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function index(CounterCalculator $counterCalculator) : Response
    {
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findAllParent();

        return $this->render('App/Wishlist/index.html.twig', [
            'wishlists' => $wishlists,
            'counters' => $counterCalculator->wishlistsCounters($wishlists)
        ]);
    }

    /**
     * @Route("/wishlists/add", name="app_wishlist_add", methods={"GET", "POST"})
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
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/wishlists/{id}", name="app_wishlist_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Route("/user/{username}/wishlists/{id}", name="app_user_wishlist_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Route("/preview/wishlists/{id}", name="app_preview_wishlist_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Entity("wishlist", expr="repository.findById(id)")
     *
     * @param Wishlist $wishlist
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function show(Wishlist $wishlist, CounterCalculator $counterCalculator) : Response
    {
        return $this->render('App/Wishlist/show.html.twig', [
            'wishlist' => $wishlist,
            'counters' => $counterCalculator->wishlistCounters($wishlist)
        ]);
    }

    /**
     * @Route("/wishlists/{id}/edit", name="app_wishlist_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
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
     * @Route("/wishlists/{id}/delete", name="app_wishlist_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
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
