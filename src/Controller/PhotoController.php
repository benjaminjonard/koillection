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
    #[Route(path: '/photos/add', name: 'app_photo_add', methods: ['GET', 'POST'])]
    public function add(Request $request, AlbumRepository $albumRepository, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $album = null;
        if ($request->query->has('album')) {
            $album = $albumRepository->findOneBy([
                'id' => $request->query->get('album'),
                'owner' => $this->getUser(),
            ]);
        }

        if ($album === null) {
            throw $this->createNotFoundException();
        }

        $photo = new Photo();
        $photo
            ->setAlbum($album)
            ->setVisibility($album->getVisibility())
            ->setParentVisibility($album->getVisibility())
            ->setFinalVisibility($album->getFinalVisibility())
        ;

        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($photo);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.photo_added', ['photo' => $photo->getTitle()]));

            return $this->redirectToRoute('app_album_show', ['id' => $photo->getAlbum()->getId()]);
        }

        return $this->render('App/Photo/add.html.twig', [
            'form' => $form->createView(),
            'album' => $album,
        ]);
    }

    #[Route(path: '/photos/{id}/edit', name: 'app_photo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Photo $photo, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.photo_edited', ['photo' => $photo->getTitle()]));

            return $this->redirectToRoute('app_album_show', ['id' => $photo->getAlbum()->getId()]);
        }

        return $this->render('App/Photo/edit.html.twig', [
            'form' => $form->createView(),
            'photo' => $photo,
        ]);
    }

    #[Route(path: '/photos/{id}/delete', name: 'app_photo_delete', methods: ['POST'])]
    public function delete(Request $request, Photo $photo, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $form = $this->createDeleteForm('app_photo_delete', $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($photo);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.photo_deleted', ['photo' => $photo->getTitle()]));
        }

        return $this->redirectToRoute('app_album_show', ['id' => $photo->getAlbum()->getId()]);
    }
}
