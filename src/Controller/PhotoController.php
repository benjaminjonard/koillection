<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
use App\Form\Type\Entity\PhotoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PhotoController extends AbstractController
{
    /**
     * @Route("/photos/add", name="app_photo_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $album = null;
        if ($request->query->has('album')) {
            $album = $em->getRepository(Album::class)->findOneBy([
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($photo);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.photo_added', ['%photo%' => '&nbsp;<strong>'.$photo->getTitle().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_album_show', ['id' => $photo->getAlbum()->getId()]);
        }

        return $this->render('App/Photo/add.html.twig', [
            'form' => $form->createView(),
            'album' => $album,
        ]);
    }

    /**
     * @Route("/photos/{id}/edit", name="app_photo_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Photo $photo
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Photo $photo, TranslatorInterface $translator) : Response
    {
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.photo_edited', ['%photo%' => '&nbsp;<strong>'.$photo->getTitle().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_album_show', ['id' => $photo->getAlbum()->getId()]);
        }

        return $this->render('App/Photo/edit.html.twig', [
            'form' => $form->createView(),
            'photo' => $photo,
        ]);
    }

    /**
     * @Route("/photos/{id}/delete", name="app_photo_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Photo $photo
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Photo $photo, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($photo);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.photo_deleted', ['%photo%' => '&nbsp;<strong>'.$photo->getTitle().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_album_show', ['id' => $photo->getAlbum()->getId()]);
    }
}
