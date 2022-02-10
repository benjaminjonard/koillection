<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Tag;
use App\Form\Type\Entity\TagType;
use App\Form\Type\Model\SearchTagType;
use App\Model\Search\SearchTag;
use App\Repository\ItemRepository;
use App\Repository\LogRepository;
use App\Repository\TagRepository;
use App\Service\ContextHandler;
use App\Service\PaginatorFactory;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TagController extends AbstractController
{
    #[Route(
        path: ['en' => '/tags', 'fr' => '/tags'],
        name: 'app_tag_index', methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/user/{username}/tags', 'fr' => '/utilisateur/{username}/tags'],
        name: 'app_shared_tag_index', methods: ['GET']
    )]
    public function index(Request $request, PaginatorFactory $paginatorFactory, ContextHandler $contextHandler,
                          int $paginationItemsPerPage, TagRepository $tagRepository
    ) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $context = $contextHandler->getContext();
        $search = new SearchTag($request->query->getInt('page', 1), $paginationItemsPerPage);
        $form = $this->createForm(SearchTagType::class, $search, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $itemsCount = $tagRepository->count([]);
        $tagsCount = $tagRepository->countForTagSearch($search, $context);
        $results = $tagRepository->findForTagSearch($search, $context, $itemsCount);

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
            'paginator' => $paginatorFactory->generate($tagsCount),
            'form' => $form->createView()
        ]);
    }

    #[Route(
        path: ['en' => '/tags/{id}', 'fr' => '/tags/{id}'],
        name: 'app_tag_show', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/user/{username}/tags/{id}', 'fr' => '/utilisateur/{username}/tags/{id}'],
        name: 'app_shared_tag_show', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Entity('tag', expr: 'repository.findWithItems(id)', class: Tag::class)]
    public function show(Tag $tag, TagRepository $tagRepository) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        return $this->render('App/Tag/show.html.twig', [
            'tag' => $tag,
            'relatedTags' => $tagRepository->findRelatedTags($tag)
        ]);
    }

    #[Route(
        path: ['en' => '/tags/{id}/edit', 'fr' => '/tags/{id}/editer'],
        name: 'app_tag_edit', requirements: ['id' => '%uuid_regex%'], methods: ['GET', 'POST']
    )]
    public function edit(Request $request, Tag $tag, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.tag_edited', ['%tag%' => '&nbsp;<strong>'.$tag->getLabel().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_tag_show', ['id' => $tag->getId()]);
        }


        return $this->render('App/Tag/edit.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag,
        ]);
    }

    #[Route(
        path: ['en' => '/tags/{id}/delete', 'fr' => '/tags/{id}/supprimer'],
        name: 'app_tag_delete', requirements: ['id' => '%uuid_regex%'], methods: ['POST']
    )]
    public function delete(Request $request, Tag $tag, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $form = $this->createDeleteForm('app_tag_delete', $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($tag);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.tag_deleted', ['%tag%' => '&nbsp;<strong>'.$tag->getLabel().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_tag_index');
    }

    #[Route(
        path: ['en' => '/tags/autocomplete/{search}', 'fr' => '/tags/autocompletion/{search}'],
        name: 'app_tag_autocomplete', methods: ['GET']
    )]
    public function autocomplete(string $search, TagRepository $tagRepository) : JsonResponse
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $tags = $tagRepository->findLike($search);
        $data = [];
        foreach ($tags as $tag) {
            $data[] = $tag->getLabel();
        }

        return new JsonResponse($data);
    }

    #[Route(
        path: ['en' => '/tags/{id}/history', 'fr' => '/tags/{id}/historique'],
        name: 'app_tag_history', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    public function history(Tag $tag, LogRepository $logRepository, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags', 'history']);

        return $this->render('App/Tag/history.html.twig', [
            'tag' => $tag,
            'logs' => $logRepository->findBy([
                'objectId' => $tag->getId(),
                'objectClass' => $managerRegistry->getManager()->getClassMetadata(\get_class($tag))->getName(),
            ], [
                'loggedAt' => 'DESC',
                'type' => 'DESC'
            ])
        ]);
    }

    #[Route(
        path: ['en' => '/tags/{tagId}/items/{itemId}', 'fr' => '/tags/{tagId}/objets/{itemId}'],
        name: 'app_tag_item_show', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/user/{username}/tags/{tagId}/items/{itemId', 'fr' => '/utilisateur/{username}tags/{tagId}/objets/{itemId}'],
        name: 'app_shared_tag_item_show', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Entity('item', expr: 'repository.findById(itemId)', class: Item::class)]
    #[Entity('tag', expr: 'repository.find(tagId)', class: Tag::class)]
    public function item(Item $item, Tag $tag, ItemRepository $itemRepository) : Response
    {
        $nextAndPrevious = $itemRepository->findNextAndPrevious($item, $tag);

        return $this->render('App/Tag/item.html.twig', [
            'item' => $item,
            'tag' => $tag,
            'previousItem' => $nextAndPrevious['previous'],
            'nextItem' => $nextAndPrevious['next']
        ]);
    }
}
