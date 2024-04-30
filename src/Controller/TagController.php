<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Tag;
use App\Form\Type\Entity\TagType;
use App\Form\Type\Model\SearchTagType;
use App\Model\Search\SearchTag;
use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use App\Service\ContextHandler;
use App\Service\PaginatorFactory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TagController extends AbstractController
{
    #[Route(path: '/tags', name: 'app_tag_index', methods: ['GET'])]
    #[Route(path: '/user/{username}/tags', name: 'app_shared_tag_index', methods: ['GET'])]
    public function index(
        Request $request,
        PaginatorFactory $paginatorFactory,
        ContextHandler $contextHandler,
        TagRepository $tagRepository,
        ItemRepository $itemRepository
    ): Response {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $context = $contextHandler->getContext();
        $search = new SearchTag($request->query->getInt('page', 1), 15);
        $form = $this->createForm(SearchTagType::class, $search, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $itemsCount = $itemRepository->count([]);
        $tagsCount = $tagRepository->countForTagSearch($search, $context);
        $results = $tagRepository->findForTagSearch($search, $context, $itemsCount);

        if ($request->isXmlHttpRequest()) {
            return $this->render('App/Tag/_tags_table.html.twig', [
                'results' => $results,
                'paginator' => $paginatorFactory->generate($tagsCount),
            ]);
        }

        return $this->render('App/Tag/index.html.twig', [
            'results' => $results,
            'search' => $search,
            'tagsCount' => $tagsCount,
            'paginator' => $paginatorFactory->generate($tagsCount),
            'form' => $form,
        ]);
    }

    #[Route(path: '/tags/{id}', name: 'app_tag_show', methods: ['GET'])]
    #[Route(path: '/user/{username}/tags/{id}', name: 'app_shared_tag_show', methods: ['GET'])]
    public function show(
        #[MapEntity(expr: 'repository.findWithItems(id)')] Tag $tag,
        TagRepository $tagRepository
    ): Response {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        return $this->render('App/Tag/show.html.twig', [
            'tag' => $tag,
            'relatedTags' => $tagRepository->findRelatedTags($tag),
        ]);
    }

    #[Route(path: '/tags/{id}/edit', name: 'app_tag_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tag $tag, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.tag_edited', ['tag' => $tag->getLabel()]));

            return $this->redirectToRoute('app_tag_show', ['id' => $tag->getId()]);
        }

        return $this->render('App/Tag/edit.html.twig', [
            'form' => $form,
            'tag' => $tag,
        ]);
    }

    #[Route(path: '/tags/{id}/delete', name: 'app_tag_delete', methods: ['POST'])]
    public function delete(Request $request, Tag $tag, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $form = $this->createDeleteForm('app_tag_delete', $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($tag);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.tag_deleted', ['tag' => $tag->getLabel()]));
        }

        return $this->redirectToRoute('app_tag_index');
    }

    #[Route(path: '/tags/delete-unused-tags', name: 'app_tag_delete_unused_tags', methods: ['POST'])]
    public function deleteUnusedTags(Request $request, TranslatorInterface $translator, TagRepository $tagRepository, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $form = $this->createDeleteForm('app_tag_delete_unused_tags');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unusedTags = $tagRepository->getUnusedTags();
            foreach ($unusedTags as $tag) {
                $managerRegistry->getManager()->remove($tag);
            }

            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.unused_tags_deleted', ['count' => \count($unusedTags)]));
        }

        return $this->redirectToRoute('app_tag_index');
    }

    #[Route(path: '/tags/autocomplete/{search}', name: 'app_tag_autocomplete', methods: ['GET'])]
    public function autocomplete(string $search, TagRepository $tagRepository): JsonResponse
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $tags = $tagRepository->findLike($search);
        $data = [];
        foreach ($tags as $key => $tag) {
            $data[] = ['id' => $key, 'text' => $tag->getLabel()];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/tags/{tagId}/items/{itemId}', name: 'app_tag_item_show', methods: ['GET'])]
    #[Route(path: '/user/{username}/tags/{tagId}/items/{itemId}', name: 'app_shared_tag_item_show', methods: ['GET'])]
    public function item(
        #[MapEntity(expr: 'repository.findById(itemId)')] Item $item,
        #[MapEntity(expr: 'repository.find(tagId)')] Tag $tag,
        ItemRepository $itemRepository
    ): Response {
        $nextAndPrevious = $itemRepository->findNextAndPrevious($item, $tag);

        return $this->render('App/Tag/item.html.twig', [
            'item' => $item,
            'tag' => $tag,
            'previousItem' => $nextAndPrevious['previous'],
            'nextItem' => $nextAndPrevious['next'],
        ]);
    }
}
