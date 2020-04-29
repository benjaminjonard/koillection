<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Log;
use App\Entity\Tag;
use App\Form\Type\Entity\TagType;
use App\Service\ContextHandler;
use App\Service\PaginatorFactory;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TagController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/tags",
     *     "fr": "/tags"
     * }, name="app_tag_index", methods={"GET"})
     *
     * @Route({
     *     "en": "/user/{username}/tags",
     *     "fr": "/utilisateur/{username}/tags"
     * }, name="app_user_tag_index", methods={"GET"})
     *
     * @Route({
     *     "en": "/preview/tags",
     *     "fr": "/apercu/tags"
     * }, name="app_preview_tag_index", methods={"GET"})
     *
     * @param Request $request
     * @param ContextHandler $contextHandler
     * @param PaginatorFactory $paginatorFactory
     * @return Response
     * @throws NonUniqueResultException
     */
    public function index(Request $request, ContextHandler $contextHandler, PaginatorFactory $paginatorFactory) : Response
    {
        $context = $contextHandler->getContext();
        $page = $request->query->get('page', 1);
        $search = $request->query->get('search', null);
        $em = $this->getDoctrine()->getManager();
        $itemsCount = $em->getRepository(Item::class)->count([]);
        $tagsCount = $em->getRepository(Tag::class)->countForPagination($search, $context);
        $results = $em->getRepository(Tag::class)->findTagsPaginatedWithItemsCount(
            $itemsCount, $paginatorFactory->getPaginationItemsPerPage(), $page, $search, $context
        );

        if ($request->isXmlHttpRequest()) {
            return $this->render('App/Tag/_tags_table.html.twig', [
                'results' => $results,
                'paginator' => $paginatorFactory->generate($tagsCount)
            ]);
        }

        return $this->render('App/Tag/index.html.twig', [
            'results' => $results,
            'search' => $search,
            'tagsCount' => $tagsCount,
            'paginator' => $paginatorFactory->generate($tagsCount)
        ]);
    }

    /**
     * @Route({
     *     "en": "/tags/{id}",
     *     "fr": "/tags/{id}"
     * }, name="app_tag_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Route({
     *     "en": "/user/{username}/tags/{id}",
     *     "fr": "/utilisateur/{username}/tags/{id}"
     * }, name="app_user_tag_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Route({
     *     "en": "/preview/tags/{id}",
     *     "fr": "/apercu/tags/{id}"
     * }, name="app_preview_tag_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Entity("tag", expr="repository.findWithItems(id)")
     *
     * @param Tag $tag
     * @return Response
     */
    public function show(Tag $tag) : Response
    {
        return $this->render('App/Tag/show.html.twig', [
            'tag' => $tag,
            'relatedTags' => $this->getDoctrine()->getRepository(Tag::class)->findRelatedTags($tag)
        ]);
    }

    /**
     * @Route({
     *     "en": "/tags/{id}/edit",
     *     "fr": "/tags/{id}/editer"
     * }, name="app_tag_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Tag $tag
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Tag $tag, TranslatorInterface $translator) : Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.tag_edited', ['%tag%' => '&nbsp;<strong>'.$tag->getLabel().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_tag_show', ['id' => $tag->getId()]);
        }


        return $this->render('App/Tag/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag,
        ]);
    }

    /**
     * @Route({
     *     "en": "/tags/{id}/delete",
     *     "fr": "/tags/{id}/supprimer"
     * }, name="app_tag_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Tag $tag
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Tag $tag, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($tag);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.tag_deleted', ['%tag%' => '&nbsp;<strong>'.$tag->getLabel().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_tag_index');
    }

    /**
     * @Route({
     *     "en": "/tags/autocomplete/{search}",
     *     "fr": "/tags/autocompletion/{search}"
     * }, name="app_tag_autocomplete", methods={"GET"})
     *
     * @param string $search
     * @return JsonResponse
     */
    public function autocomplete(string $search) : JsonResponse
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findLike($search);
        $data = [];
        foreach ($tags as $tag) {
            $data[] = $tag->getLabel();
        }

        return new JsonResponse($data);
    }

    /**
     * @Route({
     *     "en": "/tags/{id}/history",
     *     "fr": "/tags/{id}/historique"
     * }, name="app_tag_history", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @param Tag $tag
     * @return Response
     */
    public function history(Tag $tag) : Response
    {
        return $this->render('App/Tag/history.html.twig', [
            'tag' => $tag,
            'logs' => $this->getDoctrine()->getRepository(Log::class)->findBy([
                'objectId' => $tag->getId(),
                'objectClass' => $this->getDoctrine()->getManager()->getClassMetadata(\get_class($tag))->getName(),
            ], [
                'loggedAt' => 'DESC',
                'type' => 'DESC'
            ])
        ]);
    }
}
