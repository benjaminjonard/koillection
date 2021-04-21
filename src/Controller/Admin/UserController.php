<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\DatumTypeEnum;
use App\Form\Type\Entity\Admin\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    #[Route(
        path: ['en' => '/admin/users', 'fr' => '/admin/utilisateurs'],
        name: 'app_admin_user_index', methods: ['GET']
    )]
    public function index() : Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('App/Admin/User/index.html.twig', [
            'users' => $em->getRepository(User::class)->findBy([], ['lastDateOfActivity' => 'DESC'])
        ]);
    }

    #[Route(
        path: ['en' => '/admin/users/add', 'fr' => '/admin/utilisateurs/ajouter'],
        name: 'app_admin_user_add', methods: ['GET', 'POST']
    )]
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.user_added', ['%user%' => '&nbsp;<strong>'.$user->getUsername().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_admin_user_index', ['id' => $user->getId()]);
        }

        return $this->render('App/Admin/User/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(
        path: ['en' => '/admin/users/{id}/edit', 'fr' => '/admin/utilisateurs/{id}/editer'],
        name: 'app_admin_user_edit', methods: ['GET', 'POST']
    )]
    public function edit(Request $request, User $user, TranslatorInterface $translator) : Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.user_edited', ['%user%' => '&nbsp;<strong>'.$user->getUsername().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_admin_user_index', ['id' => $user->getId()]);
        }

        $em = $this->getDoctrine()->getManager();

        return $this->render('App/Admin/User/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'counters' => [
                'collections' => $em->getRepository(Collection::class)->count(['owner' => $user]),
                'items' => $em->getRepository(Item::class)->count(['owner' => $user]),
                'tags' => $em->getRepository(Tag::class)->count(['owner' => $user]),
                'wishlists' => $em->getRepository(Wishlist::class)->count(['owner' => $user]),
                'wishes' => $em->getRepository(Wish::class)->count(['owner' => $user]),
                'albums' => $em->getRepository(Album::class)->count(['owner' => $user]),
                'photos' => $em->getRepository(Photo::class)->count(['owner' => $user]),
                'signs' => $em->getRepository(Datum::class)->count(['owner' => $user, 'type' => DatumTypeEnum::TYPE_SIGN]),
            ],
        ]);
    }
    
    #[Route(
        path: ['en' => '/admin/users/{id}/delete', 'fr' => '/admin/utilisateurs/{id}/supprimer'],
        name: 'app_admin_user_delete', requirements: ['id' => '%uuid_regex%'], methods: ['DELETE']
    )]
    public function delete(Request $request, User $user, TranslatorInterface $translator) : Response
    {
        if ($user->isAdmin()) {
            return $this->render('App/Admin/User/delete.html.twig', [
                'user' => $user,
                'error' =>  $translator->trans('error.cannot_delete_admin_user')
            ]);
        }

        $form = $this->createDeleteForm('app_admin_user_delete', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            $this->addFlash('notice', $translator->trans('message.user_deleted', ['%user%' => '&nbsp;<strong>'.$user->getUsername().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_admin_user_index');
    }
}
