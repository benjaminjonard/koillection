<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Log;
use App\Entity\Photo;
use App\Form\Type\Entity\AlbumType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AlbumController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/albums",
     *     "fr": "/albums"
     * }, name="app_album_index", methods={"GET"})
     *
     * @Route({
     *     "en": "/user/{username}/albums",
     *     "fr": "/utilisateur/{username}/albums"
     * }, name="app_user_album_index", methods={"GET"})
     *
     * @Route({
     *     "en": "/preview/albums",
     *     "fr": "/apercu/albums"
     * }, name="app_preview_album_index", methods={"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $albums = $this->getDoctrine()->getRepository(Album::class)->findBy(['parent' => null], ['title' => 'ASC']);
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
     * @Route({
     *     "en": "/albums/add",
     *     "fr": "/albums/ajouter"
     * }, name="app_album_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $album = new Album();
        $em = $this->getDoctrine()->getManager();

        if ($request->query->has('parent')) {
            $parent = $em->getRepository(Album::class)->findOneBy([
                'id' => $request->query->get('parent'),
                'owner' => $this->getUser()
            ]);
            $album
                ->setParent($parent)
                ->setVisibility($parent->getVisibility())
            ;
        }

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
     * @Route({
     *     "en": "/albums/{id}/edit",
     *     "fr": "/albums/{id}/editer"
     * }, name="app_album_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Album $album
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Album $album, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

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
     * @Route({
     *     "en": "/albums/{id}/delete",
     *     "fr": "/albums/{id}/supprimer"
     * }, name="app_album_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Album $album
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Album $album, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $em = $this->getDoctrine()->getManager();
        $em->remove($album);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.album_deleted', ['%album%' => '&nbsp;<strong>'.$album->getTitle().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_album_index');
    }

    /**
     * @Route({
     *     "en": "/albums/{id}",
     *     "fr": "/albums/{id}"
     * }, name="app_album_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Route({
     *     "en": "/user/{username}/albums/{id}",
     *     "fr": "/utilisateur/{username}/albums/{id}"
     * }, name="app_user_album_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Route({
     *     "en": "/preview/albums/{id}",
     *     "fr": "/apercu/albums/{id}"
     * }, name="app_preview_album_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @param Album $album
     * @return Response
     */
    public function show(Album $album) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums']);

        $em = $this->getDoctrine()->getManager();
        return $this->render('App/Album/show.html.twig', [
            'album' => $album,
            'children' => $em->getRepository(Album::class)->findBy(['parent' => $album]),
            'photos' => $em->getRepository(Photo::class)->findBy(['album' => $album])
        ]);
    }

    /**
     * @Route({
     *     "en": "/albums/{id}/history",
     *     "fr": "/albums/{id}/historique"
     * }, name="app_album_history", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @param Album $album
     * @return Response
     */
    public function history(Album $album) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['albums', 'history']);
        
        return $this->render('App/Album/history.html.twig', [
            'album' => $album,
            'logs' => $this->getDoctrine()->getRepository(Log::class)->findBy([
                'objectId' => $album->getId(),
                'objectClass' => $this->getDoctrine()->getManager()->getClassMetadata(\get_class($album))->getName(),
            ], [
                'loggedAt' => 'DESC',
                'type' => 'DESC'
            ])
        ]);
    }
}
