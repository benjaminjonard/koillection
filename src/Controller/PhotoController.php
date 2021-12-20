<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Photo;
use App\Form\Type\Entity\PhotoType;
use App\Repository\AlbumRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PhotoController extends AbstractController
{
    #[Route(
        path: ['en' => '/photos/ajouter', 'fr' => '/photos/add'],
        name: 'app_photo_add', methods: ['GET', 'POST']
    )]
    public function add(Request $request, AlbumRepository $albumRepository, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $album = null;
        if ($request->query->has('album')) {
            $album = $albumRepository->findOneBy([
                'id' => $request->query->get('album'),
                'owner' => $this->getUser()
            ]);
        }

        if (!$album) {
            throw $this->createNotFoundException();
        }

        $photo = new Photo();
        $photo
            ->setAlbum($album)
            ->setVisibility($album->getVisibility())
        ;

        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($photo);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.photo_added', ['%photo%' => '&nbsp;<strong>'.$photo->getTitle().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_album_show', ['id' => $photo->getAlbum()->getId()]);
        }

        return $this->render('App/Photo/add.html.twig', [
            'form' => $form->createView(),
            'album' => $album,
        ]);
    }

    #[Route(
        path: ['en' => '/photos/{id}/edit', 'fr' => '/photos/{id}/editer'],
        name: 'app_photo_edit', requirements: ['id' => '%uuid_regex%'], methods: ['GET', 'POST']
    )]
    public function edit(Request $request, Photo $photo, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.photo_edited', ['%photo%' => '&nbsp;<strong>'.$photo->getTitle().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_album_show', ['id' => $photo->getAlbum()->getId()]);
        }

        return $this->render('App/Photo/edit.html.twig', [
            'form' => $form->createView(),
            'photo' => $photo,
        ]);
    }

    #[Route(
        path: ['en' => '/photos/{id}/delete', 'fr' => '/photos/{id}/supprimer'],
        name: 'app_photo_delete', requirements: ['id' => '%uuid_regex%'], methods: ['POST']
    )]
    public function delete(Request $request, Photo $photo, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $form = $this->createDeleteForm('app_photo_delete', $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($photo);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.photo_deleted', ['%photo%' => '&nbsp;<strong>'.$photo->getTitle().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_album_show', ['id' => $photo->getAlbum()->getId()]);
    }
}
