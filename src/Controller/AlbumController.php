<?php

namespace App\Controller;

use App\Entity\Album;
use App\Form\Type\Entity\AlbumType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AlbumController
 *
 * @package App\Controller
 */
class AlbumController extends AbstractController
{
    /**
     * @Route("/albums", name="app_album_index", methods={"GET"})
     * @Route("/user/{username}/albums", name="app_user_album_index", methods={"GET"})
     * @Route("/preview/albums", name="app_preview_album_index", methods={"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        $albums = $this->getDoctrine()->getRepository(Album::class)->findAll();
        $photosCounter = 0;
        foreach ($albums as $album) {
            $photosCounter += \count($album->getPhotos());
        }

        return $this->render('App/Album/index.html.twig', [
            'albums' => $albums,
            'photosCounter' => $photosCounter
        ]);
    }

    /**
     * @Route("/albums/add", name="app_album_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $album = new Album();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($album);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.album_added', ['%album%' => '&nbsp;<strong>'.$album->getTitle().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_album_show', ['id' => $album->getId()]);
        }

        return $this->render('App/Album/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/albums/{id}/edit", name="app_album_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Album $album
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Album $album, TranslatorInterface $translator) : Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.album_edited', ['%album%' => '&nbsp;<strong>'.$album->getTitle().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_album_show', ['id' => $album->getId()]);
        }

        return $this->render('App/Album/edit.html.twig', [
            'form' => $form->createView(),
            'album' => $album,
        ]);
    }

    /**
     * @Route("/albums/{id}/delete", name="app_album_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Album $album
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Album $album, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($album);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.album_deleted', ['%album%' => '&nbsp;<strong>'.$album->getTitle().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_album_index');
    }

    /**
     * @Route("/albums/{id}", name="app_album_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Route("/user/{username}/albums/{id}", name="app_user_album_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Route("/preview/albums/{id}", name="app_preview_album_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @param Album $album
     * @return Response
     */
    public function show(Album $album) : Response
    {
        return $this->render('App/Album/show.html.twig', [
            'album' => $album,
        ]);
    }
}
